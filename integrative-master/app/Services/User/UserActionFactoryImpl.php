<?php

namespace App\Services\User;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class UserActionFactoryImpl implements UserActionFactory
{
    /** @var array<string,class-string<UserActionInterface>> */
    private array $map;

    public function __construct(private readonly Container $container)
    {
        $this->map = [
            'signin.show' => \App\Services\User\SignInShowAction::class,
            'signin.attempt' => \App\Services\User\SignInAttemptAction::class,
            'signup.show' => \App\Services\User\SignUpShowAction::class,
            'signup.create' => \App\Services\User\SignUpCreateAction::class,
            'profile.show' => \App\Services\User\ProfileShowAction::class,
            'profile.update' => \App\Services\User\ProfileUpdateAction::class,
            'password.send_otp' => \App\Services\User\PasswordSendOtpAction::class,
            'password.verify_otp' => \App\Services\User\PasswordVerifyOtpAction::class,
            'password.reset' => \App\Services\User\PasswordResetAction::class,
        ];
    }

    public function make(string $actionKey): UserActionInterface
    {
        $class = $this->map[$actionKey] ?? null;
        if ($class === null) {
            throw new InvalidArgumentException("Unknown user action: {$actionKey}");
        }
        return $this->container->make($class);
    }
}


