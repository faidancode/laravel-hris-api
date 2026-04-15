<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(AuthRequest $request)
    {
        Log::info('Login attempt', ['email' => $request->email]);
        try {
            $data = $this->authService->login(
                $request->toDto()
            );

            Log::debug('Login successful', ['email' => $request->email]);
            return response()->json($data);
        } catch (Exception $e) {
            Log::debug('Login failed', ['email' => $request->email, 'error' => $e->getMessage()]);
            return response()->json([
                'message' => $e->getMessage()
            ], 401);
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