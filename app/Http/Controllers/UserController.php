<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\PartnerProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }

    public function user(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Профиль сәтті жаңартылды ✅',
            'user' => $user
        ]);
    }

    public function index(Request $request)
    {
        $sort = $request->get('sort', 'id');

        $query = User::leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->select(
                'users.*',
                DB::raw('SUM(orders.total) as total_orders_sum'),
                DB::raw('COUNT(orders.id) as total_orders_count')
            )
            ->groupBy('users.id', 'users.name', 'users.email');

        switch ($sort) {
            case 'sum_max':
                $query->orderByDesc('total_orders_sum');
                break;
            case 'sum_min':
                $query->orderBy('total_orders_sum');
                break;
            default:
                $query->orderBy('id');
                break;
        }

        $users = $query->get();

        return response()->json($users);
    }

    public function show(User $user)
    {
        $orders = Order::where('user_id', $user->id)->get();

        $user->orders = $orders;

        return response()->json([
            'data' => $user
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
        ]);

        return response()->json([
            'message' => 'Қолданушы сәтті қосылды ✅',
            'user' => $user
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string',
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (isset($validated['role'])) {
            $user->role = $validated['role'];
        }

        $user->save();

        return response()->json([
            'message' => 'Қолданушы сәтті жаңартылды ✅',
            'user' => $user
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
