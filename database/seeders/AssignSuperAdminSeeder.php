<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tentukan identifier (Bisa email atau UUID)
        // Anda bisa mengganti ini secara manual atau via ENV
        $identifier = 'user.pilihan@hris.test';

        // 2. Cari user berdasarkan email atau ID
        $user = User::where('email', $identifier)
            ->orWhere('id', $identifier)
            ->first();

        if (!$user) {
            $this->command->error("Gagal: User dengan email/ID '{$identifier}' tidak ditemukan di database.");
            return;
        }

        // 3. Pastikan role superadmin tersedia
        $role = Role::firstOrCreate([
            'name' => 'superadmin',
            'guard_name' => 'api'
        ]);

        // 4. Berikan role ke user tersebut
        if (!$user->hasRole('superadmin')) {
            $user->assignRole($role);
            $this->command->info("Berhasil! User {$user->email} sekarang memiliki akses Super Admin.");
        } else {
            $this->command->warn("User {$user->email} memang sudah menjadi Super Admin.");
        }
    }
}