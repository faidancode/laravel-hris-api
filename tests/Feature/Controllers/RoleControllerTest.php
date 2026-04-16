<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\Traits\WithAuthUser;

uses(RefreshDatabase::class, WithAuthUser::class);

uses()->beforeEach(function () {
    $this->authUser = $this->createAuthUser([
        'role.view',
        'role.create',
        'role.update',
        'role.delete',
        'role.assign-permission',
    ]);
});

// ─────────────────────────────────────────────
// GET /api/v1/roles
// ─────────────────────────────────────────────

describe('GET /api/v1/roles', function () {

    it('returns paginated roles with 200', function () {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);

        expect(count($response->json('data')))->toBeGreaterThan(0);
    });

});

// ─────────────────────────────────────────────
// POST /api/v1/roles
// ─────────────────────────────────────────────

describe('POST /api/v1/roles', function () {

    it('creates role successfully', function () {
        $response = $this->postJson('/api/v1/roles', [
            'name' => 'admin'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    });

    it('fails when name is missing', function () {
        $response = $this->postJson('/api/v1/roles', []);

        $response->assertStatus(500);
    });

});

// ─────────────────────────────────────────────
// PUT /roles/{id}/permissions
// ─────────────────────────────────────────────

describe('PUT /roles/{id}/permissions', function () {

    it('syncs permissions successfully', function () {
        $role = Role::create(['name' => 'admin']);
        Permission::create(['name' => 'user.create']);

        $response = $this->putJson("/api/v1/roles/{$role->id}/permissions", [
            'permissions' => ['user.create']
        ]);

        $response->assertStatus(200);

        expect($role->fresh()->permissions->pluck('name'))
            ->toContain('user.create');
    });

    it('fails with invalid permission', function () {
        $role = Role::create(['name' => 'admin']);

        $response = $this->putJson("/api/v1/roles/{$role->id}/permissions", [
            'permissions' => ['invalid.permission']
        ]);

        $response->assertStatus(422);
    });

});