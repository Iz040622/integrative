<?php

namespace App\Services\User;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

class SignUpShowAction extends BaseAction
{
    public function execute(mixed ...$args): View
    {
        /** @var ViewFactory $view */
        $view = $this->container->make(ViewFactory::class);
        return $view->make('auth.SignUp');
    }
}
