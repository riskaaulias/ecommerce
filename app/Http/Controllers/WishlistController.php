<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()->wishlists ?? collect();

        return view('wishlist.index', [
            'items' => $wishlists,
        ]);
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ])->first();

        if ($wishlist) {
            $wishlist->delete();
            $isInWishlist = false;
            $message = 'Dihapus dari favorit';
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $isInWishlist = true;
            $message = 'Ditambahkan ke favorit';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'isInWishlist' => $isInWishlist,
        ]);
    }
}