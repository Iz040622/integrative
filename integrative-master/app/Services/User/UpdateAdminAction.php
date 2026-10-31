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

class UpdateAdminAction implements AdminActionInterface
{
    public function execute(array $data, ?int $id = null)
    {
        $admin = Admin::findOrFail($id);

        Validator::make($data, [
            'ic_no' => 'required|digits:12|unique:admins,ic_no,' . $id,
            'phone_no' => 'required|digits_between:10,11|unique:admins,phone_no,' . $id,
            'email' => 'required|email|unique:users,email,' . $admin->user_id . ',id',
            'password' => 'nullable|string|min:8|confirmed',
            'position' => 'required|in:Admin,Super Admin',
            'availability' => 'required|in:0,1',
        ])->validate();

        $userData = [];
        if (isset($data['email'])) $userData['email'] = $data['email'];
        if (!empty($data['password'])) $userData['password'] = bcrypt($data['password']);
        if (!empty($userData)) {
            $admin->user->update($userData);
        }

        $admin->update($data);

        return $admin;
    }
}
