<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mengarahkan pengguna ke dashboard spesifik role masing-masing.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('petugas')) {
            return redirect()->route('petugas.dashboard');
        }

        return redirect()->route('siswa.dashboard');
    }

    /**
     * Dashboard Administrator
     */
    public function admin(): View
    {
        $totalBuku = Buku::count();
        $totalPengguna = User::count();
        $totalPeminjaman = Peminjaman::count();
        $peminjamanAktif = Peminjaman::whereNull('tanggal_kembali')
            ->where('status', '!=', 'dikembalikan')
            ->count();
        $peminjamanTerbaru = Peminjaman::with(['user', 'anggota', 'buku'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalBuku',
            'totalPengguna',
            'totalPeminjaman',
            'peminjamanAktif',
            'peminjamanTerbaru'
        ));
    }

    /**
     * Dashboard Petugas
     */
    public function petugas(): View
    {
        $totalBuku = Buku::count();
        $peminjamanAktif = Peminjaman::whereNull('tanggal_kembali')
            ->where('status', '!=', 'dikembalikan')
            ->count();
        $peminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $peminjamanTerbaru = Peminjaman::with(['user', 'anggota', 'buku'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.petugas', compact(
            'totalBuku',
            'peminjamanAktif',
            'peminjamanMenunggu',
            'peminjamanTerbaru'
        ));
    }

    /**
     * Dashboard Siswa
     */
    public function siswa(): View
    {
        $user = Auth::user();

        $peminjamanSaya = Peminjaman::with('buku')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $bukuDipinjam = Peminjaman::where('user_id', $user->id)
            ->whereNull('tanggal_kembali')
            ->where('status', '!=', 'dikembalikan')
            ->count();

        $riwayatSelesai = Peminjaman::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('tanggal_kembali')
                    ->orWhere('status', 'dikembalikan');
            })
            ->count();

        return view('dashboard.siswa', compact(
            'peminjamanSaya',
            'bukuDipinjam',
            'riwayatSelesai'
        ));
    }
}
