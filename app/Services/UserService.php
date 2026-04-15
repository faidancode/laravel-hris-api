<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $repo
    ) {
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginate($filters);
    }

    public function getById(string $id): User
    {
        $user = $this->repo->findById($id);

        if (!$user) {
            throw new Exception("User tidak ditemukan.");
        }

        return $user;
    }

    public function create(array $data): User
    {
        if ($this->repo->existsByEmail($data['email'])) {
            throw new Exception("Email sudah digunakan.");
        }

        $user = $this->repo->create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    public function update(string $id, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->repo->update($id, $data);

        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function delete(string $id): void
    {
        $this->repo->delete($id);
    }
}