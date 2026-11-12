<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        $totalBuku = Buku::count();
        $totalPengguna = User::count();
        $totalPeminjaman = Peminjaman::count();
        $dipinjamAktif = Peminjaman::whereNull('tanggal_kembali')
            ->where('status', '!=', 'dikembalikan')
            ->count();
        $sudahKembali = Peminjaman::query()
            ->where(function ($query): void {
                $query->whereNotNull('tanggal_kembali')
                    ->orWhere('status', 'dikembalikan');
            })
            ->count();
        $menungguVerifikasi = Peminjaman::where('status', 'menunggu')->count();

        // Rekap Buku Terpopuler
        $bukuPopuler = Buku::withCount('peminjamans')
            ->orderByDesc('peminjamans_count')
            ->take(5)
            ->get();

        // Rekap Kategori
        $kategoris = Kategori::withCount('bukus')->get();

        return view('laporan.index', compact(
            'totalBuku',
            'totalPengguna',
            'totalPeminjaman',
            'dipinjamAktif',
            'sudahKembali',
            'menungguVerifikasi',
            'bukuPopuler',
            'kategoris'
        ));
    }
}
