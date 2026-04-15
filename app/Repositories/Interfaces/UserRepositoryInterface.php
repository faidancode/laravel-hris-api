<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator;
    public function findById(string $id): ?User;
    public function create(array $data): User;
    public function update(string $id, array $data): User;
    public function delete(string $id): void;
    public function existsByEmail(string $email): bool;
}