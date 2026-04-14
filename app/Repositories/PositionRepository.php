<?php

namespace App\Repositories;

use App\Models\Position;
use App\Repositories\Interfaces\PositionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PositionRepository implements PositionRepositoryInterface
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Position::query()->with('department');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $limit = $filters['limit'] ?? 10;

        $allowedSortColumns = ['name', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');

        return $query->paginate($limit);
    }

    public function findById(string $id): ?Position
    {
        return Position::with('department')->find($id);
    }

    public function create(array $data): Position
    {
        return Position::create($data);
    }

    public function update(Position $position, array $data): Position
    {
        $position->update($data);
        return $position->fresh('department');
    }

    public function delete(Position $position): bool
    {
        return (bool) $position->delete();
    }
}