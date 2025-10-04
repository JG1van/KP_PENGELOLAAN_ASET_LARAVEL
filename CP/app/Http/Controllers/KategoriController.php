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
        $request->validate([
            'Nama_Kategori' => 'required|string|max:100',
        ]);

        $newId = generateUniqueId('kategori', 'Id_Kategori');

        \App\Models\Kategori::create([
            'Id_Kategori' => $newId,
            'Nama_Kategori' => $request->Nama_Kategori,
        ]);
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
