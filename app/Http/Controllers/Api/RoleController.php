<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $service
    ) {
    }

    public function index(Request $request)
    {
        return response()->json(
            $this->service->paginate($request->all())
        );
    }

    public function store(Request $request)
    {
        $role = $this->service->create($request->only('name'));

        return response()->json($role, 201);
    }

    public function syncPermissions(Request $request, string $id)
    {
        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = $this->service->syncPermissions($id, $request->permissions);

        return response()->json($role);
    }
}