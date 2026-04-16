<?php

use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Services\RoleService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(MockeryPHPUnitIntegration::class)->beforeEach(function () {
    $this->repositoryMock = Mockery::mock(RoleRepositoryInterface::class);
    $this->permissionRegistrarMock = Mockery::mock(PermissionRegistrar::class);

    $this->service = new RoleService($this->repositoryMock, $this->permissionRegistrarMock);
});

// ─────────────────────────────────────────────
// paginate()
// ─────────────────────────────────────────────

describe('paginate()', function () {
    it('returns paginator from repository', function () {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repositoryMock
            ->shouldReceive('paginate')
            ->once()
            ->with(['search' => 'admin'])
            ->andReturn($paginator);

        $result = $this->service->paginate(['search' => 'admin']);

        expect($result)->toBe($paginator);
    });

    it('returns empty paginator when no data', function () {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repositoryMock
            ->shouldReceive('paginate')
            ->once()
            ->andReturn($paginator);

        $result = $this->service->paginate([]);

        expect($result)->toBe($paginator);
    });
});

// ─────────────────────────────────────────────
// findOrFail()
// ─────────────────────────────────────────────

describe('findOrFail()', function () {
    it('returns role when found', function () {
        $role = Mockery::mock(Role::class)->makePartial();
        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->andReturn($role);

        $result = $this->service->findOrFail('id');

        expect($result)->toBe($role);
    });

    it('throws exception when not found', function () {
        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        $this->service->findOrFail('invalid');
    })->throws(ModelNotFoundException::class);
});

// ─────────────────────────────────────────────
// syncPermissions()
// ─────────────────────────────────────────────

describe('syncPermissions()', function () {
    it('syncs permissions successfully', function () {
        $role = Mockery::mock(Role::class)->makePartial();

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->andReturn($role);

        $role->shouldReceive('syncPermissions')
            ->once()
            ->with(['user.create']);

        $role->shouldReceive('load')
            ->once()
            ->with('permissions')
            ->andReturnSelf();

        $this->permissionRegistrarMock
            ->shouldReceive('forgetCachedPermissions')
            ->once();

        $result = $this->service->syncPermissions('id', ['user.create']);

        expect($result)->toBe($role);
    });

    it('throws exception when role not found', function () {
        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->andReturn(null);

        $this->service->syncPermissions('invalid', []);
    })->throws(ModelNotFoundException::class);
});