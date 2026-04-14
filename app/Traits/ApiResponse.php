<?php

namespace App\Traits;

use App\Contracts\AppErrorInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

trait ApiResponse
{
    protected function success(mixed $data, mixed $message = null, int $status = 200, mixed $meta = null): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => $this->recursiveCamelCase($data),
            'error' => null,
            'meta' => $meta ? $this->recursiveCamelCase($meta) : null,
            'message' => $message,
        ], $status);
    }

    protected function error(string $errorCode, string $message, mixed $details = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'ok' => false,
            'data' => null,
            'error' => [
                'code' => $errorCode,
                'message' => $message,
                'details' => $this->recursiveCamelCase($details),
            ],
            'meta' => null,
        ], $status);
    }

    public function paginatedResponse(LengthAwarePaginator $paginator, ?string $message = null): JsonResponse
    {
        return $this->success($paginator->items(), $message, 200, [
            'total' => $paginator->total(),
            'count' => $paginator->count(),
            'limit' => $paginator->perPage(),
            'currentPage' => $paginator->currentPage(),
            'totalPages' => $paginator->lastPage(),
            'hasNext' => $paginator->hasMorePages(),
        ]);
    }

    /**
     * Mengubah kunci array menjadi camelCase secara rekursif
     */
    private function recursiveCamelCase(mixed $data): mixed
    {
        if ($data instanceof \Illuminate\Http\Resources\Json\JsonResource) {
            $data = $data->resolve();
        }

        if ($data instanceof \Illuminate\Support\Collection || $data instanceof \Illuminate\Database\Eloquent\Collection) {
            $data = $data->toArray();
        }

        if (!is_array($data))
            return $data;

        $camelCased = [];
        foreach ($data as $key => $value) {
            // Ubah key jadi camelCase
            $newKey = is_string($key) ? Str::camel($key) : $key;

            // Jika value adalah array, proses lagi (rekursif)
            $camelCased[$newKey] = $this->recursiveCamelCase($value);
        }

        return $camelCased;
    }


    public function errorFromEnum(AppErrorInterface $error, mixed $details = null): JsonResponse
    {
        return $this->error(
            $error->code(),
            $error->message(),
            $details,
            $error->status()
        );
    }

}