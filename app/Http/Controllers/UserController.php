<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    public function index()
    {
        return User::all();
    }

    public function show(int $id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = $this->userRepository->create($validated);

        return response()->json($user, 201);
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $validated = $request->validated();

        $user = User::findOrFail($id);
        $user->update($validated);

        return response()->json($user);
    }

    public function delete(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->noContent();
    }

    public function getUsernameById(int $id)
    {
        $username = $this->userRepository->getUsername($id);

        return response()->json(['name' => $username]);
    }
}
