<?php

namespace App\Services;

use App\DTOs\PositionDto;
use App\Models\Position;
use App\Repositories\Interfaces\PositionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PositionService
{
    public function __construct(
        private readonly PositionRepositoryInterface $repository,
    ) {
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function findOrFail(string $id): Position
    {
        $position = $this->repository->findById($id);

        if (!$position) {
            throw new ModelNotFoundException("Position with ID [{$id}] not found.");
        }

        return $position;
    }

    public function create(PositionDto $dto): Position
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(string $id, array $data): Position
    {
        $position = $this->findOrFail($id);
        return $this->repository->update($position, $data);
    }

    public function delete(string $id): bool
    {
        $position = $this->findOrFail($id);
        return $this->repository->delete($position);
    }
}