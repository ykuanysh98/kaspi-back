<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PartnerProduct;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\CreateOrderRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function store(Request $request)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {

            $total = 0;

            foreach ($request->items as $item) {
                $productPrice = PartnerProduct::where('product_id', $item['product_id'])
                    ->where('partner_id', $item['partner_id'])
                    ->value('price');

                // Фолбэк: егер partner_product.price NULL → product.price
                if (!$productPrice) {
                    $productPrice = Product::find($item['product_id'])->price;
                }

                $total += $productPrice * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total'   => $total,
                'status'  => 'pending',
            ]);

            foreach ($request->items as $item) {

                $product = Product::find($item['product_id']);

                $partnerPrice = PartnerProduct::where('product_id', $item['product_id'])
                    ->where('partner_id', $item['partner_id'])
                    ->value('price');

                $finalPrice = $partnerPrice ?: $product->price;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'partner_id' => $item['partner_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $finalPrice,
                ]);
            }

            if ($user) {
                Cart::where('user_id', $user->id)->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order'   => $order->load('items.product')
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Order creation failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
       $orders = Order::with(['items.product'])
           ->where('user_id', Auth::id())
           ->get();

        return response()->json($orders, 201);
    }

    public function list()
    {
        $partner = Auth::user();

        $orders = \DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.partner_id', $partner->id)
            ->select('orders.*')
            ->get();

        return response()->json($orders, 201);
    }

}
