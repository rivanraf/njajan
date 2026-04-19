<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table; // Kita butuh ini untuk ambil daftar meja
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    /**
     * Menampilkan Form Reservasi di Landing Page
     */
    public function index()
    {
        // Ambil semua meja agar user bisa memilih meja mana yang mau di-booking
        $tables = Table::all();
        return view('welcome', compact('tables'));
    }

    /**
     * Menyimpan Data Reservasi
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|numeric',
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required',
            'guests' => 'required|integer|min:1',
        ]);

        // 2. Logika Generate Kode Booking Unik (Contoh: NJN-ABC12)
        $bookingCode = 'NJN-' . strtoupper(Str::random(5));

        // 3. Simpan ke Database
        Reservation::create([
            'booking_code' => $bookingCode,
            'name' => $request->name,
            'whatsapp' => $request->whatsapp,
            'table_id' => $request->table_id,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'guests' => $request->guests,
            'status' => 'pending', // Status awal selalu pending
        ]);

        // 4. Kirim kode booking ke halaman sukses via Session
        return redirect()->back()->with('success_booking', $bookingCode);
    }

    /**
     * Menampilkan daftar reservasi di Dashboard Admin/Kasir
     */
    public function adminIndex()
    {
        // Reservation::with('table') agar tidak boros query (Eager Loading)
        $reservations = Reservation::with('table')->orderBy('reservation_date', 'asc')->get();
        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Update status reservasi (Pending -> Confirmed/Check-in -> Cancelled)
     */
    public function updateStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status reservasi meja ' . $reservation->booking_code . ' berhasil diperbarui.');
    }
}