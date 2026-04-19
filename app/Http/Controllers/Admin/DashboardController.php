<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Omzet Hari Ini (Status: Processing atau Completed)
        $todayRevenue = Order::whereDate('created_at', Carbon::today())
            ->whereIn('order_status', ['processing', 'completed'])
            ->sum('total_price');

        // 2. Jumlah Pesanan Masuk Hari Ini
        $totalOrdersToday = Order::whereDate('created_at', Carbon::today())->count();

        // 3. Pesanan yang Masih Pending (Butuh Perhatian Kasir)
        $pendingOrders = Order::where('order_status', 'pending')->count();

        // 4. Pesanan Selesai Hari Ini
        $completedOrders = Order::whereDate('created_at', Carbon::today())
            ->where('order_status', 'completed')
            ->count();

        $topMenus = \App\Models\OrderDetail::select('menu_id', DB::raw('SUM(qty) as total_qty'))
            ->with('menu')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todayRevenue', 
            'totalOrdersToday', 
            'pendingOrders', 
            'completedOrders',
            'topMenus'
        ));
    }
}