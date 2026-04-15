<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\Traits\WithAuthUser;

uses(RefreshDatabase::class, WithAuthUser::class);


uses(MockeryPHPUnitIntegration::class)->beforeEach(function () {
     $this->authUser = $this->createAuthUser([
        'user.view',
        'user.create',
        'user.update',
        'user.delete',
    ]);
});


// ─────────────────────────────────────────────
// GET /api/v1/users
// ─────────────────────────────────────────────

describe('GET /api/v1/users', function () {

    it('returns paginated users with 200', function () {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'total',
                    'count',
                    'limit',
                    'currentPage',
                    'totalPages',
                    'hasNext'
                ],
            ]);

        expect(count($response->json('data')))->toBeGreaterThan(0);
    });


    it('filters users by name search term', function () {
        User::factory()->create(['name' => 'Admin User']);
        User::factory()->create(['name' => 'Staff User']);

        $response = $this->getJson('/api/v1/users?search=Admin');

        $response->assertStatus(200);

        $data = $response->json('data');

        expect(count($data))->toBe(1);
        expect($data[0]['name'])->toBe('Admin User');
    });


    it('respects pagination limit parameter', function () {
        User::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/users?limit=3');

        $response->assertStatus(200);

        expect(count($response->json('data')))->toBe(3);
        expect($response->json('meta.limit'))->toBe(3);
    });


    it('returns 403 if user has no permission', function () {
        // user tanpa permission
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(403);
    });


    it('returns empty data if no users found', function () {
        $response = $this->getJson('/api/v1/users?search=notfound');

        $response->assertStatus(200);

        expect($response->json('data'))->toBe([]);
    });

});

// ─────────────────────────────────────────────
// POST /api/v1/users (Store)
// ─────────────────────────────────────────────

describe('POST /api/v1/users', function () {
    it('creates a new user successfully with hashed password', function () {
        Role::create(['name' => 'admin', 'guard_name' => 'api']);

        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'role' => 'admin'
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('email', 'john@example.com');

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);

        $user = User::where('email', 'john@example.com')->first();
        expect(\Illuminate\Support\Facades\Hash::check('secret123', $user->password))->toBeTrue();
        expect($user->hasRole('admin'))->toBeTrue();
    });

    it('returns 400 if email is already taken', function () {
        User::factory()->create(['email' => 'existing@example.com']);

        $payload = [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Email sudah digunakan.']);
    });
});

// ─────────────────────────────────────────────
// GET /api/v1/users/{id} (Show)
// ─────────────────────────────────────────────

describe('GET /api/v1/users/{id}', function () {
    it('returns user details if exists', function () {
        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email);
    });

    it('returns 404 if user not found', function () {
        $response = $this->getJson("/api/v1/users/non-existent-id");

        $response->assertStatus(404)
            ->assertJson(['message' => 'User tidak ditemukan.']);
    });
});

// ─────────────────────────────────────────────
// PUT /api/v1/users/{id} (Update)
// ─────────────────────────────────────────────

describe('PUT /api/v1/users/{id}', function () {
    it('updates user information and roles', function () {
        Role::create(['name' => 'editor', 'guard_name' => 'api']);
        $user = User::factory()->create(['name' => 'Old Name']);

        $payload = [
            'name' => 'Updated Name',
            'role' => 'editor'
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('name', 'Updated Name');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name'
        ]);

        expect($user->refresh()->hasRole('editor'))->toBeTrue();
    });

    it('updates password only if provided', function () {
        $user = User::factory()->create();
        $oldPassword = $user->password;

        $payload = ['password' => 'new-secure-password'];

        $response = $this->putJson("/api/v1/users/{$user->id}", $payload);

        $response->assertStatus(200);
        $user->refresh();
        expect($user->password)->not->toBe($oldPassword);
        expect(\Illuminate\Support\Facades\Hash::check('new-secure-password', $user->password))->toBeTrue();
    });
});

// ─────────────────────────────────────────────
// DELETE /api/v1/users/{id} (Destroy)
// ─────────────────────────────────────────────

describe('DELETE /api/v1/users/{id}', function () {
    it('deletes user and returns 204', function () {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    });
});