<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

trait WithAuthUser
{
    protected function setupPermissions(array $permissions = []): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'api',
            ]);
        }
    }

    protected function createAuthUser(array $permissions = []): User
    {
        $user = User::factory()->create([
            'id' => (string) Str::uuid(),
        ]);

        if (!empty($permissions)) {
            $this->setupPermissions($permissions);
            $user->givePermissionTo($permissions);
        }

        $this->actingAs($user, 'api');

        return $user;
    }
}