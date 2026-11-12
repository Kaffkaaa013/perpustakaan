<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $siswa = User::query()->where('email', 'siswa@perpustakaan.test')->first();
        $bukus = Buku::query()->orderBy('id')->get();

        if ($siswa === null || $bukus->count() < 3) {
            return;
        }

        $anggota = Anggota::query()->firstOrCreate(
            ['email' => $siswa->email],
            [
                'nama' => $siswa->name,
                'no_telepon' => '-',
            ]
        );

        $samples = [
            [
                'buku_id' => $bukus[0]->id,
                'status' => 'menunggu',
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali' => null,
            ],
            [
                'buku_id' => $bukus[1]->id,
                'status' => 'dipinjam',
                'tanggal_pinjam' => now()->subDays(3)->toDateString(),
                'tanggal_kembali' => null,
            ],
            [
                'buku_id' => $bukus[2]->id,
                'status' => 'dikembalikan',
                'tanggal_pinjam' => now()->subDays(14)->toDateString(),
                'tanggal_kembali' => now()->subDays(7)->toDateString(),
            ],
        ];

        foreach ($samples as $sample) {
            Peminjaman::query()->firstOrCreate(
                [
                    'user_id' => $siswa->id,
                    'buku_id' => $sample['buku_id'],
                    'status' => $sample['status'],
                ],
                [
                    'anggota_id' => $anggota->id,
                    'tanggal_pinjam' => $sample['tanggal_pinjam'],
                    'tanggal_kembali' => $sample['tanggal_kembali'],
                ]
            );
        }
    }
}
