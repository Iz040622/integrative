<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Get all admins with user email
    public function getAdmins()
    {
        $admins = Admin::with('user')->get();

        return response()->json($admins->map(function($admin) {
            return [
                'id'       => $admin->id,
                'user_id'  => $admin->user_id,
                'position' => $admin->position,
                'phone_no' => $admin->phone_no,
                'ic_no'    => $admin->ic_no,
                'availability' => $admin->availability,
                'user'     => [
                    'email' => $admin->user->email
                ]
            ];
        }));
    }

    // Get admin info by user ID
    public function getAdminByUserId($userId)
    {
        $admin = Admin::with('user')->where('user_id', $userId)->first();

        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        return response()->json([
            'id'       => $admin->id,
            'user_id'  => $admin->user_id,
            'position' => $admin->position,
            'phone_no' => $admin->phone_no,
            'ic_no'    => $admin->ic_no,
            'availability' => $admin->availability,
            'user'     => [
                'email' => $admin->user->email
            ]
        ]);
    }
}
