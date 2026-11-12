<?php

namespace Database\Seeders;

use App\Models\Anggota;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        if (Anggota::query()->exists()) {
            return;
        }

        Anggota::factory()->count(3)->create();
    }
}
