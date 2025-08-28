<?php

namespace App\Repository;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $attributes): User;
    public function getUsername(int $id): string;
}
