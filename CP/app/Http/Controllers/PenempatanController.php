<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Aset;
use App\Models\Lokasi;
use App\Models\PenempatanAset;
use App\Models\DetailPenempatan;
use App\Exports\PenempatanExport;
use Maatwebsite\Excel\Facades\Excel;
class PenempatanController extends Controller
{
    public function index()
    {
        $data = PenempatanAset::with(['user', 'detail.lokasi'])
            ->orderBy('Id_Penempatan', 'asc')
            ->get();

        return view('penempatan.index', compact('data'));
    }

    public function create()
    {
        $lokasi = Lokasi::orderBy('Id_Lokasi')->get();
        $aset = Aset::where('STATUS', 'Aktif')->with(['kategori', 'penempatanTerakhir.lokasi'])->get();

        return view('penempatan.create', compact('lokasi', 'aset'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Tanggal_Penempatan' => 'required|date',
            'penempatan' => 'required|array|min:1',
            'penempatan.*.Id_Aset' => 'required|string|exists:aset,Id_Aset',
            'penempatan.*.Id_Lokasi' => 'nullable|string|exists:lokasi,Id_Lokasi',
        ]);

        if (!auth()->check()) {
            return back()->with('error', 'Silakan login terlebih dahulu!');
        }

        DB::beginTransaction();
        try {
            $idPenempatan = generateUniqueId('penempatan_aset', 'Id_Penempatan');

            // Simpan data utama
            $penempatan = PenempatanAset::create([
                'Id_Penempatan' => $idPenempatan,
                'Tanggal_Penempatan' => $validated['Tanggal_Penempatan'],
                'User_Id' => auth()->id(),
            ]);

            $lokasiDefault = '1';

            foreach ($validated['penempatan'] as $data) {
                $idDetail = generateUniqueId('detail_penempatan_aset', 'Id_Detail_Penempatan');

                $idLokasi = $data['Id_Lokasi'] ?? $lokasiDefault;
                if (empty($idLokasi)) {
                    $idLokasi = $lokasiDefault;
                }

                DetailPenempatan::create([
                    'Id_Detail_Penempatan' => $idDetail,
                    'Id_Penempatan' => $idPenempatan,
                    'Id_Aset' => $data['Id_Aset'],
                    'Id_Lokasi' => $idLokasi,
                ]);
            }

            DB::commit();
            return redirect()->route('penempatan.index')->with('success', 'Data penempatan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data penempatan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $penempatan = PenempatanAset::with(['detail.aset.kategori'])->findOrFail($id);
        $lokasi = Lokasi::orderBy('Nama_Lokasi')->get();

        return view('penempatan.show', compact('penempatan', 'lokasi'));
    }


    public function exportExcel($id)
    {
        $penempatan = \App\Models\PenempatanAset::findOrFail($id);

        return Excel::download(new PenempatanExport($penempatan), 'Penempatan_Aset_ID_' . $id . "_Tanggal_" . now()->format('Y-m-d') . '.xlsx');
    }


}
