<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\GlobalErrorCode;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(AuthRequest $request): JsonResponse
    {
        try {
            // Memanggil method toDto() yang ada di AuthRequest Anda
            $result = $this->authService->login($request->toDto());

            return $this->success(
                new AuthResource($result),
                'Login successful'
            );
        } catch (Exception $e) {
            Log::error('Login failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            // Cek error spesifik (401 Unauthorized)
            if ($e->getMessage() === "Kredensial salah." || $e->getMessage() === "User tidak aktif.") {
                return $this->error('AUTH_ERROR', $e->getMessage(), null, 401);
            }

            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $data = $this->authService->refreshToken(
                $request->refresh_token
            );

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 401);
        }
    }

}