<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PartnerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:partners',
            'password' => 'required|min:6|confirmed',
        ]);

        $partner = Partner::create([
            'company_name' => $data['company_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['partner' => $partner], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $partner = Partner::where('email', $request->email)->first();

        if (!$partner || !\Hash::check($request->password, $partner->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $partner->createToken('partner_token')->plainTextToken;

        return response()->json([
            'partner' => $partner,
            'token' => $token,
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me()
    {
        return response()->json(auth('sanctum')->user());
    }

    public function edit(Request $request)
    {
        $partner = auth('sanctum')->user();

        if (!$partner) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:partners,email,' . $partner->id,
            'password' => 'sometimes|string|min:6|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $partner->update($validated);

        return response()->json([
            'message' => 'Мәлімет сәтті жаңартылды ✅',
            'partner' => $partner,
        ]);
    }

    public function index()
    {
        $partners = Partner::all();

        foreach ($partners as $partner) {
            $items = OrderItem::where('partner_id', $partner->id)->get();

            $orderIds = $items->pluck('order_id')->unique();

            $orders = Order::whereIn('id', $orderIds)->get();

            $partner->orders = $orders;

            $total = $items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $partner->total_sales = $total;

            $avgRating = PartnerReview::where('partner_id', $partner->id)->avg('rating');
            $partner->rating = $avgRating ? round($avgRating, 1) : 0;

            $partner->reviews_count = PartnerReview::where('partner_id', $partner->id)->count();
        }

        return response()->json($partners);
    }

    public function show($id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json(['message' => 'Partner not found'], 404);
        }
        return response()->json($partner);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:partners',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $partner = Partner::create($validated);

        return response()->json([
            'message' => 'Partner created successfully',
            'partner' => $partner
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json(['message' => 'Partner not found'], 404);
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:partners,email,' . $id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $partner->update($validated);

        return response()->json([
            'message' => 'Partner updated successfully',
            'partner' => $partner
        ]);
    }

    public function destroy($id)
    {
        $partner = Partner::find($id);
        if (!$partner) {
            return response()->json(['message' => 'Partner not found'], 404);
        }

        $partner->delete();

        return response()->json(['message' => 'Partner deleted successfully']);
    }
}
