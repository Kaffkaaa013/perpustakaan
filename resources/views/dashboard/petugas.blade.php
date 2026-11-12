@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Dashboard Petugas</h2>
    <p>Selamat bertugas, <strong>{{ Auth::user()->name }}</strong>. Layani sirkulasi dan kelola koleksi buku dengan teliti.</p>
</div>

<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Total Koleksi Buku</div>
        <div style="font-size: 28px; font-weight: 700; color: #1c2b3a; margin-top: 6px;">{{ $totalBuku }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Peminjaman Aktif</div>
        <div style="font-size: 28px; font-weight: 700; color: #a9812f; margin-top: 6px;">{{ $peminjamanAktif }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Menunggu Persetujuan</div>
        <div style="font-size: 28px; font-weight: 700; color: #1c2b3a; margin-top: 6px;">{{ $peminjamanMenunggu }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Layanan Petugas</h3>
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        @can('Kelola data buku')
            <a href="{{ route('kelola-buku.index') }}" class="btn btn-primary">Kelola Data Buku</a>
        @endcan
        @can('Kelola peminjaman')
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Sirkulasi & Pengembalian</a>
        @endcan
        @can('Lihat laporan perpustakaan')
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Laporan Perpustakaan</a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Antrean Sirkulasi Peminjaman</h3>
        <a href="{{ route('peminjaman.index') }}" class="back-link">Kelola Semua &rarr;</a>
    </div>
    <div class="table-wrapper">
        <table class="table-buku">
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamanTerbaru as $pinjam)
                    <tr>
                        <td><strong>{{ $pinjam->user?->name ?? ($pinjam->anggota?->nama ?? 'Anggota #'.$pinjam->anggota_id) }}</strong></td>
                        <td>{{ $pinjam->buku?->judul ?? '-' }}</td>
                        <td>{{ $pinjam->tanggal_pinjam }}</td>
                        <td>
                            @if($pinjam->status === 'menunggu')
                                <span class="badge-role badge-petugas">Menunggu</span>
                            @elseif($pinjam->tanggal_kembali)
                                <span class="badge-role badge-siswa">Selesai</span>
                            @else
                                <span class="badge-role badge-petugas">Dipinjam</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            @if($pinjam->status === 'menunggu')
                                @can('Memproses persetujuan peminjaman')
                                    <form method="POST" action="{{ route('peminjaman.approve', $pinjam->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-edit">Setujui</button>
                                    </form>
                                @endcan
                            @elseif(!$pinjam->tanggal_kembali)
                                @can('Mencatat pengembalian buku')
                                    <form method="POST" action="{{ route('peminjaman.kembali', $pinjam->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Tandai Kembali</button>
                                    </form>
                                @endcan
                            @else
                                <span style="color: #a0aec0;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="table-status-cell">Belum ada transaksi peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

