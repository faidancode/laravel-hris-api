<?php

namespace App\Services;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $repo,
        private readonly PermissionRegistrar $permissionRegistrar, // ✅ inject
    ) {
    }

    public function paginate(array $filters)
    {
        return $this->repo->paginate($filters);
    }

    public function findOrFail(string $id): Role
    {
        $role = $this->repo->findById($id);

        if (!$role) {
            throw new ModelNotFoundException("Role not found");
        }

        return $role;
    }

    public function create(array $data): Role
    {
        return $this->repo->create($data);
    }

    public function update(string $id, array $data): Role
    {
        $role = $this->findOrFail($id);
        return $this->repo->update($role, $data);
    }

    public function delete(string $id): bool
    {
        $role = $this->findOrFail($id);
        return $this->repo->delete($role);
    }

    public function syncPermissions(string $id, array $permissions): Role
    {
        $role = $this->findOrFail($id);

        $role->syncPermissions($permissions);

        $this->permissionRegistrar->forgetCachedPermissions(); // ✅ aman

        return $role->load('permissions');
    }
}