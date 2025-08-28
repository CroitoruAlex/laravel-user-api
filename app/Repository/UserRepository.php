<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $attributes): User
    {
        $user = new User();

        $user->fill($attributes);
        $user->save();

        Cache::put("user:{$user->id}:name", $user->name, 600);

        return $user;
    }

    //dummy implementation for cache
    public function getUsername(int $id): string
    {
        $cachedName = Cache::get("user:{$id}:name");

        if ($cachedName) {
            return $cachedName;
        }

        $user = User::findOrFail($id);

        Cache::put("user:{$user->id}:name", $user->name, 600);

        return $user->name;
    }
}
