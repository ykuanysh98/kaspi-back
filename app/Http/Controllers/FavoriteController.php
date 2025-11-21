<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\Favorites;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function index()
    {
        $products = Product::leftjoin('favorites', 'favorites.product_id', '=', 'products.id')
            ->where('favorites.user_id', Auth::id())
            ->select('products.*', DB::raw('true as is_favorite'))
            ->get();

        foreach ($products as $product) {
            // Images
            $product->images = ProductImage::where('product_id', $product->id)
                ->select('id', 'path')
                ->get();
        }

        return response()->json($products);
    }

    public function toggle(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $favorites = Favorites::where('product_id', $request->product_id)
            ->where('user_id', $user->id)
            ->first();

        if ($favorites) {

            $favorites->delete();
            return response()->json(['message' => 'Removed from favorites', 'is_favorite' => false], 200);

        } else {

            Favorites::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id
            ]);
            return response()->json(['message' => 'Added to favorites', 'is_favorite' => true], 200);

        }
    }
}

