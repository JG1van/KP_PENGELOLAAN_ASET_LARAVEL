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
        // Mengambil data dengan relasi detail dan user, urut berdasarkan Id_Penghapusan desc
        $data = PenghapusanAset::with(['detail', 'user'])
            ->orderByDesc('Id_Penghapusan')
            ->get();

        return view('penghapusan.index', compact('data'));
    }

    public function create()
    {
        $asets = Aset::where('STATUS', 'aktif')
            ->whereIn('Kondisi', ['rusak berat', 'hilang', 'diremajakan'])
            ->get();

        return view('penghapusan.create', compact('asets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Tanggal_Hapus' => 'required|date',
            'Dokumen_Penghapusan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'aset_terpilih' => 'required|array|min:1'
        ]);

        if (!auth()->check()) {
            return back()->with('error', 'Silakan login terlebih dahulu!');
        }

        DB::beginTransaction();
        try {
            // Generate ID Penghapusan terlebih dahulu
            $tahun = now()->format('y');
            $prefix = '4P' . $tahun;
            $last = PenghapusanAset::where('Id_Penghapusan', 'like', "$prefix%")
                ->orderByDesc('Id_Penghapusan')->first();
            $noUrut = $last ? intval(substr($last->Id_Penghapusan, 4)) + 1 : 1;
            $idPenghapusan = $prefix . str_pad($noUrut, 2, '0', STR_PAD_LEFT);

            // Upload file ke Cloudinary
            $file = $request->file('Dokumen_Penghapusan');
            $tanggalFormat = date('Y-m-d', strtotime($request->Tanggal_Hapus));
            $publicId = "dokumen_penghapusan_{$tanggalFormat}_{$idPenghapusan}";

            $uploaded = Cloudinary::upload(
                $file->getRealPath(),
                [
                    'folder' => 'dokumen_Sekolah/dokumen_penghapusan',
                    'public_id' => $publicId,
                    'resource_type' => 'auto'
                ]
            );

            $fileUrl = $uploaded->getSecurePath();

            // Simpan penghapusan
            $penghapusan = PenghapusanAset::create([
                'Id_Penghapusan' => $idPenghapusan,
                'Tanggal_Hapus' => $request->Tanggal_Hapus,
                'Dokumen_Penghapusan' => $fileUrl, // disimpan sebagai URL
                'user_id' => auth()->id(),
            ]);

            // Detail penghapusan
            $lastDetail = DetailPenghapusanAset::orderByDesc('Id_Detail_Penghapusan')->first();
            $detailUrut = $lastDetail ? intval(substr($lastDetail->Id_Detail_Penghapusan, 2)) + 1 : 1;

            foreach ($request->aset_terpilih as $idAset) {
                $idDetail = '4D' . str_pad($detailUrut++, 4, '0', STR_PAD_LEFT);

                DetailPenghapusanAset::create([
                    'Id_Detail_Penghapusan' => $idDetail,
                    'Id_Penghapusan' => $idPenghapusan,
                    'Id_Aset' => $idAset
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

        if ($penghapusan->Dokumen_Penghapusan) {
            $dokumen = $penghapusan->Dokumen_Penghapusan;

            // Jika berupa JSON array dari Cloudinary
            if (is_array(json_decode($dokumen, true))) {
                $files = json_decode($dokumen, true);
            } else {
                // Jika berupa URL tunggal
                if (filter_var($dokumen, FILTER_VALIDATE_URL)) {
                    $files[] = [
                        'secure_url' => $dokumen,
                        'public_id' => basename($dokumen),
                    ];
                }
            }
        }

        return view('penghapusan.show', compact('penghapusan', 'files'));
    }


}
