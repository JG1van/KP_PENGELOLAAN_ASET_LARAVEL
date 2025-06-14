<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Aset;
use App\Models\PenghapusanAset;
use App\Models\DetailPenghapusanAset;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PenghapusanAsetController extends Controller
{
    public function index()
    {
        $data = PenghapusanAset::with(['detail.aset', 'user'])
            ->orderByDesc('Id_Penghapusan')
            ->get();

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
            // Buat ID Penghapusan: 4P + 2 digit tahun + 2 digit urut
            $tahun = date('y', strtotime($request->Tanggal_Hapus));
            $prefix = '4P' . $tahun;

            $last = PenghapusanAset::where('Id_Penghapusan', 'like', "$prefix%")
                ->orderByDesc('Id_Penghapusan')->lockForUpdate()->first();

            $noUrut = $last ? intval(substr($last->Id_Penghapusan, 4)) + 1 : 1;
            $idPenghapusan = $prefix . str_pad($noUrut, 2, '0', STR_PAD_LEFT);

            // Upload file dokumen ke Cloudinary
            $file = $request->file('Dokumen_Penghapusan');
            $publicId = 'Dokumen_Penghapusan_' . $request->Tanggal_Hapus . '_' . $idPenghapusan;
            $upload = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'dokumen_Sekolah/Dokumen_Penghapusan',
                'public_id' => $publicId,
                'resource_type' => 'auto',
            ]);
            $fileUrl = $upload->getSecurePath();

            // Simpan data penghapusan
            $penghapusan = PenghapusanAset::create([
                'Id_Penghapusan' => $idPenghapusan,
                'Tanggal_Hapus' => $request->Tanggal_Hapus,
                'Dokumen_Penghapusan' => $fileUrl,
                'user_id' => auth()->id(),
            ]);

            // Detail penghapusan: 3D0001, dst
            $lastDetail = DetailPenghapusanAset::orderByDesc('Id_Detail_Penghapusan')->lockForUpdate()->first();
            $urutDetail = $lastDetail ? intval(substr($lastDetail->Id_Detail_Penghapusan, 2)) + 1 : 1;

            foreach ($request->aset_terpilih as $idAset) {
                $idDetail = '4D' . str_pad($urutDetail++, 4, '0', STR_PAD_LEFT);

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
            // JSON atau URL tunggal
            $decoded = json_decode($dokumen, true);
            if (is_array($decoded)) {
                $files = $decoded;
            } elseif (filter_var($dokumen, FILTER_VALIDATE_URL)) {
                $files[] = [
                    'secure_url' => $dokumen,
                    'public_id' => basename($dokumen),
                ];
            }
        }

        return view('penghapusan.show', compact('penghapusan', 'files'));
    }
}
