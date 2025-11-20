<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MergeGuestCartRequest;

class CartController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $cart = Cart::where('user_id', $userId)->get();

        foreach ($cart as $item) {
            $item->product = Product::find($item->product_id);
        }

        return response()->json($cart);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'partner_id' => 'required|exists:partners,id',
            'quantity' => 'required|integer',
            'price' => 'nullable|integer',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $qty = $request->quantity ?? 1;

        if ($qty > $product->quantity) {
            return response()->json(['message' => 'Қоймада тек ' . $product->quantity . ' дана бар'], 400);
        }

        $cart = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'partner_id' => $request->partner_id,
            'price' => $request->price,
        ]);

        $newQty = ($cart->exists ? $cart->quantity : 0) + $qty;

        if ($newQty > $product->quantity)
            return response()->json(['message' => 'Қоймада бар саннан артық қоса алмайсыз'], 400);

        $cart->quantity = $newQty;
        $cart->save();

        return response()->json($cart, 201);
    }

    public function decrease(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'partner_id' => 'required|exists:partners,id',
            'quantity' => 'integer|min:1'
        ]);

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('partner_id', $request->partner_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->quantity -= $request->quantity ?? 1;

        if ($cartItem->quantity <= 0) {
            $cartItem->delete();
            return response()->json(['message' => 'Cart item removed']);
        } else {
            $cartItem->save();
            return response()->json($cartItem);
        }
    }

    public function mergeGuestCart(MergeGuestCartRequest $request)
    {
        $user = $request->user();
        foreach ($request->guest_cart as $item) {
            $existing = Cart::where('user_id', $user->id)
                ->where('product_id', $item['product_id'])
                ->where('partner_id', $item['partner_id'])
                ->first();

            if ($existing) {
                $existing->quantity += $item['quantity'];
                $existing->save();
            } else {
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $item['product_id'],
                    'partner_id' => $item['partner_id'],
                    'quantity' => $item['quantity']
                ]);
            }
        }

        return response()->json(['message' => 'Guest cart merged successfully']);
    }

}
