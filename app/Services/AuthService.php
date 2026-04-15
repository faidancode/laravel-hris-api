<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AuthRepository;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWTGuard;
use App\DTOs\AuthDto;

class AuthService
{
    public function __construct(
        protected AuthRepository $authRepo
    ) {
    }

    /**
     * HR Create Employee
     */

    /**
     * Login
     */
    public function login(AuthDto $dto): array
    {
        $user = $this->authRepo->getByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new Exception("Kredensial salah.");
        }

        if (!$user->is_active) {
            throw new Exception("User tidak aktif.");
        }

        /** @var JWTGuard $guard */
        $guard = auth('api');

        $accessToken = $guard->login($user);

        if (!$accessToken) {
            throw new Exception("Gagal membuat token.");
        }

        $refreshToken = Str::random(64);
        $this->authRepo->updateRefreshToken($user->id, $refreshToken);

        return [
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $guard->factory()->getTTL() * 60
        ];
    }

    /**
     * Refresh Token
     */
    public function refreshToken(string $refreshToken): array
    {
        $user = $this->authRepo->getUserByRefreshToken($refreshToken);

        if (!$user) {
            throw new Exception("Refresh token tidak valid.");
        }

        /** @var JWTGuard $guard */
        $guard = auth('api');

        $newAccessToken = $guard->login($user);

        $newRefreshToken = Str::random(64);
        $this->authRepo->updateRefreshToken($user->id, $newRefreshToken);

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'expires_in' => $guard->factory()->getTTL() * 60
        ];
    }
}