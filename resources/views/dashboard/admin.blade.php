@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Dashboard Administrator</h2>
    <p>Selamat datang, <strong>{{ Auth::user()->name }}</strong>. Anda memiliki wewenang penuh dalam mengelola sistem perpustakaan.</p>
</div>

<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Total Buku</div>
        <div style="font-size: 28px; font-weight: 700; color: #1c2b3a; margin-top: 6px;">{{ $totalBuku }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Pengguna Terdaftar</div>
        <div style="font-size: 28px; font-weight: 700; color: #1c2b3a; margin-top: 6px;">{{ $totalPengguna }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Total Transaksi</div>
        <div style="font-size: 28px; font-weight: 700; color: #1c2b3a; margin-top: 6px;">{{ $totalPeminjaman }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Peminjaman Aktif</div>
        <div style="font-size: 28px; font-weight: 700; color: #a9812f; margin-top: 6px;">{{ $peminjamanAktif }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Menu Cepat Administrator</h3>
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        @can('Kelola data buku')
            <a href="{{ route('kelola-buku.index') }}" class="btn btn-primary">Kelola Koleksi Buku</a>
        @endcan
        @can('Kelola data pengguna')
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kelola Akun & Role</a>
        @endcan
        @can('Kelola peminjaman')
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Sirkulasi Peminjaman</a>
        @endcan
        @can('Lihat laporan perpustakaan')
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Laporan Perpustakaan</a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Aktivitas Peminjaman Terbaru</h3>
        <a href="{{ route('peminjaman.index') }}" class="back-link">Lihat Semua &rarr;</a>
    </div>
    <div class="table-wrapper">
        <table class="table-buku">
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamanTerbaru as $pinjam)
                    <tr>
                        <td><strong>{{ $pinjam->user?->name ?? ($pinjam->anggota?->nama ?? 'Anggota #'.$pinjam->anggota_id) }}</strong></td>
                        <td>{{ $pinjam->buku?->judul ?? '-' }}</td>
                        <td>{{ $pinjam->tanggal_pinjam }}</td>
                        <td>
                            @if($pinjam->tanggal_kembali)
                                <span class="badge-role badge-siswa">Dikembalikan</span>
                            @else
                                <span class="badge-role badge-petugas">Dipinjam</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="table-status-cell">Belum ada aktivitas peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

