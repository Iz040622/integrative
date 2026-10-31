<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function getCurrentCustomer()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $customer = Customer::with('user')->where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $customerData = [
            'id' => $customer->user_id,
            'name' => $customer->first_name . ' ' . $customer->last_name,
            'email' => $customer->user->email,
        ];
        return response()->json($customerData);
    }
}
