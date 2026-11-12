@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Laporan Perpustakaan</h2>
    <p>Rekapitulasi data statistik koleksi, sirkulasi peminjaman, dan aktivitas perpustakaan.</p>
</div>

<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 11px; font-weight: 600; color: #718096; text-transform: uppercase;">Total Buku</div>
        <div style="font-size: 26px; font-weight: 700; color: #1c2b3a; margin-top: 4px;">{{ $totalBuku }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 11px; font-weight: 600; color: #718096; text-transform: uppercase;">Total Pengguna</div>
        <div style="font-size: 26px; font-weight: 700; color: #1c2b3a; margin-top: 4px;">{{ $totalPengguna }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 11px; font-weight: 600; color: #718096; text-transform: uppercase;">Total Transaksi</div>
        <div style="font-size: 26px; font-weight: 700; color: #1c2b3a; margin-top: 4px;">{{ $totalPeminjaman }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 11px; font-weight: 600; color: #718096; text-transform: uppercase;">Sedang Dipinjam</div>
        <div style="font-size: 26px; font-weight: 700; color: #a9812f; margin-top: 4px;">{{ $dipinjamAktif }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 11px; font-weight: 600; color: #718096; text-transform: uppercase;">Sudah Kembali</div>
        <div style="font-size: 26px; font-weight: 700; color: #22543d; margin-top: 4px;">{{ $sudahKembali }}</div>
    </div>
</div>

<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
    <div class="card">
        <div class="card-header">
            <h3>Buku Paling Sering Dipinjam</h3>
        </div>
        <div class="table-wrapper">
            <table class="table-buku">
                <thead>
                    <tr>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th style="text-align: right;">Total Pinjam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bukuPopuler as $buku)
                        <tr>
                            <td><strong>{{ $buku->judul }}</strong></td>
                            <td>{{ $buku->penulis }}</td>
                            <td style="text-align: right;">
                                <span class="badge-kategori">{{ $buku->peminjamans_count }} Kali</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="table-status-cell">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Distribusi Koleksi per Kategori</h3>
        </div>
        <div class="table-wrapper">
            <table class="table-buku">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th style="text-align: right;">Jumlah Koleksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $kat)
                        <tr>
                            <td><strong>{{ $kat->nama }}</strong></td>
                            <td style="text-align: right;">
                                <span class="badge-role badge-petugas">{{ $kat->bukus_count }} Judul</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="table-status-cell">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

