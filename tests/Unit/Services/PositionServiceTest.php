<?php

use App\DTOs\PositionDto;
use App\Models\Position;
use App\Repositories\Interfaces\PositionRepositoryInterface;
use App\Services\PositionService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;

uses(MockeryPHPUnitIntegration::class)->beforeEach(function () {
    $this->repositoryMock = Mockery::mock(PositionRepositoryInterface::class);
    $this->service = new PositionService($this->repositoryMock);
});
// ─────────────────────────────────────────────
// paginate()
// ─────────────────────────────────────────────

describe('paginate()', function () {
    it('returns a paginator from repository', function () {
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $filters = ['search' => 'Engineer', 'limit' => 10, 'sort_by' => 'name', 'sort_order' => 'asc'];

        $this->repositoryMock
            ->shouldReceive('paginate')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $result = $this->service->paginate($filters);

        expect($result)->toBe($paginator);
    });

    it('passes empty filters to repository', function () {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repositoryMock
            ->shouldReceive('paginate')
            ->once()
            ->with([])
            ->andReturn($paginator);

        $result = $this->service->paginate([]);

        expect($result)->toBe($paginator);
    });
});

// ─────────────────────────────────────────────
// findOrFail()
// ─────────────────────────────────────────────

describe('findOrFail()', function () {
    it('returns position when found', function () {
        $id = (string) \Illuminate\Support\Str::uuid();
        $position = new Position(['id' => $id, 'name' => 'Engineer']);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($position);

        $result = $this->service->findOrFail($id);

        expect($result)->toBe($position);
    });

    it('throws ModelNotFoundException when position not found', function () {
        $id = (string) \Illuminate\Support\Str::uuid();

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        expect(fn() => $this->service->findOrFail($id))
            ->toThrow(ModelNotFoundException::class);
    });

    it('throws ModelNotFoundException for soft-deleted position', function () {
        $id = (string) \Illuminate\Support\Str::uuid();

        // Soft-deleted records are excluded from `find()` — repository returns null
        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        expect(fn() => $this->service->findOrFail($id))
            ->toThrow(ModelNotFoundException::class);
    });
});

// ─────────────────────────────────────────────
// create()
// ─────────────────────────────────────────────

describe('create()', function () {
    it('creates and returns a position', function () {
        $dto = new PositionDto(
            name: 'Backend Engineer',
            department_id: (string) \Illuminate\Support\Str::uuid(),
        );

        $position = new Position($dto->toArray());

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($dto->toArray())
            ->andReturn($position);

        $result = $this->service->create($dto);

        expect($result)->toBe($position);
    });
});

// ─────────────────────────────────────────────
// update()
// ─────────────────────────────────────────────

describe('update()', function () {
    it('updates and returns a position', function () {
        $id = (string) \Illuminate\Support\Str::uuid();
        $position = new Position(['id' => $id, 'name' => 'Old Name']);
        $updated = new Position(['id' => $id, 'name' => 'New Name']);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($position);

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($position, ['name' => 'New Name'])
            ->andReturn($updated);

        $result = $this->service->update($id, ['name' => 'New Name']);

        expect($result->name)->toBe('New Name');
    });

    it('throws ModelNotFoundException when updating non-existent position', function () {
        $id = (string) \Illuminate\Support\Str::uuid();

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        expect(fn() => $this->service->update($id, ['name' => 'New Name']))
            ->toThrow(ModelNotFoundException::class);
    });
});

// ─────────────────────────────────────────────
// delete()
// ─────────────────────────────────────────────

describe('delete()', function () {
    it('deletes a position and returns true', function () {
        $id = (string) \Illuminate\Support\Str::uuid();
        $position = new Position(['id' => $id]);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($position);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($position)
            ->andReturn(true);

        $result = $this->service->delete($id);

        expect($result)->toBeTrue();
    });

    it('throws ModelNotFoundException when deleting non-existent position', function () {
        $id = (string) \Illuminate\Support\Str::uuid();

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        expect(fn() => $this->service->delete($id))
            ->toThrow(ModelNotFoundException::class);
    });
});