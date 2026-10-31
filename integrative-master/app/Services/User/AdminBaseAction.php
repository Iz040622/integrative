<?php

namespace App\Services\User;

use Illuminate\Contracts\Container\Container;

abstract class BaseAction implements AdminActionInterface
{
    public function __construct(protected Container $container)
    {
        
    }
}
