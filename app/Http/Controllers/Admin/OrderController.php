<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function print($id)
    {
        // Mengambil data order beserta relasi meja dan detail menu
        $order = \App\Models\Order::with(['table', 'details.menu'])->findOrFail($id);

        return view('admin.orders.print', compact('order'));
    }
    
    public function index()
    {
        // Tampilkan: (1) semua pesanan yang sudah PAID, ATAU
        //            (2) pesanan CASH yang masih PENDING (belum dibayar ke kasir)
        // Keduanya harus belum SELESAI.
        $orders = Order::with('details.menu')
                    ->where('order_status', '!=', 'completed')
                    ->where(function ($query) {
                        $query->where('payment_status', 'paid')
                              ->orWhere(function ($q) {
                                  $q->where('payment_type', 'cash')
                                    ->where('payment_status', 'pending');
                              });
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $newStatus = $request->status; // 'processing' atau 'completed'

        // 1. Set order_status
        $order->order_status = $newStatus;

        // 2. OTOMATIS: Jika pesanan adalah Cash & kasir menekan Terima/Proses,
        //    anggap uang tunai sudah diterima → set payment_status ke 'paid'.
        //    Logika QRIS tidak disentuh (statusnya diatur oleh callback Midtrans).
        if ($order->payment_type === 'cash' && $order->payment_status === 'pending') {
            $order->payment_status = 'paid';
        }

        $order->save();

        // 3. LOGIKA STRATEGIS: Jika pesanan Selesai (Sudah Bayar & Pulang)
        if ($newStatus === 'completed') {
            $table = \App\Models\Table::find($order->table_id);
            if ($table) {
                // Meja kembali kosong dan siap di-scan pelanggan baru
                $table->update(['status' => 'available']);
            }
        }

        return redirect()->back()->with('success', 'Pesanan #' . $order->id . ' berhasil diupdate!');
    }
}