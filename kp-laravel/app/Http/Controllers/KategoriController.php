<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $data = Kategori::all();
        return view('kategori.index', compact('data'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        // Validasi input hanya Nama_Kategori
        $request->validate([
            'Nama_Kategori' => 'required|string|max:100',
        ]);

        // Ambil ID kategori terakhir dari database
        $lastKategori = \App\Models\Kategori::orderBy('Id_Kategori', 'desc')->first();

        // Hitung ID baru (misal dari K0 jadi K1, dst)
        if ($lastKategori) {
            // Ambil angka dari belakang, lalu tambah 1
            $lastNumber = (int) substr($lastKategori->Id_Kategori, 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 0;
        }

        // Bentuk ID baru, misalnya: K0, K1, ...
        $newId = 'K' . $newNumber;

        // Simpan ke database
        \App\Models\Kategori::create([
            'Id_Kategori' => $newId,
            'Nama_Kategori' => $request->Nama_Kategori,
        ]);

        // Redirect kembali ke halaman index
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->update($request->all());
        return redirect()->route('kategori.index');
    }

    public function destroy($id)
    {
        try {
            Kategori::destroy($id);
            return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Kategori sedang digunakan dan tidak bisa dihapus.');
        }
    }

}
