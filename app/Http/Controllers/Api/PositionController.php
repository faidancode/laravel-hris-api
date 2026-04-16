<?php

namespace App\Http\Controllers\Api;

use App\Enums\GlobalErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\PositionRequest;
use App\Services\PositionService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PositionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly PositionService $service,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->query('search'),
                'limit' => (int) $request->query('limit', 10),
                'sort_by' => $request->query('sort_by', 'created_at'),
                'sort_order' => $request->query('sort_order', 'desc'),
            ];

            $paginator = $this->service->paginate($filters);

            return $this->paginatedResponse($paginator);
        } catch (Exception $e) {
            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR, $e->getMessage());
        }
    }

    public function store(PositionRequest $request): JsonResponse
    {
        try {
            $position = $this->service->create($request->toDto());

            return $this->success($position->load('department'), 'Position created successfully.', 201);
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
            $position = $this->service->findOrFail($id);

            return $this->success($position);
        } catch (ModelNotFoundException $e) {
            return $this->errorFromEnum(GlobalErrorCode::NOT_FOUND, $e->getMessage());
        } catch (Exception $e) {
            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR, $e->getMessage());
        }
    }

    public function update(PositionRequest $request, string $id): JsonResponse
    {
        if (!Str::isUuid($id)) {
            return $this->errorFromEnum(GlobalErrorCode::INVALID_UUID, 'Invalid UUID format.');
        }

        try {
            $position = $this->service->update($id, $request->validated());

            return $this->success($position, 'Position updated successfully.');
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

            return $this->success(null, 'Position deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorFromEnum(GlobalErrorCode::NOT_FOUND, $e->getMessage());
        } catch (Exception $e) {
            return $this->errorFromEnum(GlobalErrorCode::INTERNAL_ERROR, $e->getMessage());
        }
    }
}