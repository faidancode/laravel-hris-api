<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Exception;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {
    }

    public function index(Request $request)
    {
        $data = $this->service->paginate($request->all());

        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'total' => $data->total(),
                'count' => count($data->items()),
                'limit' => $data->perPage(),
                'currentPage' => $data->currentPage(),
                'totalPages' => $data->lastPage(),
                'hasNext' => $data->hasMorePages(),
            ],
        ]);
    }

    public function show(string $id)
    {
        try {
            return response()->json($this->service->getById($id));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function store(UserRequest $request)
    {
        try {
            $user = $this->service->create($request->toDto());
            return response()->json($user, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function update(UserRequest $request, string $id)
    {
        try {
            return response()->json(
                $this->service->update($id, $request->toDto())
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return response()->json([], 204);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function assignRole(Request $request, string $id)
    {
        try {
            $user = $this->service->assignRole($id, $request->role);

            return response()->json($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}