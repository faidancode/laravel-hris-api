<?php

use App\DTOs\DepartmentDto;
use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Services\DepartmentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;

uses(MockeryPHPUnitIntegration::class)->beforeEach(function () {
    $this->repositoryMock = Mockery::mock(DepartmentRepositoryInterface::class);
    $this->service = new DepartmentService($this->repositoryMock);
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
    it('returns Department when found', function () {
        $id = (string) \Illuminate\Support\Str::uuid();
        $Department = new Department(['id' => $id, 'name' => 'Engineer']);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($Department);

        $result = $this->service->findOrFail($id);

        expect($result)->toBe($Department);
    });

    it('throws ModelNotFoundException when Department not found', function () {
        $id = (string) \Illuminate\Support\Str::uuid();

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        expect(fn() => $this->service->findOrFail($id))
            ->toThrow(ModelNotFoundException::class);
    });

    it('throws ModelNotFoundException for soft-deleted Department', function () {
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
    it('creates and returns a Department', function () {
        $dto = new DepartmentDto(
            name: 'Backend Engineer',
        );

        $Department = new Department($dto->toArray());

        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($dto->toArray())
            ->andReturn($Department);

        $result = $this->service->create($dto);

        expect($result)->toBe($Department);
    });
});

// ─────────────────────────────────────────────
// update()
// ─────────────────────────────────────────────

describe('update()', function () {
    it('updates and returns a Department', function () {
        $id = (string) \Illuminate\Support\Str::uuid();
        $Department = new Department(['id' => $id, 'name' => 'Old Name']);
        $updated = new Department(['id' => $id, 'name' => 'New Name']);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($Department);

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($Department, ['name' => 'New Name'])
            ->andReturn($updated);

        $result = $this->service->update($id, ['name' => 'New Name']);

        expect($result->name)->toBe('New Name');
    });

    it('throws ModelNotFoundException when updating non-existent Department', function () {
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
    it('deletes a Department and returns true', function () {
        $id = (string) \Illuminate\Support\Str::uuid();
        $Department = new Department(['id' => $id]);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($Department);

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($Department)
            ->andReturn(true);

        $result = $this->service->delete($id);

        expect($result)->toBeTrue();
    });

    it('throws ModelNotFoundException when deleting non-existent Department', function () {
        $id = (string) \Illuminate\Support\Str::uuid();

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        expect(fn() => $this->service->delete($id))
            ->toThrow(ModelNotFoundException::class);
    });
});