<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(Request $request)
    {
        try {
            $data = $this->authService->login(
                $request->email,
                $request->password
            );

            return response()->json($data);
        } catch (Exception $e) {
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