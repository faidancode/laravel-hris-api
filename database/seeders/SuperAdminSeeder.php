<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clear Cache Spatie di awal untuk menghindari konflik memori
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Definisi Permissions secara kolektif
        $permissions = [
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
        ];

        // Buat permission menggunakan map untuk efisiensi
        collect($permissions)->each(function ($permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        });

        // 3. Buat/Update Role
        $role = Role::firstOrCreate([
            'name' => 'superadmin',
            'guard_name' => 'api',
        ]);

        // Sync permissions ke role
        $role->syncPermissions($permissions);

        // 4. Buat User dengan proteksi terhadap Model Events (untuk mencegah infinite loop/memory leak)
        // withoutEvents mencegah pengiriman email verifikasi otomatis atau trigger observer saat seeding
        User::withoutEvents(function () use ($role) {
            $user = User::updateOrCreate(
                ['email' => 'superadmin@hris.test'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role ke user
            if (!$user->hasRole('superadmin')) {
                $user->assignRole($role);
            }
        });

        $this->command->info('Success: Super Admin seeded without memory exhaustion.');
    }
}