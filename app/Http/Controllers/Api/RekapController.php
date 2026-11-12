<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;

class RekapController extends Controller
{
    /**
     * Rekap jumlah buku per kategori.
     */
    public function bukuPerKategori()
    {
        $rekap = Kategori::withCount([
            'bukus',
            'bukus as total_buku',
            'bukus as jumlah_buku',
        ])->get();

        return response()->json([
            'success' => true,
            'message' => 'Rekap jumlah buku per kategori berhasil diambil.',
            'data' => $rekap,
        ], 200);
    }

    /**
     * Rekap jumlah peminjaman per buku.
     */
    public function peminjamanPerBuku()
    {
        $rekap = Buku::with('kategori')
            ->withCount([
                'peminjamans',
                'peminjamans as peminjaman_count',
                'peminjamans as total_peminjaman',
                'peminjamans as jumlah_peminjaman',
            ])->get();

        return response()->json([
            'success' => true,
            'message' => 'Rekap peminjaman per buku berhasil diambil.',
            'data' => $rekap,
        ], 200);
    }
}
