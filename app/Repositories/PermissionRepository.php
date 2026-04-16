<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Permission::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($filters['limit'] ?? 10);
    }

    public function findById(string $id): ?Permission
    {
        return Permission::find($id);
    }
}