<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'bukus';

    protected $fillable = [
        'kategori_id',
        'judul',
        'penulis',
        'tahun_terbit',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'buku_id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'buku_id');
    }

    public function getTahunAttribute()
    {
        return $this->tahun_terbit;
    }

    public function getKategoriNamaAttribute()
    {
        $kategoriModel = $this->getRelationValue('kategori');
        if (! $kategoriModel) {
            $kategoriModel = $this->kategori()->first();
        }

        return $kategoriModel ? $kategoriModel->nama : '-';
    }
}
