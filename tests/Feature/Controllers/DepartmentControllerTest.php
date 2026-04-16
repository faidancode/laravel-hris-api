<?php

use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\Traits\WithAuthUser;

uses(RefreshDatabase::class, WithAuthUser::class);

uses(MockeryPHPUnitIntegration::class)->beforeEach(function () {
    $this->authUser = $this->createAuthUser([
        'department.view',
        'department.create',
        'department.update',
        'department.delete',
    ]);
});

// ─────────────────────────────────────────────
// GET /api/v1/departments
// ─────────────────────────────────────────────

describe('GET /api/v1/departments', function () {
    it('returns paginated departments with 200', function () {
        Department::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/departments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'total',
                    'count',
                    'limit',
                    'currentPage',
                    'totalPages',
                    'hasNext',
                ],
            ]);
    });

    it('filters departments by search term', function () {
        $dept = Department::factory()->create(['name' => 'IT Department']);

        $response = $this->getJson('/api/v1/departments?search=IT');

        $response->assertStatus(200);
        $data = $response->json('data');
        expect(count($data))->toBe(1);
        expect($data[0]['name'])->toBe('IT Department');
    });

    it('respects limit parameter', function () {
        Department::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/departments?limit=5');

        $response->assertStatus(200);
        expect(count($response->json('data')))->toBe(5);
    });

    it('does not return soft-deleted departments', function () {
        Department::factory()->create(['name' => 'Deleted Role'])
            ->delete();

        $response = $this->getJson('/api/v1/departments?search=Deleted');

        $response->assertStatus(200);
        expect(count($response->json('data')))->toBe(0);
    });
});

// ─────────────────────────────────────────────
// POST /api/v1/departments
// ─────────────────────────────────────────────

describe('POST /api/v1/departments', function () {
    it('creates a department and returns 201', function () {

        $payload = [
            'name' => 'IT Department',
        ];

        $response = $this->postJson('/api/v1/departments', $payload);
        dump($response);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'IT Department']);
    });

    it('returns 422 when name is missing', function () {
        $department = Department::factory()->create();

        $response = $this->postJson('/api/v1/departments', [
            'department_id' => $department->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });
});

// ─────────────────────────────────────────────
// GET /api/v1/departments/{id}
// ─────────────────────────────────────────────

describe('GET /api/v1/departments/{id}', function () {
    it('returns a department with 200', function () {
        $department = Department::factory()->create();
        $department = Department::factory()->create();

        $response = $this->getJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $department->name]);
    });

    it('returns error for invalid UUID', function () {
        $response = $this->getJson('/api/v1/departments/not-a-uuid');

        $response->assertStatus(400)
            ->assertJsonFragment(['code' => 'INVALID_UUID']);
    });

    it('returns 404 for non-existent department', function () {
        $response = $this->getJson('/api/v1/departments/' . Str::uuid());

        $response->assertStatus(404)
            ->assertJsonFragment(['code' => 'DATA_NOT_FOUND']);
    });

    it('returns 404 for soft-deleted department', function () {
        $department = Department::factory()->create();
        $department = Department::factory()->create();
        $department->delete();

        $response = $this->getJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(404);
    });
});

// ─────────────────────────────────────────────
// PUT /api/v1/departments/{id}
// ─────────────────────────────────────────────

describe('PUT /api/v1/departments/{id}', function () {
    it('updates a department and returns 200', function () {
        $department = Department::factory()->create();
        $department = Department::factory()->create();

        $response = $this->putJson("/api/v1/departments/{$department->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Name']);
    });

    it('returns error for invalid UUID', function () {
        $response = $this->putJson('/api/v1/departments/not-a-uuid', ['name' => 'Test']);

        $response->assertStatus(400)
            ->assertJsonFragment(['code' => 'INVALID_UUID']);
    });

    it('returns 404 for non-existent department', function () {
        $response = $this->putJson('/api/v1/departments/' . Str::uuid(), ['name' => 'Test']);

        $response->assertStatus(404)
            ->assertJsonFragment(['code' => 'DATA_NOT_FOUND']);
    });
});

// ─────────────────────────────────────────────
// DELETE /api/v1/departments/{id}
// ─────────────────────────────────────────────

describe('DELETE /api/v1/departments/{id}', function () {
    it('soft-deletes a department and returns 200', function () {
        $department = Department::factory()->create();
        $department = Department::factory()->create();

        $response = $this->deleteJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('departments', ['id' => $department->id]);
    });

    it('returns error for invalid UUID', function () {
        $response = $this->deleteJson('/api/v1/departments/not-a-uuid');

        $response->assertStatus(400)
            ->assertJsonFragment(['code' => 'INVALID_UUID']);
    });

    it('returns 404 for non-existent department', function () {
        $response = $this->deleteJson('/api/v1/departments/' . Str::uuid());

        $response->assertStatus(404)
            ->assertJsonFragment(['code' => 'DATA_NOT_FOUND']);
    });

    it('returns 404 when deleting already soft-deleted department', function () {
        $department = Department::factory()->create();
        $department = Department::factory()->create();
        $department->delete();

        $response = $this->deleteJson("/api/v1/departments/{$department->id}");

        $response->assertStatus(404);
    });
});