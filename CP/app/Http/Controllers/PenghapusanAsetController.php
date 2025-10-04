<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Aset;
use App\Models\PenghapusanAset;
use App\Models\DetailPenghapusanAset;


class PenghapusanAsetController extends Controller
{
    public function index()
    {
        $data = PenghapusanAset::with(['detail.aset', 'user'])->get();

        return view('penghapusan.index', compact('data'));
    }

    public function create()
    {
        $asets = Aset::where('STATUS', 'Aktif')
            ->whereIn('Kondisi', ['rusak berat', 'hilang', 'diremajakan'])
            ->get();

        return view('penghapusan.create', compact('asets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Tanggal_Hapus' => 'required|date',
            'Dokumen_Penghapusan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'aset_terpilih' => 'required|array|min:1',
        ]);

        if (!auth()->check()) {
            return back()->with('error', 'Silakan login terlebih dahulu!');
        }

        DB::beginTransaction();
        try {
            $idPenghapusan = generateUniqueId('penghapusan_aset', 'Id_Penghapusan');
            $tanggal = $request->Tanggal_Hapus;

            // Simpan file di storage lokal
            $file = $request->file('Dokumen_Penghapusan');
            $filename = "Dokumen_Penghapusan_ID_{$idPenghapusan}_Tanggal_" . date('Y-m-d', strtotime($tanggal)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/Dokumen_Penghapusan', $filename);


            // Simpan data penghapusan
            $penghapusan = PenghapusanAset::create([
                'Id_Penghapusan' => $idPenghapusan,
                'Tanggal_Hapus' => $tanggal,
                'Dokumen_Penghapusan' => $filename,
                'User_Id' => auth()->id(),
            ]);

            foreach ($request->aset_terpilih as $idAset) {
                $idDetail = generateUniqueId('detail_penghapusan_aset', 'Id_Detail_Penghapusan');

                DetailPenghapusanAset::create([
                    'Id_Detail_Penghapusan' => $idDetail,
                    'Id_Penghapusan' => $idPenghapusan,
                    'Id_Aset' => $idAset,
                ]);

                Aset::where('Id_Aset', $idAset)->update(['STATUS' => 'Tidak Aktif']);
            }

            DB::commit();
            return redirect()->route('penghapusan.index')->with('success', 'Penghapusan aset berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }



    public function show($id)
    {
        $penghapusan = PenghapusanAset::with(['detail.aset.kategori', 'user'])->findOrFail($id);

        $files = [];
        $dokumen = $penghapusan->Dokumen_Penghapusan;
        if ($dokumen) {
            $files[] = [
                'url' => $dokumen,
                'name' => basename($dokumen),
            ];
        }

        return view('penghapusan.show', compact('penghapusan', 'files'));
    }


}
