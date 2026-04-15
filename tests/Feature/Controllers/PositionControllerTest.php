<?php

use App\Models\Department;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\Traits\WithAuthUser;

uses(RefreshDatabase::class, WithAuthUser::class);

uses(MockeryPHPUnitIntegration::class)->beforeEach(function () {
    $this->authUser = $this->createAuthUser([
        'position.view',
        'position.create',
        'position.update',
        'position.delete',
    ]);
});

// ─────────────────────────────────────────────
// GET /api/v1/positions
// ─────────────────────────────────────────────

describe('GET /api/v1/positions', function () {
    it('returns paginated positions with 200', function () {
        $department = Department::factory()->create();
        Position::factory()->count(5)->create(['department_id' => $department->id]);

        $response = $this->getJson('/api/v1/positions');

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

    it('filters positions by search term', function () {
        $department = Department::factory()->create();
        Position::factory()->create(['name' => 'Backend Engineer', 'department_id' => $department->id]);
        Position::factory()->create(['name' => 'Product Manager', 'department_id' => $department->id]);

        $response = $this->getJson('/api/v1/positions?search=Backend');

        $response->assertStatus(200);
        $data = $response->json('data');
        expect(count($data))->toBe(1);
        expect($data[0]['name'])->toBe('Backend Engineer');
    });

    it('respects limit parameter', function () {
        $department = Department::factory()->create();
        Position::factory()->count(15)->create(['department_id' => $department->id]);

        $response = $this->getJson('/api/v1/positions?limit=5');

        $response->assertStatus(200);
        expect(count($response->json('data')))->toBe(5);
    });

    it('does not return soft-deleted positions', function () {
        $department = Department::factory()->create();
        Position::factory()->create(['name' => 'Deleted Role', 'department_id' => $department->id])
            ->delete();

        $response = $this->getJson('/api/v1/positions?search=Deleted');

        $response->assertStatus(200);
        expect(count($response->json('data')))->toBe(0);
    });
});

// ─────────────────────────────────────────────
// POST /api/v1/positions
// ─────────────────────────────────────────────

describe('POST /api/v1/positions', function () {
    it('creates a position and returns 201', function () {
        $department = Department::factory()->create();

        $payload = [
            'name' => 'Software Engineer',
            'department_id' => $department->id,
        ];

        $response = $this->postJson('/api/v1/positions', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Software Engineer']);
    });

    it('returns 422 when name is missing', function () {
        $department = Department::factory()->create();

        $response = $this->postJson('/api/v1/positions', [
            'department_id' => $department->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });

    it('returns 422 when department_id is not a valid UUID', function () {
        $response = $this->postJson('/api/v1/positions', [
            'name' => 'Engineer',
            'department_id' => 'not-a-uuid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['department_id']);
    });

    it('returns 422 when department_id does not exist', function () {
        $response = $this->postJson('/api/v1/positions', [
            'name' => 'Engineer',
            'department_id' => (string) Str::uuid(),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['department_id']);
    });
});

// ─────────────────────────────────────────────
// GET /api/v1/positions/{id}
// ─────────────────────────────────────────────

describe('GET /api/v1/positions/{id}', function () {
    it('returns a position with 200', function () {
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $response = $this->getJson("/api/v1/positions/{$position->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $position->name]);
    });

    it('returns error for invalid UUID', function () {
        $response = $this->getJson('/api/v1/positions/not-a-uuid');

        $response->assertStatus(400)
            ->assertJsonFragment(['code' => 'INVALID_UUID']);
    });

    it('returns 404 for non-existent position', function () {
        $response = $this->getJson('/api/v1/positions/' . Str::uuid());

        $response->assertStatus(404)
            ->assertJsonFragment(['code' => 'DATA_NOT_FOUND']);
    });

    it('returns 404 for soft-deleted position', function () {
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);
        $position->delete();

        $response = $this->getJson("/api/v1/positions/{$position->id}");

        $response->assertStatus(404);
    });
});

// ─────────────────────────────────────────────
// PUT /api/v1/positions/{id}
// ─────────────────────────────────────────────

describe('PUT /api/v1/positions/{id}', function () {
    it('updates a position and returns 200', function () {
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $response = $this->putJson("/api/v1/positions/{$position->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Name']);
    });

    it('returns error for invalid UUID', function () {
        $response = $this->putJson('/api/v1/positions/not-a-uuid', ['name' => 'Test']);

        $response->assertStatus(400)
            ->assertJsonFragment(['code' => 'INVALID_UUID']);
    });

    it('returns 404 for non-existent position', function () {
        $response = $this->putJson('/api/v1/positions/' . Str::uuid(), ['name' => 'Test']);

        $response->assertStatus(404)
            ->assertJsonFragment(['code' => 'DATA_NOT_FOUND']);
    });

    it('returns 422 when department_id is not a valid UUID', function () {
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $response = $this->putJson("/api/v1/positions/{$position->id}", [
            'department_id' => 'bad-uuid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['department_id']);
    });
});

// ─────────────────────────────────────────────
// DELETE /api/v1/positions/{id}
// ─────────────────────────────────────────────

describe('DELETE /api/v1/positions/{id}', function () {
    it('soft-deletes a position and returns 200', function () {
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $response = $this->deleteJson("/api/v1/positions/{$position->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('positions', ['id' => $position->id]);
    });

    it('returns error for invalid UUID', function () {
        $response = $this->deleteJson('/api/v1/positions/not-a-uuid');

        $response->assertStatus(400)
            ->assertJsonFragment(['code' => 'INVALID_UUID']);
    });

    it('returns 404 for non-existent position', function () {
        $response = $this->deleteJson('/api/v1/positions/' . Str::uuid());

        $response->assertStatus(404)
            ->assertJsonFragment(['code' => 'DATA_NOT_FOUND']);
    });

    it('returns 404 when deleting already soft-deleted position', function () {
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);
        $position->delete();

        $response = $this->deleteJson("/api/v1/positions/{$position->id}");

        $response->assertStatus(404);
    });
});