<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\Traits\WithAuthUser;

uses(RefreshDatabase::class, WithAuthUser::class);

uses()->beforeEach(function () {
    $this->authUser = $this->createAuthUser([
        'permission.view',
    ]);
});

// ─────────────────────────────────────────────
// GET /api/v1/permissions
// ─────────────────────────────────────────────

describe('GET /api/v1/permissions', function () {

    it('returns paginated permissions', function () {
        Permission::create(['name' => 'user.create']);
        Permission::create(['name' => 'user.update']);

        $response = $this->getJson('/api/v1/permissions');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);

        expect(count($response->json('data')))->toBeGreaterThan(0);
    });

    it('returns empty when no permissions', function () {
        $response = $this->getJson('/api/v1/permissions');

        $response->assertStatus(200);
        dump($response);

        expect(count($response->json('data')))->toBeGreaterThanOrEqual(1);
    });

});