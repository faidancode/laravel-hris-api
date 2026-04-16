<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionService $service
    ) {
    }

    public function index(Request $request)
    {
        return response()->json(
            $this->service->paginate($request->all())
        );
    }
}