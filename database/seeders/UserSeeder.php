<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (Role::query()->count() === 0) {
            $this->call(RolePermissionSeeder::class);
        }

        $users = [
            [
                'email' => 'admin@perpustakaan.test',
                'name' => 'Administrator Perpustakaan',
                'role' => 'admin',
            ],
            [
                'email' => 'petugas@perpustakaan.test',
                'name' => 'Petugas Perpustakaan',
                'role' => 'petugas',
            ],
            [
                'email' => 'siswa@perpustakaan.test',
                'name' => 'Siswa Pengguna',
                'role' => 'siswa',
            ],
        ];

        foreach ($users as $userData) {
            // Soal 5(c): Ganti password admin bawaan seeder
            $password = ($userData['role'] === 'admin') ? 'AdminPerpus2026!#' : 'password';

            $user = User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($password),
                ]
            );

            $user->syncRoles([$userData['role']]);
        }
    }
}
