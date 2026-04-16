<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = User::query()->with(['roles', 'employee']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', fn($q) => $q->where('name', $filters['role']));
        }

        if (array_key_exists('employee_id', $filters)) {
            if ($filters['employee_id'] === 'null' || $filters['employee_id'] === null) {
                $query->whereNull('employee_id');
            } else {
                $query->where('employee_id', $filters['employee_id']);
            }
        }

        $limit = $filters['limit'] ?? 10;

        return $query->paginate($limit);
    }

    public function findById(string $id): ?User
    {
        return User::with(['roles', 'employee'])->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(string $id, array $data): User
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete(string $id): void
    {
        User::findOrFail($id)->delete();
    }

    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}