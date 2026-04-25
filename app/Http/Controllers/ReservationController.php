<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Carbon\Carbon; // Pastikan Carbon di-import

class ReservationController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('welcome', compact('tables'));
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
        
        // Gabungkan tanggal dan jam input menjadi satu objek Carbon
        $inputDateTime = Carbon::parse($request->reservation_date . ' ' . $request->reservation_time);
        
        // Cek apakah waktu yang dipilih sudah lewat dari waktu server saat ini
        if ($inputDateTime->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu reservasi tidak valid. Jam ' . $request->reservation_time . ' untuk hari ini sudah terlewati.'
            ], 400);
        }

        // --- MULAI LOGIKA ANTI-BUG (PENCEGAHAN BENTROK) ---
        
        $isBooked = Reservation::where('table_id', $request->table_id)
            ->where('reservation_date', $request->reservation_date)
            ->where('reservation_time', $request->reservation_time)
            ->whereIn('status', ['pending', 'confirmed']) 
            ->exists();

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, Meja ' . $request->table_id . ' sudah dipesan untuk jadwal tersebut. Silakan pilih waktu atau meja lain.'
            ], 400);
        }

        // --- SELESAI LOGIKA ANTI-BUG ---

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
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        \Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS', true);

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
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
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
            return redirect()->route('reserve.pending', $id);
        }
        return view('reservation.success', compact('reservation'));
    }

    public function pending($id)
    {
        $reservation = Reservation::where('booking_code', $id)->firstOrFail();
        if (in_array($reservation->payment_status, ['paid', 'settlement'])) {
            return redirect()->route('reserve.success', $id);
        }
        return view('reservation.pending', compact('reservation'));
    }

    public function adminIndex()
    {
        $reservations = Reservation::with('table')->orderBy('reservation_date', 'asc')->get();
        return view('admin.reservations.index', compact('reservations'));
    }

    public function updateStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'status' => $request->status
        ]);
        return redirect()->back()->with('success', 'Status reservasi meja ' . $reservation->booking_code . ' berhasil diperbarui.');
    }

    public function edit($id)
    {
        $reservation = Reservation::findOrFail($id);
        $tables = \App\Models\Table::all(); 
        return view('admin.reservations.edit', compact('reservation', 'tables'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required',
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update($request->all());

        return redirect()->route('admin.reservations.index')
            ->with('success', 'Data reservasi ' . $reservation->booking_code . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return redirect()->back()->with('success', 'Data reservasi telah dihapus permanen.');
    }
}