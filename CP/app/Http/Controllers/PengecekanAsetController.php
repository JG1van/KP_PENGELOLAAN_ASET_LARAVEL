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
        $data = PengecekanAset::with(['user', 'detail'])->get();

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
            $idPengecekan = generateUniqueId('pengecekan_aset', 'Id_Pengecekan');

            // Simpan data utama pengecekan
            $pengecekan = PengecekanAset::create([
                'Id_Pengecekan' => $idPengecekan,
                'Tanggal_Pengecekan' => $request->Tanggal_Pengecekan,
                'User_Id' => Auth::id(),
            ]);

            $asetAktif = Aset::where('STATUS', 'aktif')->get();

            foreach ($asetAktif as $aset) {
                $kondisi = $request->kondisi[$aset->Id_Aset] ?? 'hilang';

                $idDetail = generateUniqueId('detail_pengecekan_aset', 'Id_Detail_Pengecekan');

                DetailPengecekanAset::create([
                    'Id_Detail_Pengecekan' => $idDetail,
                    'Id_Pengecekan' => $idPengecekan,
                    'Id_Aset' => $aset->Id_Aset,
                    'Kondisi' => $kondisi,
                ]);

                // Update kondisi terakhir aset
                $aset->update(['Kondisi' => $kondisi]);
            }

            DB::commit();

            return redirect()->route('pengecekan.index')->with('success', 'Aktivitas pengecekan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function incrementId2()
    {
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 2;

        do {
            $randomId = '';
            for ($i = 0; $i < $length; $i++) {
                $randomId .= $charset[random_int(0, strlen($charset) - 1)];
            }
        } while (\App\Models\PenempatanAset::where('Id_Penempatan', $randomId)->exists());

        return $randomId;
    }

    private function generateUniqueIdDetailPengecekan()
    {
        do {
            $id = $this->generateRandomId(4);
        } while (DetailPengecekanAset::where('Id_Detail_Pengecekan', $id)->exists());
        return $id;
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
            $filename = 'Pengecekan_Aset_ID_' . $id . "_Tanggal_" . now()->format('Y-m-d') . '.xlsx';

            return Excel::download(new PengecekanDetailExport($pengecekan), $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }
    private function generateRandomId($length = 4)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
    }

}
