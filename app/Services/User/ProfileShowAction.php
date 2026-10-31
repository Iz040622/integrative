<?php

namespace App\Services\User;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ProfileShowAction extends BaseAction
{
    public function execute(mixed ...$args): View
    {
        $user = Auth::user()->load('customer');
        /** @var ViewFactory $view */
        $view = $this->container->make(ViewFactory::class);
        return $view->make('profile', compact('user'));
    }
}
