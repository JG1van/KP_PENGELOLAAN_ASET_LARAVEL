<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lokasi;

class LokasiController extends Controller
{
    public function index()
    {
        $data = Lokasi::all()->sortBy(function ($item) {
            return intval(substr($item->Id_Lokasi, 1));
        })->values(); // reset index collection

        return view('lokasi.index', compact('data'));
    }

    public function create()
    {
        return view('lokasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_Lokasi' => 'required|string|max:100',
        ]);

        // Ambil ID terakhir numerik → buat ID baru → 00, 01, 02, dst.
        $newId = generateUniqueId('lokasi', 'Id_Lokasi');

        Lokasi::create([
            'Id_Lokasi' => $newId,
            'Nama_Lokasi' => $request->Nama_Lokasi,
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        return view('lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Lokasi' => 'required|string|max:100',
        ]);

        $lokasi = Lokasi::findOrFail($id);
        $lokasi->update([
            'Nama_Lokasi' => $request->Nama_Lokasi,
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            Lokasi::destroy($id);
            return redirect()->back()->with('success', 'Lokasi berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Lokasi sedang digunakan dan tidak bisa dihapus.');
        }
    }

}
