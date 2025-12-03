<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\PartnerProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\DecreaseCartItemRequest;
use App\Http\Requests\MergeGuestCartRequest;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->with('product.partners')->get();
        return response()->json($cart);
    }

    public function add(AddCartItemRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $qty = $request->quantity ?? 1;
        $qtyPartner = PartnerProduct::where('product_id', $product->id)
            ->where('partner_id', $request->partner_id)
            ->value('quantity');

        if ($qty > $qtyPartner) {
            return response()->json([
                'message' => "Қоймада тек {$qtyPartner} дана бар"
            ], 400);
        }

        $price = PartnerProduct::where('product_id', $product->id)
            ->where('partner_id', $request->partner_id)
            ->value('price') ?? $product->price;

        $cart = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'partner_id' => $request->partner_id,
        ]);

        $cart->quantity = ($cart->quantity ?? 0) + $qty;
        if ($cart->quantity > $qtyPartner) {
            return response()->json([
                'message' => 'Қоймада бар саннан артық қоса алмайсыз'
            ], 400);
        }

        $cart->price = $price;
        $cart->save();

        return response()->json($cart->load('product'), 201);
    }

    public function decrease(DecreaseCartItemRequest $request)
    {
        $cartItem = Cart::where([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'partner_id' => $request->partner_id
        ])->firstOrFail();

        $cartItem->quantity -= $request->quantity ?? 1;

        if ($cartItem->quantity <= 0) {
            $cartItem->delete();
            return response()->json(['message' => 'Cart item removed']);
        }

        $cartItem->save();
        return response()->json($cartItem);
    }

    public function mergeGuestCart(MergeGuestCartRequest $request)
    {
        $userId = $request->user()->id;

        foreach ($request->guest_cart as $item) {
            Cart::updateOrCreate(
                [
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'partner_id' => $item['partner_id'],
                ],
                ['quantity' => DB::raw("quantity + {$item['quantity']}")]
            );
        }

        return response()->json(['message' => 'Guest cart merged successfully']);
    }
}
