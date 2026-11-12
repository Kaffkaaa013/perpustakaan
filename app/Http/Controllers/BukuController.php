<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;

class BukuController extends Controller
{
    public function index()
    {
        $buku = Buku::with('kategori')->get();
        $kategoris = Kategori::orderBy('nama')->get();

        return view('buku.index', compact('buku', 'kategoris'));
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->find($id);

        return view('buku.show', compact('buku'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('buku.kelola', compact('kategoris'));
    }

    public function destroy(Buku $buku)
    {
        $buku->peminjamans()->delete();
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
