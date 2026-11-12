@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Riwayat Peminjaman Saya</h2>
    <p>Daftar seluruh transaksi peminjaman buku yang pernah Anda ajukan.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Riwayat</h3>
        @can('Mengajukan peminjaman buku')
            <a href="{{ route('peminjaman.create') }}" class="btn btn-sm btn-primary">+ Ajukan Pinjam Buku</a>
        @endcan
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
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamans as $index => $pinjam)
                    <tr>
                        <td style="text-align: center; color: #718096;">{{ $peminjamans->firstItem() + $index }}</td>
                        <td><strong>{{ $pinjam->buku?->judul ?? 'Buku #'.$pinjam->buku_id }}</strong></td>
                        <td>{{ $pinjam->tanggal_pinjam }}</td>
                        <td>{{ $pinjam->tanggal_kembali ?? '-' }}</td>
                        <td>
                            @if($pinjam->status === 'menunggu')
                                <span class="badge-role badge-petugas">Menunggu Persetujuan</span>
                            @elseif($pinjam->tanggal_kembali || $pinjam->status === 'dikembalikan')
                                <span class="badge-role badge-siswa">Sudah Dikembalikan</span>
                            @else
                                <span class="badge-role badge-petugas">Sedang Dipinjam</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('peminjaman.show', $pinjam->id) }}" class="btn btn-sm btn-edit">Lihat Detail &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-status-cell">
                            Belum ada riwayat peminjaman buku.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($peminjamans->hasPages())
        <div style="margin-top: 16px;">
            {{ $peminjamans->links() }}
        </div>
    @endif
</div>
@endsection

