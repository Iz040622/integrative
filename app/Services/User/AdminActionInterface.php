<?php

namespace App\Services\User;

interface AdminActionInterface
{
    public function execute(array $data, ?int $id = null);
}
