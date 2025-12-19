<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Ambil cart user atau buat baru
        $cart = Cart::firstOrCreate(
            ['user_id' => auth()->id()]
        );

        // Ambil item cart
        $items = $cart->items()->with('product')->get();

        return view('cart.index', [
            'cart' => $cart,
            'items' => $items,
            'total' => $items->sum(fn ($item) => $item->subtotal),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        $cart = Cart::firstOrCreate(
            ['user_id' => auth()->id()]
        );

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->update([
                'quantity' => $item->quantity + $quantity,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk ditambahkan ke keranjang',
            'cart_count' => $cart->items()->count(),
        ]);
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item diperbarui',
        ]);
    }

    public function remove(CartItem $item)
    {
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang',
        ]);
    }
}