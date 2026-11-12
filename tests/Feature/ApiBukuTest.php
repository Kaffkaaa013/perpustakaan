<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiBukuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_api_buku_index_returns_paginated_list(): void
    {
        $response = $this->getJson('/api/buku');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'kategori_id',
                        'judul',
                        'penulis',
                        'tahun_terbit',
                        'kategori',
                    ],
                ],
                'per_page',
                'total',
            ]);

        $this->assertEquals(10, $response->json('per_page'));
    }

    public function test_api_buku_index_with_cari_filter(): void
    {
        $response = $this->getJson('/api/buku?cari=Pelangi');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertStringContainsString('Pelangi', $data[0]['judul']);
    }

    public function test_api_buku_index_with_kategori_filter(): void
    {
        $kategori = Kategori::where('nama', 'Novel')->first();

        // Filter dengan nama kategori
        $response = $this->getJson('/api/buku?kategori=Novel');
        $response->assertStatus(200);
        foreach ($response->json('data') as $item) {
            $this->assertEquals('Novel', $item['kategori']['nama']);
        }

        // Filter dengan ID kategori
        $responseId = $this->getJson('/api/buku?kategori='.$kategori->id);
        $responseId->assertStatus(200);
        foreach ($responseId->json('data') as $item) {
            $this->assertEquals($kategori->id, $item['kategori_id']);
        }
    }

    public function test_api_buku_index_with_tahun_filter(): void
    {
        $response = $this->getJson('/api/buku?tahun=2018');

        $response->assertStatus(200);
        foreach ($response->json('data') as $item) {
            $this->assertEquals(2018, $item['tahun_terbit']);
        }
    }

    public function test_api_buku_index_has_query_string_pagination(): void
    {
        $response = $this->getJson('/api/buku?cari=Pelangi&page=1');

        $response->assertStatus(200);
        $firstPageUrl = $response->json('first_page_url');
        $this->assertStringContainsString('cari=Pelangi', $firstPageUrl);
    }

    public function test_api_buku_store_validation_errors(): void
    {
        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->postJson('/api/buku', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kategori_id', 'judul', 'penulis', 'tahun_terbit']);

        // Periksa pesan error bahasa Indonesia
        $errors = $response->json('errors');
        $this->assertEquals('Kategori wajib dipilih.', $errors['kategori_id'][0]);
        $this->assertEquals('Judul buku wajib diisi.', $errors['judul'][0]);
        $this->assertEquals('Penulis buku wajib diisi.', $errors['penulis'][0]);
        $this->assertEquals('Tahun terbit wajib diisi.', $errors['tahun_terbit'][0]);
    }

    public function test_api_buku_store_success(): void
    {
        $kategori = Kategori::first();

        $payload = [
            'kategori_id' => $kategori->id,
            'judul' => 'Clean Code Indonesia',
            'penulis' => 'Robert C. Martin',
            'tahun_terbit' => 2008,
        ];

        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->postJson('/api/buku', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.judul', 'Clean Code Indonesia')
            ->assertJsonPath('data.kategori.id', $kategori->id);

        $this->assertDatabaseHas('bukus', [
            'judul' => 'Clean Code Indonesia',
            'penulis' => 'Robert C. Martin',
            'tahun_terbit' => 2008,
        ]);
    }

    public function test_api_buku_show_success(): void
    {
        $buku = Buku::first();

        $response = $this->getJson('/api/buku/'.$buku->id);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $buku->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'kategori_id',
                    'judul',
                    'penulis',
                    'tahun_terbit',
                    'kategori',
                    'peminjamans_count',
                ],
            ]);
    }

    public function test_api_buku_show_not_found(): void
    {
        $response = $this->getJson('/api/buku/99999');

        $response->assertStatus(404);
    }

    public function test_api_buku_update_success(): void
    {
        $buku = Buku::first();
        $kategori = Kategori::first();

        $payload = [
            'kategori_id' => $kategori->id,
            'judul' => 'Judul Buku Diperbarui',
            'penulis' => 'Penulis Baru',
            'tahun_terbit' => 2023,
        ];

        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->putJson('/api/buku/'.$buku->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.judul', 'Judul Buku Diperbarui');

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'judul' => 'Judul Buku Diperbarui',
        ]);
    }

    public function test_api_buku_destroy_success(): void
    {
        $buku = Buku::create([
            'kategori_id' => Kategori::first()->id,
            'judul' => 'Buku Akan Dihapus',
            'penulis' => 'Tester',
            'tahun_terbit' => 2021,
        ]);

        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->deleteJson('/api/buku/'.$buku->id);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('bukus', [
            'id' => $buku->id,
        ]);
    }

    public function test_rekap_buku_per_kategori(): void
    {
        $response = $this->getJson('/api/rekap/buku-per-kategori');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nama',
                        'bukus_count',
                    ],
                ],
            ]);
    }

    public function test_rekap_peminjaman_per_buku(): void
    {
        $response1 = $this->getJson('/api/rekap/pemminjaman-per-buku');
        $response1->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'judul',
                        'peminjamans_count',
                        'kategori',
                    ],
                ],
            ]);

        $response2 = $this->getJson('/api/rekap/peminjaman-per-buku');
        $response2->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
