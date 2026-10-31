<?php

namespace App\Services\User;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Services\User\AdminBaseAction;
use App\Models\CrudAdmin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Admin;

class StoreAdminAction implements AdminActionInterface
{
    public function execute(array $data, ?int $id = null)
    {
        Validator::make($data, [
            'ic_no' => 'required|digits:12|unique:admins,ic_no',
            'phone_no' => 'required|digits_between:10,11|unique:admins,phone_no',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'position' => 'required|in:Admin,Super Admin',
        ])->validate();

        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $data['user_id'] = $user->id;
        $data['availability'] = 1;
        
        return Admin::create($data);
    }
}
