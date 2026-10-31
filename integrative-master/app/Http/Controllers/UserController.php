<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::with(['customer', 'admin'])->get();
        return response()->json($users);
    }

    public function currentUserRole()
    {
        $user = Auth::user();
        // Check role
        $role = null;
        if ($user->admin) {
            $role = 'admin';
        } elseif ($user->customer) {
            $role = 'customer';
        }

        // Return response (or pass to another module via API)
        return response()->json([
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $role,
        ]);
    }
}
