<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = ['Novel', 'Pendidikan', 'Teknologi', 'Sejarah', 'Sains'];

        foreach ($kategoris as $nama) {
            Kategori::query()->firstOrCreate(['nama' => $nama]);
        }
    }
}
