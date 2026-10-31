<?php

namespace App\Services\User;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

class AdminActionFactory
{
    /** @var array<string,class-string<AdminActionInterface>> */
    private array $map;

    public function __construct(private readonly Container $container)
    {
        $this->map = [
            'admin.create' => StoreAdminAction::class,
            'admin.update' => UpdateAdminAction::class,
        ];
    }

    public function make(string $actionKey): AdminActionInterface
    {
        $class = $this->map[$actionKey] ?? null;
        if ($class === null) {
            throw new InvalidArgumentException("Unknown admin action: {$actionKey}");
        }
        return $this->container->make($class);
    }
}