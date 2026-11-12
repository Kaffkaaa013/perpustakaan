<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModulIvBukuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_menu_daftar_buku_mengarah_ke_halaman_web_bukan_api(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('href="'.url('/buku').'"', false);
        $response->assertDontSee('href="'.url('/api/buku').'"', false);
    }

    public function test_halaman_buku_dapat_diakses(): void
    {
        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->get('/buku');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Data Buku');
        $response->assertSee('Daftar Buku (Data Real-Time API)');
        $response->assertSee('form-buku');
    }

    public function test_get_api_buku_memetakan_atribut_melalui_resource(): void
    {
        $response = $this->getJson('/api/buku?all=true');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'kategori_id',
                        'judul',
                        'penulis',
                        'tahun_terbit',
                        'tahun',
                        'kategori' => [
                            'id',
                            'nama',
                        ],
                        'kategori_nama',
                    ],
                ],
            ]);
    }

    public function test_post_api_buku_berhasil_menambahkan_data(): void
    {
        $kategori = Kategori::first();

        $payload = [
            'kategori_id' => $kategori->id,
            'judul' => 'Buku Praktikum Modul IV',
            'penulis' => 'Mahasiswa PBWL',
            'tahun_terbit' => 2024,
        ];

        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->postJson('/api/buku', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.judul', 'Buku Praktikum Modul IV')
            ->assertJsonPath('data.penulis', 'Mahasiswa PBWL')
            ->assertJsonPath('data.tahun_terbit', 2024);

        $this->assertDatabaseHas('bukus', [
            'judul' => 'Buku Praktikum Modul IV',
            'penulis' => 'Mahasiswa PBWL',
            'tahun_terbit' => 2024,
        ]);
    }

    public function test_put_api_buku_berhasil_memperbarui_data(): void
    {
        $buku = Buku::first();
        $kategori = Kategori::first();

        $payload = [
            'kategori_id' => $kategori->id,
            'judul' => 'Judul Buku Telah Diperbarui',
            'penulis' => 'Penulis Telah Diperbarui',
            'tahun_terbit' => 2025,
        ];

        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->putJson('/api/buku/'.$buku->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $buku->id)
            ->assertJsonPath('data.judul', 'Judul Buku Telah Diperbarui');

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'judul' => 'Judul Buku Telah Diperbarui',
        ]);
    }

    public function test_delete_api_buku_berhasil_menghapus_data(): void
    {
        $buku = Buku::create([
            'kategori_id' => Kategori::first()->id,
            'judul' => 'Buku Sementara Untuk Dihapus',
            'penulis' => 'Penulis Sementara',
            'tahun_terbit' => 2022,
        ]);

        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->deleteJson('/api/buku/'.$buku->id);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('bukus', [
            'id' => $buku->id,
        ]);
    }

    public function test_validasi_error_422_pada_post_dan_put(): void
    {
        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $responsePost = $this->actingAs($admin)->postJson('/api/buku', []);
        $responsePost->assertStatus(422)
            ->assertJsonValidationErrors(['kategori_id', 'judul', 'penulis', 'tahun_terbit']);

        $buku = Buku::first();
        $responsePut = $this->actingAs($admin)->putJson('/api/buku/'.$buku->id, []);
        $responsePut->assertStatus(422)
            ->assertJsonValidationErrors(['kategori_id', 'judul', 'penulis', 'tahun_terbit']);
    }
}
