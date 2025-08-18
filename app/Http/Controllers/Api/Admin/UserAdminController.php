<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->orderByDesc('id');
        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone_number', 'like', "%$s%");
            });
        }
        $perPage = (int) $request->query('per_page', 20);
        $users = $query->paginate($perPage);
        return response()->json(['status' => 'success', 'data' => $users]);
    }

    public function block(int $id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = true;
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'User blocked']);
    }

    public function unblock(int $id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = false;
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'User unblocked']);
    }
}
