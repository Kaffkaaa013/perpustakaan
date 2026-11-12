<?php

use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\RekapController;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::name('api.')->group(function (): void {
    Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('buku/{buku}', [BukuController::class, 'show'])->name('buku.show');

    Route::middleware(['auth', 'permission:Kelola data buku'])->group(function (): void {
        Route::post('buku', [BukuController::class, 'store'])->name('buku.store');
        Route::put('buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
        Route::patch('buku/{buku}', [BukuController::class, 'update']);
        Route::delete('buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');
    });
});

Route::get('/kategori', function () {
    return response()->json([
        'success' => true,
        'data' => Kategori::orderBy('nama')->get(),
    ]);
});

Route::prefix('rekap')->group(function () {
    Route::get('/buku-per-kategori', [RekapController::class, 'bukuPerKategori']);
    Route::get('/pemminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);
    Route::get('/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);
});
