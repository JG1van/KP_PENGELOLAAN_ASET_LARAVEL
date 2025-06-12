<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Aset;
use App\Models\PengecekanAset;
use App\Models\PenghapusanAset;
use App\Exports\LaporanAsetExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAktivitasExport;
use App\Models\PenerimaanAset;
use App\Models\DetailPenerimaanAset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PengaturanController extends Controller
{
    public function index()
    {
        return view('pengaturan.index');
    }

    public function profil()
    {
        $user = auth()->user();
        return view('pengaturan.profil', compact('user'));
    }

    public function pengguna()
    {
        $users = User::all();
        return view('pengaturan.pengguna', compact('users'));
    }

    public function laporanAset()
    {
        $asets = Aset::with(['kategori', 'penurunans'])->get();
        return view('pengaturan.laporan_aset', compact('asets'));
    }

    public function laporanAktivitas()
    {
        $pengecekan = PengecekanAset::with('detail')->get();
        $penghapusan = PenghapusanAset::with('detail')->get();
        return view('pengaturan.laporan_aktivitas', compact('pengecekan', 'penghapusan'));
    }

    public function storePengguna(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'aktif',
        ]);

        return redirect()->route('pengaturan.pengguna')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function exportLaporanAktivitas()
    {
        return Excel::download(new LaporanAktivitasExport, 'Laporan_Aktivitas_Aset_' . now()->format('Ymd_His') . '.xlsx');
    }
    public function exportLaporanAset()
    {
        return Excel::download(new LaporanAsetExport, 'laporan_aset_' . now()->format('Ymd_His') . '.xlsx');
    }
    public function exportAktivitasExcel()
    {
        return Excel::download(new LaporanAktivitasExport, 'Laporan_Aktivitas.xlsx');
    }

    public function pengaktifanIndex(Request $request)
    {
        $query = Aset::where('STATUS', 'tidak aktif')
            ->where('Kondisi', 'hilang');


        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('Id_Aset', 'like', "%{$request->search}%")
                    ->orWhere('Nama_Aset', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('kondisi')) {
            $query->where('Kondisi', $request->kondisi);
        }

        $data = $query->with('detailPenerimaan.penerimaan')->get();
        return view('pengaturan.pengaktifan', compact('data'));
    }


    public function aktifkanAset(Request $request, $id)
    {
        $request->validate([
            'dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $aset = Aset::findOrFail($id);

            if (!in_array(strtolower($aset->Kondisi), ['hilang'])) {
                return back()->with('error', 'Hanya aset dengan kondisi hilang yang dapat diaktifkan.');
            }

            $tanggalHariIni = now();
            $tahun = $tanggalHariIni->format('y');

            $prefixPenerimaan = '1P' . $tahun;
            $lastPenerimaan = PenerimaanAset::where('Id_Penerimaan', 'like', "$prefixPenerimaan%")
                ->orderByDesc('Id_Penerimaan')->first();
            $noUrutPenerimaan = $lastPenerimaan ? intval(substr($lastPenerimaan->Id_Penerimaan, 4)) + 1 : 1;
            $idPenerimaan = $prefixPenerimaan . str_pad($noUrutPenerimaan, 2, '0', STR_PAD_LEFT);

            // === UPLOAD FILE TO CLOUD ===
            $uploadResult = Cloudinary::upload($request->file('dokumen')->getRealPath(), [
                'folder' => 'dokumen_Sekolah/dokumen_pengaktifkan',
                'resource_type' => 'auto',
                'public_id' => $idPenerimaan . '_pengaktifkan'
            ]);

            $fileUrl = $uploadResult->getSecurePath(); // URL untuk disimpan

            // Simpan ke PenerimaanAset
            PenerimaanAset::create([
                'Id_Penerimaan' => $idPenerimaan,
                'Tanggal_Terima' => $tanggalHariIni,
                'Keterangan' =>
                    "=== Informasi Pengaktifan Aset ===\n"
                    . "Tanggal Pengaktifan : " . now()->format('d-m-Y H:i') . "\n"
                    . "ID Aset             : " . $aset->Id_Aset . "\n"
                    . "Nama Aset           : " . $aset->Nama_Aset . "\n"
                    . "Status Sebelumnya: Tidak Aktif\n"
                    . "Kondisi Sebelumnya: " . $aset->Kondisi . "\n"
                    . "Kondisi Setelah Pengaktifan: Baik\n"
                    . ($request->keterangan ? "\nCatatan Tambahan:\n" . $request->keterangan . "\n" : ''),
                'Dokumen_Penerimaan' => $fileUrl,
                'User_Id' => auth()->id(),
            ]);

            $lastDetail = DetailPenerimaanAset::orderByDesc('Id_Detail_Penerimaan')->first();
            $detailUrut = $lastDetail ? intval(substr($lastDetail->Id_Detail_Penerimaan, 2)) + 1 : 1;
            $idDetail = '1D' . str_pad($detailUrut, 4, '0', STR_PAD_LEFT);

            DetailPenerimaanAset::create([
                'Id_Detail_Penerimaan' => $idDetail,
                'Id_Penerimaan' => $idPenerimaan,
                'Id_Aset' => $aset->Id_Aset,
            ]);

            $aset->update([
                'STATUS' => 'aktif',
                'Kondisi' => 'baik',
                'Tanggal_Penerimaan' => $tanggalHariIni,
            ]);

            DB::commit();
            return redirect()->route('pengaturan.pengaktifan')->with('success', 'Aset berhasil diaktifkan kembali.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengaktifkan aset: ' . $e->getMessage());
        }
    }

}
