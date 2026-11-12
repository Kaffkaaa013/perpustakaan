<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        $bukuData = [
            [
                'judul' => 'Laskar Pelangi',
                'penulis' => 'Andrea Hirata',
                'tahun_terbit' => 2005,
                'kategori_nama' => 'Novel',
            ],
            [
                'judul' => 'Bumi Manusia',
                'penulis' => 'Pramoedya Ananta Toer',
                'tahun_terbit' => 1980,
                'kategori_nama' => 'Novel',
            ],
            [
                'judul' => 'Filosofi Teras',
                'penulis' => 'Henry Manampiring',
                'tahun_terbit' => 2018,
                'kategori_nama' => 'Pendidikan',
            ],
            [
                'judul' => 'Sapiens',
                'penulis' => 'Yuval Noah Harari',
                'tahun_terbit' => 2011,
                'kategori_nama' => 'Teknologi',
            ],
            [
                'judul' => 'Atomic Habits',
                'penulis' => 'James Clear',
                'tahun_terbit' => 2018,
                'kategori_nama' => 'Pendidikan',
            ],
        ];

        foreach ($bukuData as $data) {
            $kategori = Kategori::query()->firstOrCreate([
                'nama' => $data['kategori_nama'],
            ]);

            Buku::query()->updateOrCreate(
                ['judul' => $data['judul']],
                [
                    'penulis' => $data['penulis'],
                    'tahun_terbit' => $data['tahun_terbit'],
                    'kategori_id' => $kategori->id,
                ],
            );
        }
    }
}
