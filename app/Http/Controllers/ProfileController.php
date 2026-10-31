<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\User\UserActionFactory;

class ProfileController extends Controller
{
    public function __construct(private readonly UserActionFactory $userActionFactory) {}

    public function show()
    {
        return $this->userActionFactory->make('profile.show')->execute();
    }

    public function update(Request $request)
    {
        return $this->userActionFactory->make('profile.update')->execute($request);
    }
}
