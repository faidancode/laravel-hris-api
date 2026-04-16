<?php

namespace App\Repositories\Interfaces;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DepartmentRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator;
    public function findById(string $id): ?Department;
    public function create(array $data): Department;
    public function update(Department $department, array $data): Department;
    public function delete(Department $department): bool;
}