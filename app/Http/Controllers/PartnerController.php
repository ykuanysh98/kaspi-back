<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PartnerReview;
use App\Http\Requests\PartnerRegisterRequest;
use App\Http\Requests\PartnerLoginRequest;
use App\Http\Requests\EditPartnerRequest;
use App\Http\Requests\CreatePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    public function register(PartnerRegisterRequest $request)
    {
        $partner = Partner::create([
            'company_name' => $request->company_name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        return response()->json(['partner' => $partner], 201);
    }
    public function login(PartnerLoginRequest $request)
    {
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
        $partner = auth()->user();
        return response()->json($partner);
    }
    public function edit(EditPartnerRequest $request)
    {
        $partner = $request->user();
        $validated = $request->validated();

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
        $partners = Partner::query()
            ->leftJoin('order_items', 'partners.id', '=', 'order_items.partner_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('partner_reviews', 'partners.id', '=', 'partner_reviews.partner_id')
            ->select(
                'partners.*',

                // Барлық сатылым сомасы
                DB::raw('COALESCE(SUM(order_items.price * order_items.quantity), 0) AS total_sales'),

                // Орташа рейтинг
                DB::raw('COALESCE(ROUND(AVG(partner_reviews.rating), 1), 0) AS rating'),

                // Ревью саны
                DB::raw('COUNT(partner_reviews.id) AS reviews_count')
            )
            ->groupBy('partners.id')
            ->get();

        return response()->json($partners);
    }
    public function show($id)
    {
        $partner = Partner::with([
            'products.images',      // өнімдердің суреттері
            'products.partners'     // әр өнімге байланысты партнерлер
        ])->find($id);

        if (!$partner) {
            return response()->json(['message' => 'Partner not found'], 404);
        }
        return response()->json($partner);
    }
    public function store(CreatePartnerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        $partner = Partner::create($data);

        return response()->json([
            'message' => 'Partner created successfully',
            'partner' => $partner
        ], 201);
    }
    public function update(UpdatePartnerRequest $request, $id)
    {
        $partner = Partner::find($id);

        if (!$partner) {
            return response()->json(['message' => 'Partner not found'], 404);
        }

        $data = $request->validated();

        if ($request->filled('password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $partner->update($data);

        return response()->json([
            'message' => 'Partner updated successfully',
            'partner' => $partner,
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
