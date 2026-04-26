<?php
// app/Http/Controllers/OrderController.php
namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout()
    {
        $cartItems = CartItem::with('menuItem')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong!');
        }

        $total = $cartItems->sum(fn($item) => $item->subtotal);

        return view('pages.checkout', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris',
            'notes'          => 'nullable|string|max:255',
        ]);

        $cartItems = CartItem::with('menuItem')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong!');
        }

        DB::transaction(function () use ($request, $cartItems) {
            $subtotal = $cartItems->sum(fn($item) => $item->subtotal);
            $tax      = 0; // No tax for school canteen
            $total    = $subtotal + $tax;

            $order = Order::create([
                'user_id'        => auth()->id(),
                'status'         => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'total'          => $total,
                'notes'          => $request->notes,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $cartItem->menu_item_id,
                    'item_name'    => $cartItem->menuItem->name,
                    'item_price'   => $cartItem->menuItem->price,
                    'quantity'     => $cartItem->quantity,
                    'subtotal'     => $cartItem->subtotal,
                    'notes'        => $cartItem->notes,
                ]);

                // Decrement stock
                $cartItem->menuItem->decrement('stock', $cartItem->quantity);
            }

            // Clear cart
            CartItem::where('user_id', auth()->id())->delete();

            session(['last_order_id' => $order->id]);
        });

        $orderId = session('last_order_id');

        return redirect()->route('orders.show', $orderId);
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load('items', 'user', 'kasir');

        return view('pages.order-detail', compact('order'));
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.my-orders', compact('orders'));
    }

    // --- KASIR / ADMIN methods ---

    public function kasirIndex()
    {
        $orders = Order::with('user', 'items')
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->orderByDesc('created_at')
            ->get();

        return view('pages.cashier.orders', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,ready,completed,cancelled']);

        $update = ['status' => $request->status];

        if ($request->status === 'completed') {
            $update['completed_at'] = now();
            $update['kasir_id']     = auth()->id();
        }

        $order->update($update);

        return response()->json(['success' => true, 'status' => $order->status_label]);
    }

    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $change = $request->amount_paid - $order->total;

        if ($change < 0) {
            return response()->json(['error' => 'Uang tidak cukup'], 422);
        }

        $order->update([
            'payment_status' => 'paid',
            'amount_paid'    => $request->amount_paid,
            'change_amount'  => $change,
            'paid_at'        => now(),
            'kasir_id'       => auth()->id(),
            'status'         => 'processing',
        ]);

        return response()->json([
            'success' => true,
            'change'  => 'Rp ' . number_format($change, 0, ',', '.'),
        ]);
    }

    public function confirmQrisPayment(Request $request, Order $order)
    {
        $request->validate(['qris_ref' => 'required|string']);

        $order->update([
            'payment_status' => 'paid',
            'qris_ref'       => $request->qris_ref,
            'paid_at'        => now(),
            'kasir_id'       => auth()->id(),
            'status'         => 'processing',
        ]);

        return response()->json(['success' => true]);
    }

    public function receipt(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load('items', 'user', 'kasir');

        return view('pages.receipt', compact('order'));
    }

    // Admin
    public function adminIndex()
    {
        $orders = Order::with('user', 'items')
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'today_sales'    => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total'),
            'today_orders'   => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_sales'    => Order::where('payment_status', 'paid')->sum('total'),
        ];

        return view('pages.admin.orders', compact('orders', 'stats'));
    }

    private function authorizeOrder(Order $order): void
    {
        $user = auth()->user();
        if ($user->isSiswa() && $order->user_id !== $user->id) {
            abort(403);
        }
    }
}
