<?php

namespace Database\Factories;

use App\Models\Anggota;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnggotaFactory extends Factory
{
    protected $model = Anggota::class;

    public function definition(): array
    {
        return [
            'nama' => fake('id_ID')->name(),
            'email' => fake()->unique()->safeEmail(),
            'no_telepon' => fake('id_ID')->phoneNumber(),
        ];
    }
}
