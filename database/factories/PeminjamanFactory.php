<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanFactory extends Factory
{
    protected $model = Peminjaman::class;

    public function definition(): array
    {
        $tanggalPinjam = fake()->dateTimeBetween('-1 month', 'now');
        $tanggalKembali = fake()->boolean(70)
            ? (clone $tanggalPinjam)->modify('+'.rand(3, 14).' days')->format('Y-m-d')
            : null;

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'anggota_id' => Anggota::inRandomOrder()->first()?->id ?? Anggota::factory(),
            'buku_id' => Buku::inRandomOrder()->first()?->id ?? Buku::factory(),
            'tanggal_pinjam' => $tanggalPinjam->format('Y-m-d'),
            'tanggal_kembali' => $tanggalKembali,
            'status' => $tanggalKembali ? 'dikembalikan' : 'dipinjam',
        ];
    }
}
