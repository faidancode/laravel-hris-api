<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role superadmin sudah ada (antisipasi jika RolePermissionSeeder belum dijalankan)
        $role = Role::firstOrCreate([
            'name' => 'superadmin',
            'guard_name' => 'api',
        ]);

        // 2. Buat atau Update User Superadmin Utama
        // Kita gunakan withoutEvents agar prosesnya cepat dan tidak memicu email/logic lain
        User::withoutEvents(function () use ($role) {
            $user = User::updateOrCreate(
                ['email' => 'superadmin@hris.test'], // Cari berdasarkan email ini
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password123'), // Password default
                    'email_verified_at' => now(),
                ]
            );

            // 3. Pasangkan Role ke User
            if (!$user->hasRole('superadmin')) {
                $user->assignRole($role);
            }
        });

        $this->command->info('Super Admin User created/updated: superadmin@hris.test');
    }
}