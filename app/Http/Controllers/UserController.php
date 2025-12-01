<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function me()
    {
        return response()->json(auth()->user());
    }
    public function user(UserUpdateRequest $request)
    {
        $user = auth()->user();

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

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
    public function store(CreateUserRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role' => $request['role'] ?? 'user',
        ]);

        return response()->json([
            'message' => 'Қолданушы сәтті қосылды ✅',
            'user' => $user
        ], 201);
    }
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->only(['name', 'email', 'role']);

        // Егер пароль берілсе, хэштеу
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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
