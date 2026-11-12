<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;

class BukuCrudController extends Controller
{
    /**
     * Menampilkan halaman pengelolaan buku (Admin & Petugas).
     */
    public function index()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $bukus = Buku::with('kategori')->latest()->get();

        return view('buku.kelola', compact('kategoris', 'bukus'));
    }
}
