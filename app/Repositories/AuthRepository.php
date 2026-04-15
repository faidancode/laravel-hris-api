<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class AuthRepository
{
    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function updateRefreshToken(string $userId, string $token): void
    {
        User::where('id', $userId)->update([
            'refresh_token' => $token
        ]);
    }

    public function getUserByRefreshToken(string $token): ?User
    {
        return User::where('refresh_token', $token)->first();
    }
}