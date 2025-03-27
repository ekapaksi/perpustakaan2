<?php

namespace App\Http\Controllers\Admin;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index()
    {
        $buku = Buku::with('kategori')->get();
        $kategori = Kategori::all();
        return view('admin.buku.index', compact('buku', 'kategori'));
    }
    public function create()
    {
        $kategori = Kategori::all();
        return view('admin.buku.create', compact('kategori'));
    }
    public function store(Request $request)
    {
        $messages = [
            'image.image' => 'Gambar harus berupa file image.',
            'image.max' => 'Maksimal ukuran file 1 MB.',
        ];
        $validatedData = $request->validate([
            'image' => 'image|mimes:jpeg,jpg,png|file|max:1024',
        ], $messages);
        // Upload dan simpan gambar
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('cover-buku', 'public');
        } else {
            $imagePath = 'cover-buku/book-default-cover.jpg';
        }
        // Simpan data ke dalam database
        $data = $request->only([
            'judul_buku',
            'id_kategori',
            'pengarang',
            'penerbit',
            'tahun_terbit',
            'isbn',
            'stok',
            'dipinjam',
            'dibooking'
        ]);
        $data['image'] = $imagePath;
        Buku::create($data);
        return redirect()->route('admin.master.buku.index')->with('success', 'Buku berhasil ditambahkan!');
    }
    public function show(Buku $buku)
    {
        return view('admin.buku.show', compact('buku'));
    }
    public function edit(Buku $buku)
    {
        $kategori = Kategori::all();
        return view('admin.buku.edit', compact('buku', 'kategori'));
    }
    public function update(Request $request, string $id)
    {
        $messages = [
            'image.image' => 'Gambar harus berupa file image.',
            'image.max' => 'Maksimal ukuran file 1 MB.',
        ];
        $validatedData = $request->validate([
            'image' => 'image|mimes:jpeg,jpg,png|file|max:1024',
        ], $messages);
        // Update data
        $data = $request->only([
            'judul_buku',
            'id_kategori',
            'pengarang',
            'penerbit',
            'tahun_terbit',
            'isbn',
            'stok',
            'dipinjam',
            'dibooking'
        ]);
        if ($request->file('image')) {
            if ($request->oldImage && $request->oldImage <> 'cover-buku/book-default-cover.jpg') {
                Storage::delete($request->oldImage);
            }
            $imagePath = $request->file('image')->store('cover-buku');
            $data['image'] = $imagePath;
        }
        Buku::where('id', $id)->update($data);
        return redirect()->route('admin.master.buku.index')->with('success', 'Buku berhasil diupdate.');
    }
    public function destroy(Buku $buku)
    {
        if ($buku->image && $buku->image <> 'cover-buku/book-default-cover.jpg') {
            Storage::delete($buku->image);
        }
        Buku::destroy('id', $buku->id);
        return redirect()->back()->with('success', 'Buku berhasil dihapus.');
    }
}
