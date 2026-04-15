<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cache Spatie (Sangat penting agar sinkronisasi tidak crash)
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Definisi Modul dan Action
        $modules = ['user', 'position', 'department', 'employee'];
        $actions = ['view', 'create', 'update', 'delete'];

        // Generate dan Simpan Permissions
        $allPermissionNames = [];
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $name = "{$module}.{$action}";
                $allPermissionNames[] = $name;

                Permission::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'api',
                ]);
            }
        }

        // 3. Setup Roles
        // Superadmin (Semua akses)
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'api']);
        $superadmin->syncPermissions($allPermissionNames);

        // Admin (Contoh: Semua akses juga, atau bisa dibatasi nanti)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $admin->syncPermissions($allPermissionNames);

        // Staff (Akses terbatas)
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'api']);
        $staffPermissions = [
            'user.view',
            'position.view',
            'department.view',
            'employee.view',
        ];
        $staff->syncPermissions($staffPermissions);

        $this->command->info('Roles and Permissions seeded successfully!');
    }
}