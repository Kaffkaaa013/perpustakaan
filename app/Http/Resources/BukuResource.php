<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $kategoriModel = $this->getRelationValue('kategori') ?? $this->kategori()->first();

        return [
            'id' => $this->id,
            'kategori_id' => $this->kategori_id,
            'judul' => $this->judul,
            'penulis' => $this->penulis,
            'tahun_terbit' => (int) $this->tahun_terbit,
            'tahun' => (int) $this->tahun_terbit,
            'kategori' => $kategoriModel ? [
                'id' => $kategoriModel->id,
                'nama' => $kategoriModel->nama,
            ] : null,
            'kategori_nama' => $kategoriModel ? $kategoriModel->nama : '-',
            'peminjamans_count' => $this->peminjamans_count ?? $this->peminjaman_count ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
