<?php

namespace App\Repositories\Interfaces;

use App\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PositionRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator;
    public function findById(string $id): ?Position;
    public function create(array $data): Position;
    public function update(Position $position, array $data): Position;
    public function delete(Position $position): bool;
}