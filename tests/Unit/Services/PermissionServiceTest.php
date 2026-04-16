<?php

use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Services\PermissionService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

uses(MockeryPHPUnitIntegration::class)->beforeEach(function () {
    $this->repositoryMock = Mockery::mock(PermissionRepositoryInterface::class);
    $this->service = new PermissionService($this->repositoryMock);
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
            ->with(['search' => 'user'])
            ->andReturn($paginator);

        $result = $this->service->paginate(['search' => 'user']);

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