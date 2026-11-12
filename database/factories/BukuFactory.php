<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    protected $model = Buku::class;

    public function definition(): array
    {
        $judulBuku = [
            'Laskar Pelangi',
            'Bumi Manusia',
            'Filosofi Teras',
            'Sapiens: Riwayat Singkat Umat Manusia',
            'Atomic Habits',
            'Laut Bercerita',
            'Cantik Itu Luka',
            'Negeri 5 Menara',
        ];

        return [
            'kategori_id' => Kategori::inRandomOrder()->first()?->id ?? Kategori::factory(),
            'judul' => fake()->randomElement($judulBuku),
            'penulis' => fake('id_ID')->name(),
            'tahun_terbit' => fake()->numberBetween(1990, 2024),
        ];
    }
}
