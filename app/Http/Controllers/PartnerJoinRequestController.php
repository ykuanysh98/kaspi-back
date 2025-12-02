<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PartnerJoinRequest;
use Illuminate\Http\Request;

class PartnerJoinRequestController extends Controller
{
    public function sendJoinRequest(Product $product)
    {
        // 1) Тек активный продуктке ғана запрос жіберуге болады
        if ($product->status !== 'active') {
            return response()->json(['message' => 'Product is not active'], 400);
        }

        // 2) Өзінің тауарға запрос жібермеу
        if ($product->partner_id == auth()->id()) {
            return response()->json(['message' => 'Cannot join your own product'], 400);
        }

        // 3) Алдыңғы pending запрос барма тексеру
        $exists = PartnerJoinRequest::where('product_id', $product->id)
            ->where('partner_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Request already sent'], 400);
        }

        // 4) Жаңа запрос құру
        PartnerJoinRequest::create([
            'product_id' => $product->id,
            'partner_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Join request sent successfully']);
    }
    public function approve(PartnerJoinRequest $request)
    {
        $request->update(['status' => 'approved']);

        // партнерды осы продуктке қосу
        // partner_product кестесіне жаңа жазба қосады
        $request->product->partners()->attach($request->partner_id);

        return response()->json(['message' => 'Request approved']);
    }
    public function reject(PartnerJoinRequest $request)
    {
        $request->update(['status' => 'rejected']);

        return response()->json(['message' => 'Request rejected']);
    }
    public function remove(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->partners()->detach($request->partner_id);

        return response()->json(['message' => 'Removed']);
    }
    public function index()
    {
        return PartnerJoinRequest::with(['partner', 'product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

