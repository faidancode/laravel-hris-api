<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class LeavePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'leave.view',
            'leave.create',
            'leave.update',
            'leave.delete',
            'leave.approve',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo([
                'leave.view',
                'leave.create',
            ]);
        }

        $staff = Role::where('name', 'staff')->first();
        if ($staff) {
            $staff->givePermissionTo([
                'leave.view',
                'leave.create',
            ]);
        }
    }
}