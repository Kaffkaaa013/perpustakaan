<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\BukuCrudController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Perpustakaan Digital
|--------------------------------------------------------------------------
*/

// Halaman Beranda Publik
Route::get('/', function () {
    return view('home');
})->name('home');

// Katalog Buku (Publik & Terdaftar)
Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
Route::get('/buku/create', [BukuController::class, 'create'])
    ->middleware(['auth', 'can:kelola-buku'])
    ->name('buku.create');
Route::get('/buku/{id}', [BukuController::class, 'show'])->name('buku.show');

// Rute Autentikasi (Hanya untuk Tamu / Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Rute Terlindungi (Wajib Login)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // --- SOAL 2: Perbaikan Celah Keamanan ---
    // (b) Perbaikan A2: Query aman dengan Parameter Binding
    Route::get('/buku-cari', function (Request $request) {
        $judul = $request->query('judul');
        $data = DB::select('SELECT * FROM bukus WHERE judul = ?', [$judul]);
        return response()->json($data);
    })->name('buku.cari');

    // (c) Perbaikan A3: Hapus route GET dan gunakan DELETE route dengan proteksi CSRF
    Route::delete('/buku/{buku}', [BukuController::class, 'destroy'])
        ->middleware('can:kelola-buku')
        ->name('buku.destroy');

    // Dashboard Dispatcher
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Dashboard Masing-Masing Role
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('permission:Akses dashboard admin')
        ->name('admin.dashboard');

    Route::get('/petugas/dashboard', [DashboardController::class, 'petugas'])
        ->middleware('permission:Akses dashboard petugas')
        ->name('petugas.dashboard');

    Route::get('/siswa/dashboard', [DashboardController::class, 'siswa'])
        ->name('siswa.dashboard');

    // 2. Pengelolaan Data Buku (Admin & Petugas) - Menggunakan Gate kelola-buku
    Route::get('/kelola-buku', [BukuCrudController::class, 'index'])
        ->middleware('can:kelola-buku')
        ->name('kelola-buku.index');

    // 3. Pengelolaan Pengguna & Role (Hanya Admin)
    Route::middleware('permission:Kelola data pengguna')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    Route::post('/admin/users/{user}/role', [UserController::class, 'updateRole'])
        ->middleware('permission:Kelola role pengguna')
        ->name('admin.users.updateRole');

    // 4. Sirkulasi Peminjaman & Pengembalian (Admin & Petugas)
    Route::middleware('permission:Kelola peminjaman')->group(function () {
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');

        Route::post('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'approve'])
            ->middleware('permission:Memproses persetujuan peminjaman')
            ->name('peminjaman.approve');

        Route::post('/peminjaman/{peminjaman}/kembali', [PeminjamanController::class, 'kembali'])
            ->middleware('permission:Mencatat pengembalian buku')
            ->name('peminjaman.kembali');
    });

    // 5. Pengajuan Peminjaman Buku (Siswa, Petugas, Admin)
    Route::middleware('permission:Mengajukan peminjaman buku')->group(function () {
        Route::get('/peminjaman/ajukan', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman/ajukan', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    });

    // Soal 4: Detail peminjaman yang diproteksi menggunakan PeminjamanPolicy
    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])
        ->name('peminjaman.show');

    // 6. Riwayat Peminjaman Pribadi
    Route::get('/riwayat-peminjaman', [PeminjamanController::class, 'riwayat'])
        ->middleware('permission:Melihat riwayat peminjaman sendiri')
        ->name('peminjaman.riwayat');

    // Detail Peminjaman (dengan proteksi data pribadi)
    Route::get('/riwayat-peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])
        ->name('peminjaman.show');

    // 7. Laporan Perpustakaan (Admin & Petugas)
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('permission:Lihat laporan perpustakaan')
        ->name('laporan.index');

    // 8. Pengelolaan Profil Pribadi
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->middleware('permission:Lihat profil sendiri')
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('permission:Ubah profil sendiri')
        ->name('profile.update');
});

// Route sementara untuk menguji tampilan pesan error umum saat APP_DEBUG=false
Route::get('/test-error', function () {
    throw new \RuntimeException('Pengujian halaman error tanpa jejak kode.');
});