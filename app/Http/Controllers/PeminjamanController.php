<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /**
     * Sirkulasi Peminjaman (Admin & Petugas)
     */
    public function index(Request $request): View
    {
        $query = Peminjaman::with(['user', 'anggota', 'buku'])->latest();

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'menunggu') {
                $query->where('status', 'menunggu');
            } elseif ($request->status === 'aktif') {
                $query->where('status', 'dipinjam')->whereNull('tanggal_kembali');
            } elseif ($request->status === 'selesai') {
                $query->where(function ($q) {
                    $q->whereNotNull('tanggal_kembali')->orWhere('status', 'dikembalikan');
                });
            }
        }

        $peminjamans = $query->paginate(10);

        return view('peminjaman.index', compact('peminjamans'));
    }

    /**
     * Formulir Pengajuan Peminjaman
     */
    public function create(Request $request): View
    {
        $bukus = Buku::orderBy('judul')->get();
        $selectedBukuId = $request->query('buku_id');

        return view('peminjaman.create', compact('bukus', 'selectedBukuId'));
    }

    /**
     * Simpan Pengajuan Peminjaman
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'buku_id' => ['required', 'exists:bukus,id'],
            'tanggal_pinjam' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $isStaff = $user->hasRole('admin') || $user->hasRole('petugas');

        $anggota = Anggota::query()->firstOrCreate(
            ['email' => $user->email],
            [
                'nama' => $user->name,
                'no_telepon' => '-',
            ]
        );

        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'anggota_id' => $anggota->id,
            'buku_id' => $validated['buku_id'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'status' => $isStaff ? 'dipinjam' : 'menunggu',
        ]);

        if ($isStaff) {
            return redirect()->route('peminjaman.index')
                ->with('success', 'Transaksi peminjaman berhasil dicatat.');
        }

        return redirect()->route('peminjaman.riwayat')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim. Menunggu verifikasi petugas.');
    }

    /**
     * Setujui Pengajuan Peminjaman (Petugas & Admin)
     */
    public function approve(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->update([
            'status' => 'dipinjam',
        ]);

        return redirect()->back()
            ->with('success', "Peminjaman buku '{$peminjaman->buku?->judul}' telah disetujui.");
    }

    /**
     * Catat Pengembalian Buku (Petugas & Admin)
     */
    public function kembali(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->update([
            'tanggal_kembali' => now()->format('Y-m-d'),
            'status' => 'dikembalikan',
        ]);

        return redirect()->back()
            ->with('success', "Pengembalian buku '{$peminjaman->buku?->judul}' berhasil dicatat.");
    }

    /**
     * Riwayat Peminjaman Pribadi Siswa
     */
    public function riwayat(): View
    {
        $user = Auth::user();
        $peminjamans = Peminjaman::with('buku')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('peminjaman.riwayat', compact('peminjamans'));
    }

    /**
     * Detail Peminjaman (diproteksi menggunakan PeminjamanPolicy Soal 4)
     */
    public function show(Peminjaman $peminjaman): View
    {
        // Otorisasi menggunakan Policy (menggantikan pengecekan manual Modul 5)
        Gate::authorize('view', $peminjaman);

        $peminjaman->load(['user', 'anggota', 'buku.kategori']);

        return view('peminjaman.show', compact('peminjaman'));
    }
}
