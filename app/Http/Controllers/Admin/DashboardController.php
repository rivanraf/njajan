<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input bulan dari filter (Default: bulan berjalan saat ini jika belum dipilih)
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $currentYear = Carbon::now()->year;

        // 2. Hitung Omzet Bulanan Terpilih (Status: Processing atau Completed & Wajib Paid)
        $todayRevenue = Order::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $selectedMonth)
            ->whereIn('order_status', ['processing', 'completed'])
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // 3. Jumlah Total Seluruh Pesanan Masuk pada Bulan Terpilih
        $totalOrdersToday = Order::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $selectedMonth)
            ->count();

        // 4. Jumlah Pesanan yang Masih Tertahan Pending pada Bulan Terpilih
        $pendingOrders = Order::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $selectedMonth)
            ->where('order_status', 'pending')
            ->count();

        // 5. Jumlah Pesanan Berhasil / Selesai pada Bulan Terpilih
        $completedOrders = Order::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $selectedMonth)
            ->where('order_status', 'completed')
            ->count();

        // ==============================================================================================
        // FIX BUG REALTIME & DYNAMIC MONTH: Menyaring Produk Terlaris Berdasarkan Bulan Terpilih
        // ==============================================================================================
        $topMenus = \App\Models\OrderDetail::select('order_details.menu_id', DB::raw('SUM(order_details.qty) as total_qty'))
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereYear('orders.created_at', $currentYear)
            ->whereMonth('orders.created_at', $selectedMonth)
            ->where('orders.payment_status', 'paid')                     // Harus sudah sukses dibayar
            ->whereIn('orders.order_status', ['processing', 'completed']) // Mengabaikan order cancelled/expired
            ->with('menu')
            ->groupBy('order_details.menu_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
        // ==============================================================================================

        // 6. Menyediakan Array Daftar Bulan untuk Keperluan Looping Komponen Elemen Select di Blade View
        $monthsList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Variabel penamaan dikembalikan utuh ('todayRevenue', 'totalOrdersToday', dll) agar tidak memecah variabel di blade lama
        return view('dashboard', compact(
            'todayRevenue', 
            'totalOrdersToday', 
            'pendingOrders', 
            'completedOrders',
            'topMenus',
            'selectedMonth',
            'monthsList'
        ));
    }
}