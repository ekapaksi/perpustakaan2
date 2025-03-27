<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KategoriController extends Controller
{
    public function index()
    {
        // Menampilkan semua kategori dan jumlah buku di masing-masing kategori
        $kategori = Kategori::withCount('buku')->get();
        return view('admin.kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        // Cek apakah nama kategori sudah ada
        $kategoriExists = Kategori::where('nama_kategori', $request->nama_kategori)->exists();

        if ($kategoriExists) {
            return redirect()->back()->with('error', 'Nama kategori sudah ada!');
        }

        // Jika tidak ada, simpan data
        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, string $id)
    {
        // Cek apakah nama kategori sudah ada
        $kategoriExists = Kategori::where('nama_kategori', $request->nama_kategori)->exists();

        if ($kategoriExists) {
            return redirect()->back()->with('error', 'Nama kategori sudah ada!');
        }

        // Update data kategori
        Kategori::where('id', $id)->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        // Temukan kategori berdasarkan ID
        $kategori = Kategori::findOrFail($id);

        // Hapus kategori
        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}
