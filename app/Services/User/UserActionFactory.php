<?php

namespace App\Services\User;

use Illuminate\Contracts\Container\Container;

interface UserActionFactory
{
    public function make(string $actionKey): UserActionInterface;
}

interface UserActionInterface
{
    public function execute(mixed ...$args): mixed;
}


