<?php

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\UserService;
use App\DTOs\UserDto;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

uses(MockeryPHPUnitIntegration::class)->beforeEach(function () {
    $this->repositoryMock = Mockery::mock(UserRepositoryInterface::class);
    $this->service = new UserService($this->repositoryMock);
});

// ─────────────────────────────────────────────
// paginate()
// ─────────────────────────────────────────────

describe('paginate()', function () {
    it('returns a paginator from repository with filters', function () {
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $filters = ['role' => 'admin', 'search' => 'John'];

        $this->repositoryMock
            ->shouldReceive('paginate')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $result = $this->service->paginate($filters);

        expect($result)->toBe($paginator);
    });

    it('returns empty paginator when no users match filters', function () {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repositoryMock
            ->shouldReceive('paginate')
            ->once()
            ->andReturn($paginator);

        $result = $this->service->paginate(['search' => 'non_existent_user']);

        expect($result)->toBe($paginator);
    });
});

// ─────────────────────────────────────────────
// getById()
// ─────────────────────────────────────────────

describe('getById()', function () {
    it('returns user when ID exists', function () {
        $id = 'uuid-123';
        $user = new User(['id' => $id, 'name' => 'John Doe']);

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($user);

        $result = $this->service->getById($id);

        expect($result)->toBe($user);
    });

    it('throws Exception when user is not found', function () {
        $id = 'non-existent-id';

        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        expect(fn() => $this->service->getById($id))
            ->toThrow(Exception::class, "User tidak ditemukan.");
    });
});

// ─────────────────────────────────────────────
// create()
// ─────────────────────────────────────────────

describe('create()', function () {
    it('successfully creates a user with hashed password and role', function () {
        $dto = new UserDto(
            name: 'Test Name',
            email: 'test@example.com',
            password: 'secret123',
            role: 'editor'
        );

        $user = Mockery::mock(User::class)->makePartial();

        // Check if email exists
        $this->repositoryMock
            ->shouldReceive('existsByEmail')
            ->once()
            ->with($dto->email)
            ->andReturn(false);

        // Expect create call (password should be hashed)
        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($dto) {
                return $arg['email'] === $dto->email &&
                    Hash::check($dto->password, $arg['password']);
            }))
            ->andReturn($user);

        // Expect role assignment
        $user->shouldReceive('assignRole')
            ->once()
            ->with('editor');

        $result = $this->service->create($dto);

        expect($result)->toBe($user);
    });

    it('throws exception if email already exists', function () {
        $dto = new UserDto(name: 'Test', email: 'existing@example.com', password: '123');

        $this->repositoryMock
            ->shouldReceive('existsByEmail')
            ->once()
            ->andReturn(true);

        expect(fn() => $this->service->create($dto))
            ->toThrow(Exception::class, "Email sudah digunakan.");
    });

    it('creates user without role if role is not provided', function () {
        $dto = new UserDto(name: 'Test', email: 'norole@example.com', password: '123');
        $user = Mockery::mock(User::class)->makePartial();

        $this->repositoryMock->shouldReceive('existsByEmail')->andReturn(false);
        $this->repositoryMock->shouldReceive('create')->andReturn($user);

        $user->shouldNotReceive('assignRole');

        $this->service->create($dto);
    });
});

// ─────────────────────────────────────────────
// update()
// ─────────────────────────────────────────────

describe('update()', function () {
    it('hashes password only if it is provided in data', function () {
        $id = 'uuid-123';
        $dto = new UserDto(name: 'New Name', email: 'test@example.com', password: 'new-pass');
        $user = Mockery::mock(User::class)->makePartial();

        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($id, Mockery::on(function ($arg) {
                return Hash::check('new-pass', $arg['password']);
            }))
            ->andReturn($user);

        $this->service->update($id, $dto);
    });

    it('syncs roles if role key is present', function () {
        $id = 'uuid-123';
        $dto = new UserDto(name: 'Test', email: 'test@example.com');
        $user = Mockery::mock(User::class)->makePartial();

        $this->repositoryMock
            ->shouldReceive('update')
            ->andReturn($user);

        $user->shouldReceive('syncRoles')
            ->once()
            ->with(['admin']);

        $this->service->update($id, $dto);
    });

    it('does not sync roles if role key is missing', function () {
        $id = 'uuid-123';
        $dto = new UserDto(name: 'Only Name', email: 'test@example.com');
        $user = Mockery::mock(User::class)->makePartial();

        $this->repositoryMock->shouldReceive('update')->andReturn($user);
        $user->shouldNotReceive('syncRoles');

        $this->service->update($id, $dto);
    });
});

// ─────────────────────────────────────────────
// delete()
// ─────────────────────────────────────────────

describe('delete()', function () {
    it('calls repository delete method', function () {
        $id = 'uuid-123';

        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);

        $this->service->delete($id);

        // Assertions are handled by Mockery's shouldReceive once()
    });
});