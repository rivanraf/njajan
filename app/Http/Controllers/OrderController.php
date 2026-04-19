<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Reservation;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function index(Request $request, $hash)
    {
        // 1. Cari meja berdasarkan hash dari URL
        if ($hash) {
            $table = Table::where('hash', $hash)->first();

            // Cegah akses jika meja dilarang (nonaktif)
            if ($table && $table->status === 'nonaktif') {
                return redirect('/')->with('error', 'Meja ini sedang tidak bisa digunakan.');
            }

            // Validasi Status Reservasi Harian (Pre-Check)
            if ($table) {
                $now = \Carbon\Carbon::now();
                
                $activeReservation = Reservation::where('table_id', $table->id)
                    ->where('reservation_date', $now->toDateString())
                    ->where('status', 'pending')
                    ->get()
                    ->filter(function ($reservation) use ($now) {
                        $resTime = \Carbon\Carbon::parse($reservation->reservation_time);
                        $startTime = $resTime->copy()->subMinutes(30);
                        $endTime = $resTime->copy()->addMinutes(30);
                        
                        return $now->between($startTime, $endTime);
                    })
                    ->first();

                if ($activeReservation) {
                    return redirect('/')->with('error', 'Meja ini sedang di-booking dan tidak bisa digunakan untuk pesanan mandiri saat ini.');
                }
            }
            
            // 2. Jika meja ditemukan, simpan id dan number ke dalam Session
            if ($table) {
                session([
                    'table_id' => $table->id,
                    'table_number' => $table->number,
                    'table_hash' => $hash
                ]);
                // Simpan ke Cookie sebagai backup (aktif 2 hari)
                cookie()->queue('table_hash', $hash, 60 * 24 * 2);
            }
        }

        // 3. Ambil semua data Menu yang dikelompokkan berdasarkan Category
        // Memanfaatkan relasi menus() di model Category
        $categories = Category::with('menus')->get();

        // 4. Return ke view order.index
        return view('order.index', compact('categories'));
    }

    public function search()
    {
        $menus = Menu::with('category')->get();
        return view('order.search', compact('menus'));
    }

    public function show($id)
    {
        $menu = Menu::with('category')->findOrFail($id);

        if(trim(strtolower($menu->status_stok)) == 'kosong') {
            return redirect()->back()->with('error', 'Menu ini sedang tidak tersedia.');
        }

        return view('order.show', compact('menu'));
    }

    public function addToCart(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $qty = (int) $request->input('qty', 1);
        $notes = $request->input('notes', '');
        $variant = $request->input('variant', '');
        
        // Buat logic hash key biar menu dengan 'Hot' bisa terpisah di keranjang dgn yang 'Ice'
        $cartKey = $id . ($variant ? '_' . $variant : '') . ($notes ? '_' . md5($notes) : '');
        
        $cart = session()->get('cart', []);
        
        if(isset($cart[$cartKey])) {
            $cart[$cartKey]['qty'] += $qty;
        } else {
            $cart[$cartKey] = [
                "menu_id" => $menu->id,
                "name" => $menu->name,
                "price" => $menu->price,
                "qty" => $qty,
                "variant" => $variant,
                "notes" => $notes,
                "image" => $menu->image ? asset('storage/' . $menu->image) : null
            ];
        }
        
        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'berhasil ditambahkan ke keranjang!');
    }

    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            $qty = (int) $request->input('qty');
            if ($qty > 0) {
                $cart[$id]['qty'] = $qty;
            } else {
                unset($cart[$id]);
            }
            session()->put('cart', $cart);
        }
        
        return redirect()->back();    
    }

    public function removeCart($id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        
        return redirect()->back();
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        
        $deviceId = $request->cookie('device_id') ?? ($_COOKIE['device_id'] ?? null);
        $history = [];
        if ($deviceId) {
            $history = Order::where('device_id', $deviceId)->orderBy('created_at', 'desc')->get();
        }

        return view('order.checkout', compact('cart', 'history'));
    }

    public function payment()
    {
        $cart = session()->get('cart', []);
        
        if(empty($cart)){
            return redirect('/')->with('error', 'Keranjang masih kosong!');
        }

        $totalPrice = 0;
        foreach($cart as $item) {
            $totalPrice += $item['price'] * $item['qty'];
        }

        $tableNumber = session('table_number', '-');

        return view('order.payment', compact('cart', 'totalPrice', 'tableNumber'));
    }

    public function processCheckout(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'customer_name' => 'required|string|max:255',
        'payment_method' => 'required|string|in:QRIS,Cashier',
    ]);
    
    $cart = session()->get('cart', []);
    if(empty($cart)){
        return redirect('/')->with('error', 'Keranjang masih kosong!');
    }

    $total_price = 0;
    foreach($cart as $item) {
        $total_price += $item['price'] * $item['qty'];
    }

    // 2. Mapping Enum
    $paymentTypeMapped = ($request->payment_method === 'Cashier') ? 'cash' : 'qris';

    // 3. Simpan data ke tabel orders
    $order = new Order();
    $order->table_id = session('table_id');
    $order->customer_name = $request->customer_name;
    $order->total_price = $total_price;
    $order->payment_type = $paymentTypeMapped;
    $order->device_id = $request->cookie('device_id') ?? ($_COOKIE['device_id'] ?? null);
    $order->save();

    // 4. Simpan ke order_details (BAGIAN YANG DIPERBAIKI)
    foreach($cart as $item) {
        $detail = new OrderDetail();
        $detail->order_id = $order->id;
        $detail->menu_id = $item['menu_id'];
        $detail->qty = $item['qty'];
        $detail->subtotal = $item['price'] * $item['qty'];
        
        // AMBIL DATA DARI SESSION DAN SIMPAN KE DATABASE
        $detail->variant = $item['variant'] ?? null; 
        $detail->notes = $item['notes'] ?? null;
        
        $detail->save();
    }

    // --- LOGIKA MIDTRANS MULAI DI SINI ---
    
    if ($request->payment_method === 'QRIS') {
        // Konfigurasi Kunci Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        \Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        $params = [
            'transaction_details' => [
                'order_id' => 'NJN-' . $order->id . '-' . time(), // ID unik untuk Midtrans
                'gross_amount' => (int)$total_price,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Simpan snap_token ke database agar bisa dipanggil lagi jika gagal bayar
            $order->snap_token = $snapToken;
            $order->save();

            // Kosongkan keranjang sebelum buka payment popup
            session()->forget('cart');

            // Kita kirim data ke view khusus untuk memunculkan Popup Midtrans
            return view('order.payment_snap', compact('snapToken', 'order'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke Midtrans: ' . $e->getMessage());
        }
    }

    // --- LOGIKA CASHIER (Tunggu Bayar di Kasir) ---
    $order->payment_status = 'pending'; // Tetap pending sampai kasir konfirmasi
    $order->save();

    session()->forget('cart');
    return redirect()->route('order.pending-cash', $order->id);
    }

    public function paymentSuccess($id)
    {
        // 1. Cari order berdasarkan ID, lengkap dengan relasi orderDetails
        $order = Order::with('orderDetails')->find($id);

        // 2. Pengaman: jika order tidak ditemukan, redirect ke home
        if (!$order) {
            return redirect('/')->with('error', 'Pesanan tidak ditemukan.');
        }

        // 3. Bersihkan session cart (jaga-jaga jika belum terhapus)
        session()->forget('cart');

        // 4. Kirim data order ke view
        return view('order.success', compact('order'));
    }

    public function pendingCash($id)
    {
        $order = Order::findOrFail($id);

        // Jika sudah paid (kasir konfirmasi), arahkan ke halaman sukses
        if ($order->payment_status === 'paid') {
            return redirect()->route('order.success', $order->id);
        }

        return view('order.pending-cash', compact('order'));
    }

    public function handleNotification(Request $request)
    {
        // 1. Ambil raw payload dari Midtrans
        $payload = $request->getContent();
        $notification = json_decode($payload, true);

        if (!$notification) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // 2. Signature Key Verification
        // Format: SHA512(order_id + status_code + gross_amount + server_key)
        $orderId       = $notification['order_id'] ?? '';
        $statusCode    = $notification['status_code'] ?? '';
        $grossAmount   = $notification['gross_amount'] ?? '';
        $serverKey     = env('MIDTRANS_SERVER_KEY');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $incomingSignature = $notification['signature_key'] ?? '';

        if ($expectedSignature !== $incomingSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // CEK APAKAH INI NOTIFIKASI RESERVASI ATAU ORDER BIASA
        if (\Illuminate\Support\Str::startsWith($orderId, 'BOOKING-')) {
            $reservation = \App\Models\Reservation::where('booking_code', $orderId)->first();
            if (!$reservation) {
                return response()->json(['message' => 'Reservasi tidak ditemukan'], 404);
            }

            $transactionStatus = $notification['transaction_status'] ?? '';
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $reservation->payment_status = 'paid';
                // Anda juga bisa menyesuaikan status kedatangan jika perlu, misal: status = 'confirmed'
                $reservation->status = 'confirmed'; 
            } elseif ($transactionStatus === 'expire') {
                $reservation->payment_status = 'expired';
                $reservation->status = 'cancelled';
            } elseif ($transactionStatus === 'cancel') {
                $reservation->payment_status = 'cancelled';
                $reservation->status = 'cancelled';
            }
            
            $reservation->save();
            return response()->json(['message' => 'Notifikasi reservasi diproses'], 200);
        }

        // 3. Parse Order ID dari format "NJN-{id}-{timestamp}"
        // Contoh: "NJN-20-1712345678" -> ambil angka ke-2 (index 1)
        $parts    = explode('-', $orderId);
        $localId  = $parts[1] ?? null;

        if (!$localId) {
            return response()->json(['message' => 'Order ID tidak valid'], 422);
        }

        $order = Order::find($localId);

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        // 4. Update payment_status berdasarkan status Midtrans
        $transactionStatus = $notification['transaction_status'] ?? '';

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            $order->payment_status = 'paid';
        } elseif ($transactionStatus === 'expire') {
            $order->payment_status = 'expired';
        } elseif ($transactionStatus === 'cancel') {
            $order->payment_status = 'cancelled';
        }
        // Status 'pending' → tidak perlu update, biarkan default

        $order->save();

        return response()->json(['message' => 'Notifikasi berhasil diproses'], 200);
    }

    public function track($id)
    {
        // Cari order berdasarkan ID, sertakan relasi meja (table)
        $order = Order::with('table')->findOrFail($id);

        // Kirim data ke halaman track
        return view('order.track', compact('order'));
    }
}
