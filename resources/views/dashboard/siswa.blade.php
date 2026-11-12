@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Dashboard Siswa</h2>
    <p>Halo, <strong>{{ Auth::user()->name }}</strong>. Cari buku favoritmu dan pantau status peminjamanmu di sini.</p>
</div>

<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Buku Sedang Dipinjam</div>
        <div style="font-size: 28px; font-weight: 700; color: #a9812f; margin-top: 6px;">{{ $bukuDipinjam }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Peminjaman Selesai</div>
        <div style="font-size: 28px; font-weight: 700; color: #22543d; margin-top: 6px;">{{ $riwayatSelesai }}</div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div style="font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase;">Total Transaksi Saya</div>
        <div style="font-size: 28px; font-weight: 700; color: #1c2b3a; margin-top: 6px;">{{ $peminjamanSaya->count() }}</div>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3>Riwayat & Status Peminjaman Saya</h3>
        <div style="display: flex; gap: 8px;">
            @can('Mengajukan peminjaman buku')
                <a href="{{ route('peminjaman.create') }}" class="btn btn-sm btn-primary">+ Ajukan Pinjam</a>
            @endcan
            <a href="{{ route('buku.index') }}" class="btn btn-sm btn-secondary">Katalog Buku</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table-buku">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th style="text-align: right;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamanSaya as $index => $pinjam)
                    <tr>
                        <td style="text-align: center; color: #718096;">{{ $index + 1 }}</td>
                        <td><strong>{{ $pinjam->buku?->judul ?? 'Buku #'.$pinjam->buku_id }}</strong></td>
                        <td>{{ $pinjam->tanggal_pinjam }}</td>
                        <td>{{ $pinjam->tanggal_kembali ?? '-' }}</td>
                        <td>
                            @if($pinjam->status === 'menunggu')
                                <span class="badge-role badge-petugas">Menunggu Verifikasi</span>
                            @elseif($pinjam->tanggal_kembali)
                                <span class="badge-role badge-siswa">Sudah Dikembalikan</span>
                            @else
                                <span class="badge-role badge-petugas">Sedang Dipinjam</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('peminjaman.show', $pinjam->id) }}" class="btn btn-sm btn-edit">Lihat &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-status-cell">
                            Anda belum memiliki riwayat peminjaman buku.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

