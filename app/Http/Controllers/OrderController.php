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
        
        if (!session()->has('table_id')) {
            return redirect('/')->with('error', 'Sesi meja Anda tidak valid atau telah berakhir. Silakan scan ulang QR Code di meja Anda.');
        }
        
        $qty = (int) $request->input('qty', 1);
        $notes = $request->input('notes', '');
        $variant = $request->input('variant', '');
        
        $cartKey = $id . ($variant ? '_' . $variant : '') . ($notes ? '_' . md5($notes) : '');
        
        $sessionKey = 'cart_table_' . session('table_id');
        $cart = session()->get($sessionKey, []);
        
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
        
        session()->put($sessionKey, $cart);
        
        return redirect()->back()->with('success', 'berhasil ditambahkan ke keranjang!');
    }

    public function updateCart(Request $request, $id)
    {
        $sessionKey = 'cart_table_' . session('table_id');
        $cart = session()->get($sessionKey, []);
        
        if (isset($cart[$id])) {
            $qty = (int) $request->input('qty');
            if ($qty > 0) {
                $cart[$id]['qty'] = $qty;
            } else {
                unset($cart[$id]);
            }
            session()->put($sessionKey, $cart);
        }
        
        return redirect()->back();    
    }

    public function removeCart($id)
    {
        $sessionKey = 'cart_table_' . session('table_id');
        $cart = session()->get($sessionKey, []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put($sessionKey, $cart);
        }
        
        return redirect()->back();
    }

    public function checkout(Request $request)
    {
        if (!session()->has('table_id')) {
            return redirect('/')->with('error', 'Sesi meja Anda tidak valid atau telah berakhir. Silakan scan ulang QR Code di meja Anda.');
        }

        $sessionKey = 'cart_table_' . session('table_id');
        $cart = session()->get($sessionKey, []);
        
        $deviceId = $request->cookie('device_id') ?? ($_COOKIE['device_id'] ?? null);
        $history = [];
        if ($deviceId) {
            $history = Order::where('device_id', $deviceId)->orderBy('created_at', 'desc')->get();
        }

        return view('order.checkout', compact('cart', 'history'));
    }

    public function payment()
    {
        if (!session()->has('table_id')) {
            return redirect('/')->with('error', 'Sesi meja Anda tidak valid atau telah berakhir. Silakan scan ulang QR Code di meja Anda.');
        }

        $sessionKey = 'cart_table_' . session('table_id');
        $cart = session()->get($sessionKey, []);
        
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
        $customerName = $request->input('customer_name');
        if (empty($customerName) || $customerName === '-') {
            $customerName = session('customer_name');
        }
        $request->merge(['customer_name' => $customerName]);

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'payment_method' => 'required|string|in:QRIS,Cashier',
        ]);
        
        if (!session()->has('table_id')) {
            return redirect('/')->with('error', 'Sesi meja Anda tidak valid atau telah berakhir. Silakan scan ulang QR Code di meja Anda.');
        }
        
        $sessionKey = 'cart_table_' . session('table_id');
        $cart = session()->get($sessionKey, []);
        if(empty($cart)){
            return redirect('/')->with('error', 'Keranjang masih kosong!');
        }

        $total_price = 0;
        foreach($cart as $item) {
            $total_price += $item['price'] * $item['qty'];
        }

        $paymentTypeMapped = ($request->payment_method === 'Cashier') ? 'cash' : 'qris';

        $order = new Order();
        $order->table_id = session('table_id');
        $order->customer_name = $request->customer_name;
        $order->total_price = $total_price;
        $order->payment_type = $paymentTypeMapped;
        $order->device_id = $request->cookie('device_id') ?? ($_COOKIE['device_id'] ?? null);
        $order->save();

        foreach($cart as $item) {
            $detail = new OrderDetail();
            $detail->order_id = $order->id;
            $detail->menu_id = $item['menu_id'];
            $detail->qty = $item['qty'];
            $detail->subtotal = $item['price'] * $item['qty'];
            $detail->variant = $item['variant'] ?? null; 
            $detail->notes = $item['notes'] ?? null;
            $detail->save();
        }

        if ($request->payment_method === 'QRIS') {
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            \Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
            \Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS', true);

            $params = [
                'transaction_details' => [
                    'order_id' => 'NJN-' . $order->id . '-' . time(),
                    'gross_amount' => (int)$total_price,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                ],
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->snap_token = $snapToken;
                $order->save();

                $sessionKey = 'cart_table_' . session('table_id');
                session()->forget($sessionKey);
                return view('order.payment_snap', compact('snapToken', 'order'));
                
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal terhubung ke Midtrans: ' . $e->getMessage());
            }
        }

        $order->payment_status = 'pending'; 
        $order->save();

        $sessionKey = 'cart_table_' . session('table_id');
        session()->forget($sessionKey);
        return redirect()->route('order.pending-cash', $order->id);
    }

    public function paymentSuccess($id)
    {
        $order = Order::with('orderDetails')->find($id);
        if (!$order) {
            return redirect('/')->with('error', 'Pesanan tidak ditemukan.');
        }
        $sessionKey = 'cart_table_' . session('table_id');
        session()->forget($sessionKey);
        return view('order.success', compact('order'));
    }

    public function paymentFinish(Request $request)
    {
        $orderIdParam = $request->query('order_id');
        $transactionStatus = $request->query('transaction_status');
        
        if (!$orderIdParam) {
            return redirect('/')->with('error', 'Data pesanan tidak valid dari Midtrans.');
        }

        // 1. Cek apakah ini reservasi meja (dimulai dengan BOOKING-)
        if (\Illuminate\Support\Str::startsWith($orderIdParam, 'BOOKING-')) {
            $reservation = \App\Models\Reservation::where('booking_code', $orderIdParam)->first();
            if (!$reservation) {
                return redirect('/')->with('error', 'Data reservasi tidak ditemukan.');
            }
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                return redirect()->route('reserve.success', $reservation->booking_code);
            } else {
                return redirect()->route('reserve.pending', $reservation->booking_code);
            }
        }

        // 2. Jika pesanan biasa (format: NJN-{id}-{timestamp})
        $parts = explode('-', $orderIdParam);
        $localId = $parts[1] ?? null;

        if (!$localId) {
            return redirect('/')->with('error', 'Format Order ID tidak dikenali.');
        }

        $order = Order::find($localId);
        
        if (!$order) {
            return redirect('/')->with('error', 'Pesanan tidak ditemukan.');
        }

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            return redirect()->route('order.success', $order->id);
        } else {
            return redirect()->route('order.track', $order->id);
        }
    }

    public function pendingCash($id)
    {
        $order = Order::with('table')->findOrFail($id);

        // EARLY RETURN: Jika sudah expired/cancelled
        if ($order->order_status === 'cancelled' || $order->payment_status === 'expired') {
            return view('order.expired', compact('order')); 
        }

        // Jika sudah paid (kasir konfirmasi), arahkan ke halaman sukses
        if ($order->payment_status === 'paid') {
            return redirect()->route('order.success', $order->id);
        }

        // --- START LOGIKA ORDER EXPIRATION ---
        if ($order->order_status === 'pending') {
            $now = \Carbon\Carbon::now();
            $createdAt = \Carbon\Carbon::parse($order->created_at);
            
            $expireMinutes = ($order->payment_type === 'cash') ? 5 : 15;
            $expireTime = $createdAt->copy()->addMinutes($expireMinutes);

            if ($now->greaterThan($expireTime)) {
                // FIXED: Menggunakan 'cancelled' (double L) sesuai ENUM database
                $order->order_status = 'cancelled'; 
                $order->payment_status = 'expired'; 
                $order->save();
                $order->refresh();

                if ($order->table_id) {
                    $table = \App\Models\Table::find($order->table_id);
                    if ($table) {
                        $table->status = 'available';
                        $table->save();
                    }
                }

                // REDIRECT EXECUTION: Segera tampilkan view expired agar tidak bablas
                return view('order.expired', compact('order'));
            }
        }
        // --- END LOGIKA ORDER EXPIRATION ---

        return view('order.pending-cash', compact('order'));
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload, true);

        if (!$notification) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $orderId       = $notification['order_id'] ?? '';
        $statusCode    = $notification['status_code'] ?? '';
        $grossAmount   = $notification['gross_amount'] ?? '';
        $serverKey     = env('MIDTRANS_SERVER_KEY');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $incomingSignature = $notification['signature_key'] ?? '';

        if ($expectedSignature !== $incomingSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        if (\Illuminate\Support\Str::startsWith($orderId, 'BOOKING-')) {
            $reservation = \App\Models\Reservation::where('booking_code', $orderId)->first();
            if (!$reservation) {
                return response()->json(['message' => 'Reservasi tidak ditemukan'], 404);
            }

            $transactionStatus = $notification['transaction_status'] ?? '';
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $reservation->payment_status = 'paid';
                $reservation->status = 'confirmed'; 
            } elseif ($transactionStatus === 'expire') {
                $reservation->payment_status = 'expired';
                $reservation->status = 'cancelled';
            } elseif ($transactionStatus === 'cancel') {
                $reservation->payment_status = 'expired';
                $reservation->status = 'cancelled';
            }
            
            $reservation->save();
            return response()->json(['message' => 'Notifikasi reservasi diproses'], 200);
        }

        $parts    = explode('-', $orderId);
        $localId  = $parts[1] ?? null;

        if (!$localId) {
            return response()->json(['message' => 'Order ID tidak valid'], 422);
        }

        $order = Order::find($localId);
        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $transactionStatus = $notification['transaction_status'] ?? '';

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            $order->payment_status = 'paid';
        } elseif ($transactionStatus === 'expire') {
            $order->payment_status = 'expired';
            $order->order_status = 'cancelled'; // FIXED: 'cancelled'
        } elseif ($transactionStatus === 'cancel') {
            $order->payment_status = 'expired';
            $order->order_status = 'cancelled'; // FIXED: 'cancelled'
        }

        $order->save();
        return response()->json(['message' => 'Notifikasi berhasil diproses'], 200);
    }

    public function track($id)
    {
        $order = Order::with('table')->findOrFail($id);

        // EARLY RETURN: Jika status sudah cancelled/expired, langsung return expired view
        if ($order->order_status === 'cancelled' || $order->payment_status === 'expired') {
            return view('order.expired', compact('order')); 
        }

        if ($order->order_status === 'pending') {
            $now = \Carbon\Carbon::now();
            $createdAt = \Carbon\Carbon::parse($order->created_at);
            
            $expireMinutes = ($order->payment_type === 'cash') ? 5 : 15;
            $expireTime = $createdAt->copy()->addMinutes($expireMinutes);

            if ($now->greaterThan($expireTime)) {
                // FIXED: 'cancelled' (double L)
                $order->order_status = 'cancelled'; 
                $order->payment_status = 'expired';
                $order->save();
                $order->refresh();

                if ($order->table_id) {
                    $table = \App\Models\Table::find($order->table_id);
                    if ($table) {
                        $table->status = 'available';
                        $table->save();
                    }
                }

                // REDIRECT EXECUTION: Segera tampilkan view expired agar tidak bablas ke bawah
                return view('order.expired', compact('order'));
            }
        }

        return view('order.track', compact('order'));
    }
}