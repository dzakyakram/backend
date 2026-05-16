<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama agar tidak duplikat
        DB::table('users')->whereIn('email', [
            'admin@nusantaramap.id',
            'pengelola@nusantaramap.id',
            'pengguna@nusantaramap.id',
        ])->delete();

        // Admin
        User::create([
            'nama'     => 'Admin Sistem',
            'email'    => 'admin@nusantaramap.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
            'status'   => 'aktif',
        ]);

        // Pengelola
        User::create([
            'nama'     => 'Pengelola Konten',
            'email'    => 'pengelola@nusantaramap.id',
            'password' => Hash::make('pengelola123'),
            'role'     => 'pengelola',
            'status'   => 'aktif',
        ]);

        // Pengguna (untuk testing Flutter)
        User::create([
            'nama'     => 'Pengguna Test',
            'email'    => 'pengguna@nusantaramap.id',
            'password' => Hash::make('pengguna123'),
            'role'     => 'pengguna',
            'status'   => 'aktif',
        ]);

        $this->command->info('✅ User seeder selesai:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['admin',     'admin@nusantaramap.id',     'admin123'],
                ['pengelola', 'pengelola@nusantaramap.id', 'pengelola123'],
                ['pengguna',  'pengguna@nusantaramap.id',  'pengguna123'],
            ]
        );
    }
}
