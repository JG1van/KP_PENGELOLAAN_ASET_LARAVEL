<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PengecekanAset;
use App\Models\DetailPengecekanAset;
use App\Models\Aset;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengecekanExport;
use App\Exports\PengecekanDetailExport;
class PengecekanAsetController extends Controller
{
    public function index()
    {
        $data = PengecekanAset::with(['user', 'detail'])
            ->orderByDesc('Id_Pengecekan') // Urutkan berdasarkan ID terbaru
            ->get();

        return view('pengecekan.index', compact('data'));
    }

    public function create()
    {
        $asetAktif = Aset::with('PenurunanTerbaru')
            ->where('STATUS', 'aktif')
            ->get();

        return view('pengecekan.create', compact('asetAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Tanggal_Pengecekan' => 'required|date',
            'kondisi' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $tahun = now()->format('y'); // Contoh: 25 (tahun 2025)
            $prefix = '3P' . $tahun;

            // Cari ID pengecekan terakhir dengan prefix yang sama
            $lastPengecekan = PengecekanAset::where('Id_Pengecekan', 'like', "$prefix%")
                ->orderByDesc('Id_Pengecekan')
                ->first();

            // Ambil nomor urut dari 2 digit terakhir ID terakhir, tambah 1
            $noUrutPengecekan = $lastPengecekan ? intval(substr($lastPengecekan->Id_Pengecekan, 4)) + 1 : 1;
            $newIdPengecekan = $prefix . str_pad($noUrutPengecekan, 2, '0', STR_PAD_LEFT);

            // Simpan data pengecekan utama
            $pengecekan = PengecekanAset::create([
                'Id_Pengecekan' => $newIdPengecekan,
                'Tanggal_Pengecekan' => $request->Tanggal_Pengecekan,
                'user_id' => Auth::id(),
            ]);

            $asetAktif = Aset::where('STATUS', 'aktif')->get();

            // Cari ID terakhir detail pengecekan
            $lastDetail = DetailPengecekanAset::orderByDesc('Id_Detail_Pengecekan')->first();
            $lastDetailNumber = $lastDetail ? intval(substr($lastDetail->Id_Detail_Pengecekan, 2)) : 0;

            foreach ($asetAktif as $aset) {
                $kondisi = $request->kondisi[$aset->Id_Aset] ?? 'hilang';

                $lastDetailNumber++;
                $idDetail = '3D' . str_pad($lastDetailNumber, 4, '0', STR_PAD_LEFT);

                DetailPengecekanAset::create([
                    'Id_Detail_Pengecekan' => $idDetail,
                    'Id_Pengecekan' => $newIdPengecekan,
                    'Id_Aset' => $aset->Id_Aset,
                    'Kondisi' => $kondisi,
                ]);

                // Update kondisi terakhir di tabel aset
                $aset->update(['Kondisi' => $kondisi]);
            }

            DB::commit();

            return redirect()->route('pengecekan.index')->with('success', 'Aktivitas pengecekan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pengecekan = PengecekanAset::with(['detail.aset.kategori'])->findOrFail($id);

        return view('pengecekan.show', compact('pengecekan'));
    }
    public function exportExcel($id)
    {
        try {
            $pengecekan = PengecekanAset::with(['detail.aset.kategori', 'user'])->findOrFail($id);
            $filename = 'Pengecekan_Aset_' . $id . "_" . now()->format('Ymd_His') . '.xlsx';

            return Excel::download(new PengecekanDetailExport($pengecekan), $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }
}
