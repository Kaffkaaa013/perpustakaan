@extends('layouts.app')

@section('content')
<div style="margin-bottom: 24px;">
    <h2>Manajemen Sirkulasi Peminjaman</h2>
    <p>Kelola permohonan peminjaman, verifikasi persetujuan, dan pencatatan buku kembali.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Sirkulasi Buku</h3>
        <div style="display: flex; gap: 8px; align-items: center;">
            @can('Mengajukan peminjaman buku')
                <a href="{{ route('peminjaman.create') }}" class="btn btn-sm btn-primary">+ Catat Peminjaman</a>
            @endcan
            <a href="{{ route('peminjaman.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-secondary' }}">Semua</a>
            <a href="{{ route('peminjaman.index', ['status' => 'menunggu']) }}" class="btn btn-sm {{ request('status') === 'menunggu' ? 'btn-primary' : 'btn-secondary' }}">Menunggu</a>
            <a href="{{ route('peminjaman.index', ['status' => 'aktif']) }}" class="btn btn-sm {{ request('status') === 'aktif' ? 'btn-primary' : 'btn-secondary' }}">Dipinjam</a>
            <a href="{{ route('peminjaman.index', ['status' => 'selesai']) }}" class="btn btn-sm {{ request('status') === 'selesai' ? 'btn-primary' : 'btn-secondary' }}">Dikembalikan</a>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table-buku">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Peminjam</th>
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
                        <td>
                            <strong>{{ $pinjam->user?->name ?? ($pinjam->anggota?->nama ?? 'Anggota #'.$pinjam->anggota_id) }}</strong>
                            <div style="font-size: 11px; color: #718096;">{{ $pinjam->user?->email ?? '-' }}</div>
                        </td>
                        <td>{{ $pinjam->buku?->judul ?? '-' }}</td>
                        <td>{{ $pinjam->tanggal_pinjam }}</td>
                        <td>{{ $pinjam->tanggal_kembali ?? '-' }}</td>
                        <td>
                            @if($pinjam->status === 'menunggu')
                                <span class="badge-role badge-petugas">Menunggu Persetujuan</span>
                            @elseif($pinjam->tanggal_kembali || $pinjam->status === 'dikembalikan')
                                <span class="badge-role badge-siswa">Dikembalikan</span>
                            @else
                                <span class="badge-role badge-petugas">Sedang Dipinjam</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="table-actions" style="justify-content: flex-end;">
                                @if($pinjam->status === 'menunggu')
                                    @can('Memproses persetujuan peminjaman')
                                        <form method="POST" action="{{ route('peminjaman.approve', $pinjam->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-edit">Setujui</button>
                                        </form>
                                    @endcan
                                @elseif(!$pinjam->tanggal_kembali && $pinjam->status !== 'dikembalikan')
                                    @can('Mencatat pengembalian buku')
                                        <form method="POST" action="{{ route('peminjaman.kembali', $pinjam->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">Tandai Kembali</button>
                                        </form>
                                    @endcan
                                @endif
                                <a href="{{ route('peminjaman.show', $pinjam->id) }}" class="btn btn-sm btn-secondary">Detail</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="table-status-cell">Belum ada catatan transaksi peminjaman.</td>
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

