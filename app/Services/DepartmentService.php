<?php

namespace App\Services;

use App\DTOs\DepartmentDto;
use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartmentService
{
    public function __construct(
        private readonly DepartmentRepositoryInterface $repository,
    ) {
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function findOrFail(string $id): Department
    {
        $department = $this->repository->findById($id);

        if (!$department) {
            throw new ModelNotFoundException("Department with ID [{$id}] not found.");
        }

        return $department;
    }

    public function create(DepartmentDto $dto): Department
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(string $id, array $data): Department
    {
        $department = $this->findOrFail($id);
        return $this->repository->update($department, $data);
    }

    public function delete(string $id): bool
    {
        $department = $this->findOrFail($id);
        return $this->repository->delete($department);
    }
}