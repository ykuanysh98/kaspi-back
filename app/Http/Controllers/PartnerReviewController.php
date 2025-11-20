<?php

namespace App\Http\Controllers;

use App\Models\PartnerReview;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;
use App\Models\Order;
use App\Models\PartnerProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PartnerReviewController extends Controller
{
    public function index($id)
    {
        $reviews = PartnerReview::with('user:id,name')
            ->where('partner_id', $id)
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|min:3',
        ]);

        // Бір user → бір партнерге 1 отзыв
        $existing = PartnerReview::where('partner_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Сіз бұл магазинге отзыв қалдырғансыз'], 422);
        }

        PartnerReview::create([
            'partner_id' => $id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Отзыв қосылды']);
    }
}

