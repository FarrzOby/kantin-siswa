<?php
// app/Http/Controllers/CartController.php
namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('menuItem.category')
            ->where('user_id', auth()->id())
            ->get();

        $total = $cartItems->sum(fn($item) => $item->subtotal);

        return view('pages.cart', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity'     => 'integer|min:1|max:99',
        ]);

        $menuItem = MenuItem::findOrFail($request->menu_item_id);

        if (!$menuItem->is_available || $menuItem->stock < 1) {
            return response()->json(['error' => 'Item tidak tersedia'], 422);
        }

        $cartItem = CartItem::updateOrCreate(
            ['user_id' => auth()->id(), 'menu_item_id' => $request->menu_item_id],
            ['quantity' => \DB::raw('quantity + ' . ($request->quantity ?? 1))]
        );

        // Ensure quantity doesn't exceed stock
        if ($cartItem->quantity > $menuItem->stock) {
            $cartItem->update(['quantity' => $menuItem->stock]);
        }

        $count = CartItem::where('user_id', auth()->id())->sum('quantity');

        return response()->json(['success' => true, 'cart_count' => $count]);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorize('update', $cartItem);

        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $cartItem->update(['quantity' => $request->quantity]);

        $total = CartItem::with('menuItem')
            ->where('user_id', auth()->id())
            ->get()
            ->sum(fn($item) => $item->subtotal);

        return response()->json([
            'success'       => true,
            'item_subtotal' => 'Rp ' . number_format($cartItem->subtotal, 0, ',', '.'),
            'total'         => 'Rp ' . number_format($total, 0, ',', '.'),
        ]);
    }

    public function remove(CartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);
        $cartItem->delete();

        return response()->json(['success' => true]);
    }

    public function count()
    {
        $count = CartItem::where('user_id', auth()->id())->sum('quantity');
        return response()->json(['count' => $count]);
    }
}
