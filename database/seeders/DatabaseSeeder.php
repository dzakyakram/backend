<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────────────────────
        $admin = User::create([
            'nama'     => 'Admin Sistem',
            'email'    => 'admin@nusantaramap.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
            'status'   => 'aktif',
        ]);

        $pengelola = User::create([
            'nama'     => 'Pengelola Konten',
            'email'    => 'pengelola@nusantaramap.id',
            'password' => Hash::make('pengelola123'),
            'role'     => 'pengelola',
            'status'   => 'aktif',
        ]);

        $user = User::create([
            'nama'     => 'Budi Santoso',
            'email'    => 'budi@mail.com',
            'password' => Hash::make('budi123'),
            'role'     => 'pengguna',
            'status'   => 'aktif',
        ]);

        // ── Sample Locations ──────────────────────────────────
        $locations = [
            ['nama' => 'Danau Toba',           'kat' => 'wisata',  'lat' => 2.6845,  'lng' => 98.8756, 'addr' => 'Toba Samosir, Sumatera Utara'],
            ['nama' => 'Warung Soto Deli',     'kat' => 'kuliner', 'lat' => 3.5952,  'lng' => 98.6722, 'addr' => 'Jl. Sisingamangaraja, Medan'],
            ['nama' => 'Hotel Aryaduta Medan', 'kat' => 'hotel',   'lat' => 3.5785,  'lng' => 98.6789, 'addr' => 'Jl. Kapten Maulana Lubis, Medan'],
            ['nama' => 'Masjid Raya Al Mashun','kat' => 'wisata',  'lat' => 3.5946,  'lng' => 98.6842, 'addr' => 'Jl. Sisingamangaraja, Medan'],
            ['nama' => 'Istana Maimoon',        'kat' => 'wisata',  'lat' => 3.5748,  'lng' => 98.6818, 'addr' => 'Jl. Brigjen Katamso, Medan'],
            ['nama' => 'Mie Aceh Titi Bobrok', 'kat' => 'kuliner', 'lat' => 3.5888,  'lng' => 98.6710, 'addr' => 'Jl. Puri, Medan'],
            ['nama' => 'Bukit Lawang',          'kat' => 'wisata',  'lat' => 3.5397,  'lng' => 98.1399, 'addr' => 'Bohorok, Langkat'],
            ['nama' => 'Restoran Tip Top',      'kat' => 'kuliner', 'lat' => 3.5868,  'lng' => 98.6802, 'addr' => 'Jl. Ahmad Yani, Medan'],
        ];

        foreach ($locations as $loc) {
            Location::create([
                'nama'           => $loc['nama'],
                'deskripsi'      => 'Deskripsi ' . $loc['nama'],
                'kategori'       => $loc['kat'],
                'alamat'         => $loc['addr'],
                'latitude'       => $loc['lat'],
                'longitude'      => $loc['lng'],
                'user_id'        => $user->id,
                'status'         => 'approved',
                'dimoderasi_oleh'=> $pengelola->id,
                'dimoderasi_at'  => now(),
            ]);
        }

        $this->command->info('✅ Seeder selesai: admin, pengelola, pengguna, dan 8 lokasi sample.');
    }
}
