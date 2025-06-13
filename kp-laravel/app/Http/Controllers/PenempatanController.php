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
            ->orderByDesc('Tanggal_Penempatan')
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
            // Generate ID Penempatan
            $tahun = now()->format('y');
            $prefix = '2P' . $tahun;
            $last = PenempatanAset::where('Id_Penempatan', 'like', "$prefix%")
                ->orderByDesc('Id_Penempatan')->first();
            $noUrut = $last ? intval(substr($last->Id_Penempatan, 4)) + 1 : 1;
            $idPenempatan = $prefix . str_pad($noUrut, 2, '0', STR_PAD_LEFT);

            // Simpan data utama
            $penempatan = PenempatanAset::create([
                'Id_Penempatan' => $idPenempatan,
                'Tanggal_Penempatan' => $validated['Tanggal_Penempatan'],
                'user_id' => auth()->id(),
            ]);

            // Ambil lokasi default
            $lokasiDefault = 'L00';

            // Simpan semua detail
            $lastDetail = DetailPenempatan::orderByDesc('Id_Detail_Penempatan')->first();
            $urutDetail = $lastDetail ? intval(substr($lastDetail->Id_Detail_Penempatan, 2)) + 1 : 1;

            foreach ($validated['penempatan'] as $data) {
                $idDetail = '2D' . str_pad($urutDetail++, 4, '0', STR_PAD_LEFT);

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

        return Excel::download(new PenempatanExport($penempatan), 'penempatan-' . $id . '.xlsx');
    }


}
