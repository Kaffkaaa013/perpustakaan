<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ModulVRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->withoutMiddleware([ValidateCsrfToken::class]);
    }

    /**
     * Skenario 1: Guest yang belum login diarahkan ke halaman login saat mengakses dashboard.
     */
    public function test_01_guest_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertRedirect('/login');

        $this->get('/kelola-buku')->assertRedirect('/login');
        $this->get('/peminjaman')->assertRedirect('/login');
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/laporan')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }

    /**
     * Skenario 2: Pendaftaran akun baru (publik) secara otomatis mendapatkan role siswa.
     */
    public function test_02_guest_can_register_as_siswa_only(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Siswa Baru',
            'email' => 'budi.baru@perpustakaan.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'budi.baru@perpustakaan.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('petugas'));
    }

    /**
     * Skenario 3: Pendaftar publik tidak dapat memanipulasi form untuk langsung menjadi admin.
     */
    public function test_03_registered_user_cannot_register_as_admin_directly(): void
    {
        $this->post('/register', [
            'name' => 'Attacker User',
            'email' => 'attacker@perpustakaan.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'attacker@perpustakaan.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertFalse($user->hasRole('admin'));
    }

    /**
     * Skenario 4: Pengguna dapat login dengan kredensial yang valid.
     */
    public function test_04_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@perpustakaan.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /**
     * Skenario 5: Pengguna tidak dapat login dengan kata sandi salah.
     */
    public function test_05_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@perpustakaan.test',
            'password' => 'sandi-salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Skenario 6: Admin dapat mengakses Dashboard Admin.
     */
    public function test_06_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('email', 'admin@perpustakaan.test')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator');
    }

    /**
     * Skenario 7: Petugas dapat mengakses Dashboard Petugas.
     */
    public function test_07_petugas_can_access_petugas_dashboard(): void
    {
        $petugas = User::where('email', 'petugas@perpustakaan.test')->first();

        $response = $this->actingAs($petugas)->get('/petugas/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Petugas');
    }

    /**
     * Skenario 8: Siswa dapat mengakses Dashboard Siswa.
     */
    public function test_08_siswa_can_access_siswa_dashboard(): void
    {
        $siswa = User::where('email', 'siswa@perpustakaan.test')->first();

        $response = $this->actingAs($siswa)->get('/siswa/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Siswa');
    }

    /**
     * Skenario 9: Petugas dilarang mengakses Dashboard Admin (HTTP 403 Forbidden).
     */
    public function test_09_petugas_cannot_access_admin_dashboard_returns_403(): void
    {
        $petugas = User::where('email', 'petugas@perpustakaan.test')->first();

        $response = $this->actingAs($petugas)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Skenario 10: Siswa dilarang mengakses Dashboard Admin dan Petugas (HTTP 403 Forbidden).
     */
    public function test_10_siswa_cannot_access_petugas_or_admin_dashboard_returns_403(): void
    {
        $siswa = User::where('email', 'siswa@perpustakaan.test')->first();

        $responseAdmin = $this->actingAs($siswa)->get('/admin/dashboard');
        $responseAdmin->assertStatus(403);

        $responsePetugas = $this->actingAs($siswa)->get('/petugas/dashboard');
        $responsePetugas->assertStatus(403);
    }

    /**
     * Skenario 11: Admin dapat mengelola data pengguna dan mengubah role pengguna.
     */
    public function test_11_admin_can_manage_users_and_roles(): void
    {
        $admin = User::where('email', 'admin@perpustakaan.test')->first();
        $targetUser = User::where('email', 'siswa@perpustakaan.test')->first();

        // Admin melihat daftar user
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Kelola Pengguna');

        // Admin mengubah role siswa menjadi petugas
        $responseRole = $this->actingAs($admin)->post("/admin/users/{$targetUser->id}/role", [
            'role' => 'petugas',
        ]);
        $responseRole->assertRedirect();
        $this->assertTrue($targetUser->fresh()->hasRole('petugas'));
    }

    /**
     * Skenario 12: Petugas dan Admin dapat mengelola sirkulasi peminjaman.
     */
    public function test_12_petugas_and_admin_can_manage_peminjaman(): void
    {
        $petugas = User::where('email', 'petugas@perpustakaan.test')->first();
        $peminjaman = Peminjaman::first();

        $response = $this->actingAs($petugas)->get('/peminjaman');
        $response->assertStatus(200);
        $response->assertSee('Sirkulasi Peminjaman');

        // Petugas memproses persetujuan jika ada peminjaman berstatus diajukan
        if ($peminjaman) {
            $responseApprove = $this->actingAs($petugas)->post("/peminjaman/{$peminjaman->id}/setujui");
            $responseApprove->assertRedirect();
        }
    }

    /**
     * Skenario 13: Siswa dapat mengajukan peminjaman buku dan melihat riwayat pribadi.
     */
    public function test_13_siswa_can_apply_loan_and_view_own_history(): void
    {
        $siswa = User::where('email', 'siswa@perpustakaan.test')->first();
        $buku = Buku::first();

        // Akses halaman formulir pinjam
        $responseForm = $this->actingAs($siswa)->get('/peminjaman/ajukan');
        $responseForm->assertStatus(200);

        // Ajukan peminjaman buku
        $responseSubmit = $this->actingAs($siswa)->post('/peminjaman/ajukan', [
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
        ]);
        $responseSubmit->assertRedirect('/riwayat-peminjaman');

        // Lihat riwayat peminjaman sendiri
        $responseRiwayat = $this->actingAs($siswa)->get('/riwayat-peminjaman');
        $responseRiwayat->assertStatus(200);
        $responseRiwayat->assertSee($buku->judul);
    }

    /**
     * Skenario 14: Siswa dilarang melihat detail transaksi peminjaman milik siswa lain (HTTP 403 Forbidden).
     */
    public function test_14_siswa_cannot_view_other_student_loan_detail_returns_403(): void
    {
        $siswaA = User::where('email', 'siswa@perpustakaan.test')->first();
        $siswaB = User::factory()->create();
        $siswaB->assignRole('siswa');

        $buku = Buku::first();
        $anggota = Anggota::first();
        $pinjamanSiswaB = Peminjaman::create([
            'user_id' => $siswaB->id,
            'anggota_id' => $anggota?->id ?? 1,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'dipinjam',
        ]);

        // Siswa A mencoba membuka detail riwayat milik Siswa B
        $response = $this->actingAs($siswaA)->get("/riwayat-peminjaman/{$pinjamanSiswaB->id}");
        $response->assertStatus(403);
    }

    /**
     * Skenario 15: Pengguna terautentikasi dapat logout dan session terhapus.
     */
    public function test_15_authenticated_user_can_logout_and_session_cleared(): void
    {
        $siswa = User::where('email', 'siswa@perpustakaan.test')->first();

        $response = $this->actingAs($siswa)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();

        $protected = $this->get('/admin/dashboard');
        $protected->assertRedirect('/login');
    }

    public function test_16_petugas_cannot_manage_users_or_roles(): void
    {
        $petugas = User::where('email', 'petugas@perpustakaan.test')->first();

        $this->actingAs($petugas)->get('/admin/users')->assertForbidden();
        $this->actingAs($petugas)->get('/admin/users/create')->assertForbidden();
    }

    public function test_17_siswa_cannot_manage_books_or_admin_pages(): void
    {
        $siswa = User::where('email', 'siswa@perpustakaan.test')->first();

        $this->actingAs($siswa)->get('/kelola-buku')->assertForbidden();
        $this->actingAs($siswa)->get('/laporan')->assertForbidden();
        $this->actingAs($siswa)->get('/peminjaman')->assertForbidden();
        $this->actingAs($siswa)->postJson('/api/buku', [])->assertForbidden();
    }

    public function test_18_siswa_can_view_book_catalog_and_own_profile(): void
    {
        $siswa = User::where('email', 'siswa@perpustakaan.test')->first();
        $buku = Buku::first();

        $this->actingAs($siswa)->get('/buku')->assertOk();
        $this->actingAs($siswa)->get('/buku')->assertDontSee('id="form-buku"', false);
        $this->actingAs($siswa)->get('/buku/'.$buku->id)->assertOk();
        $this->actingAs($siswa)->get('/profile')->assertOk()->assertSee($siswa->name);
    }
}
