<?php

namespace App\Http\Controllers\Api;

use App\Enums\GlobalErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Services\DepartmentService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly DepartmentService $service,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'limit' => (int) $request->integer('limit', 10) ?: 10,
                'sort_by' => $request->query('sort_by', 'created_at'),
                'sort_order' => $request->query('sort_order', 'desc'),
            ];

            $paginator = $this->service->paginate($filters);

            return $this->paginatedResponse($paginator);
        } catch (Exception $e) {
            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR, $e->getMessage());
        }
    }

    public function store(DepartmentRequest $request): JsonResponse
    {
        try {
            $department = $this->service->create($request->toDto());

            return $this->success($department, 'Department created successfully.', 201);
        } catch (Exception $e) {
            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR, $e->getMessage());
        }
    }

    public function show(string $id): JsonResponse
    {
        if (!Str::isUuid($id)) {
            return $this->errorFromEnum(GlobalErrorCode::INVALID_UUID, 'Invalid UUID format.');
        }

        try {
            $department = $this->service->findOrFail($id);

            return $this->success($department);
        } catch (ModelNotFoundException $e) {
            return $this->errorFromEnum(GlobalErrorCode::NOT_FOUND, $e->getMessage());
        } catch (Exception $e) {
            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR, $e->getMessage());
        }
    }

    public function update(DepartmentRequest $request, string $id): JsonResponse
    {
        if (!Str::isUuid($id)) {
            return $this->errorFromEnum(GlobalErrorCode::INVALID_UUID, 'Invalid UUID format.');
        }

        try {
            $department = $this->service->update($id, $request->validated());

            return $this->success($department, 'Department updated successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorFromEnum(GlobalErrorCode::NOT_FOUND, $e->getMessage());
        } catch (Exception $e) {
            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR, $e->getMessage());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        if (!Str::isUuid($id)) {
            return $this->errorFromEnum(GlobalErrorCode::INVALID_UUID, 'Invalid UUID format.');
        }

        try {
            $this->service->delete($id);

            return $this->success(null, 'Department deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorFromEnum(GlobalErrorCode::NOT_FOUND, $e->getMessage());
        } catch (Exception $e) {
            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR, $e->getMessage());
        }
    }
}