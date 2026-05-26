<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index() {
        // 1. Ambil parameter tanggal dari URL, jika tidak ada gunakan tanggal hari ini
        $selectedDate = request()->query('date', Carbon::now()->format('Y-m-d'));
        
        // 2. Ambil parameter jam dari URL. 
        $selectedTime = request()->query('time', '10:00'); 

        // Konversi ke format penunjuk waktu yang bersih untuk komparasi database
        $startTime = Carbon::parse($selectedTime)->format('H:i:s');
        $endTime = Carbon::parse($selectedTime)->addHour()->format('H:i:s');

        // 3. AMBIL SEMUA ID MEJA YANG TERKUNCI DALAM RENTANG DURASI 1 JAM
        $bookedTableIds = Reservation::where('reservation_date', $selectedDate)
            ->whereIn('status', ['pending', 'confirmed', 'arrived'])
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('reservation_time', '>=', $startTime)
                      ->where('reservation_time', '<', $endTime);
                })
                ->orWhere(function($q) use ($startTime) {
                    $q->where('reservation_time', '<=', $startTime)
                      ->whereRaw('ADDTIME(reservation_time, "01:00:00") > ?', [$startTime]);
                });
            })
            ->pluck('table_id')
            ->toArray();

        // 4. Ambil semua data meja
        $tables = Table::all();

        // 5. Kirim data ke view welcome
        return view('welcome', compact('tables', 'bookedTableIds'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|numeric',
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required',
            'guests' => 'required|integer|min:1',
        ]);

        // --- TAMBALAN LOGIKA: VALIDASI WAKTU REAL-TIME ---
        $inputDateTime = Carbon::parse($request->reservation_date . ' ' . $request->reservation_time);
        
        if ($inputDateTime->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu reservasi tidak valid. Jam ' . $request->reservation_time . ' untuk hari ini sudah terlewati.'
            ], 400);
        }

        // --- ANTI-BUG: VALIDASI BLOKIR DATA BENTROK RENTANG 1 JAM ---
        $startTime = Carbon::parse($request->reservation_time)->format('H:i:s');
        $endTime = Carbon::parse($request->reservation_time)->addHour()->format('H:i:s');

        $isBooked = Reservation::where('table_id', $request->table_id)
            ->where('reservation_date', $request->reservation_date)
            ->whereIn('status', ['pending', 'confirmed', 'arrived']) 
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('reservation_time', '>=', $startTime)
                      ->where('reservation_time', '<', $endTime);
                })
                ->orWhere(function($q) use ($startTime) {
                    $q->where('reservation_time', '<=', $startTime)
                      ->whereRaw('ADDTIME(reservation_time, "01:00:00") > ?', [$startTime]);
                });
            })
            ->exists();

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, meja tersebut sudah dipesan atau masuk dalam rentang blokir 1 jam dari reservasi lain.'
            ], 400);
        }

        // 2. Logika Generate Kode Booking Unik
        $bookingCode = 'BOOKING-' . strtoupper(Str::random(5));

        // 3. Simpan ke Database
        $reservation = Reservation::create([
            'booking_code' => $bookingCode,
            'name' => $request->name,
            'whatsapp' => $request->whatsapp,
            'table_id' => $request->table_id,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'guests' => $request->guests,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        // 4. Konfigurasi Midtrans & Request Snap Token
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        $params = [
            'transaction_details' => [
                'order_id' => $bookingCode,
                'gross_amount' => 20000,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'phone' => $request->whatsapp,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            
            $reservation->snap_token = $snapToken;
            $reservation->save();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'booking_code' => $bookingCode
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success($id)
    {
        $reservation = Reservation::with('table')->where('booking_code', $id)->firstOrFail();
        
        if (!in_array($reservation->payment_status, ['paid', 'settlement'])) {
            try {
                Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                $status = \Midtrans\Transaction::status($reservation->booking_code);
                
                if (in_array($status->transaction_status, ['settlement', 'capture'])) {
                    $reservation->payment_status = 'paid';
                    $reservation->status = 'confirmed';
                    $reservation->save();
                } else {
                    return redirect()->route('reserve.pending', $id);
                }
            } catch (\Exception $e) {
                return redirect()->route('reserve.pending', $id);
            }
        }
        
        return view('reservation.success', compact('reservation'));
    }

    public function pending($id)
    {
        $reservation = Reservation::where('booking_code', $id)->firstOrFail();

        // Mengunci aturan expired transaksi Midtrans gantung (15 menit)
        if ($reservation->status === 'pending' && Carbon::parse($reservation->created_at)->addMinutes(15)->isPast()) {
            $reservation->update([
                'status' => 'cancelled',
                'payment_status' => 'expire'
            ]);
            
            return redirect('/')->with('booking_timeout', 'Time out, bestie! You missed the payment window, so we had to release your table. Better luck next time!');
        }

        if (in_array($reservation->payment_status, ['paid', 'settlement'])) {
            return redirect()->route('reserve.success', $id);
        }

        return view('reservation.pending', compact('reservation'));
    }

    public function adminIndex()
    {
        // Otomatis bersihkan data menggantung di database yang belum bayar via Midtrans selama 15 menit
        Reservation::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subMinutes(15))
            ->update([
                'status' => 'cancelled',
                'payment_status' => 'expire'
            ]);

        $reservations = Reservation::with('table')->orderBy('reservation_date', 'asc')->get();
        return view('admin.reservations.index', compact('reservations'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,arrived,cancelled'
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'status' => $request->status
        ]);

        // FIX: Logika penguncian fisik tabel dicabut total agar tidak menginterupsi alur scan QR umum.
        return redirect()->back()->with('success', 'Status reservasi meja ' . $reservation->booking_code . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return redirect()->back()->with('success', 'Data reservasi telah dihapus permanen.');
    }
}