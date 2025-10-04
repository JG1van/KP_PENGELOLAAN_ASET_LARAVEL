<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Aset;
use App\Models\PengecekanAset;
use App\Models\PenghapusanAset;
use App\Exports\PenempatanSheet;
use App\Exports\LaporanAsetExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAktivitasExport;
use App\Models\PenerimaanAset;
use App\Models\DetailPenerimaanAset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


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
        $User_Id = generateUniqueId('users', 'User_Id');


        User::create([
            'User_Id' => $User_Id,
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
        return Excel::download(new LaporanAktivitasExport, 'Laporan_Aktivitas_Aset_Tanggal_' . now()->format('Y-m-d') . '.xlsx');
    }
    public function exportLaporanAset()
    {
        return Excel::download(new LaporanAsetExport, 'laporan_aset_Tanggal_' . now()->format('Y-m-d') . '.xlsx');
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
            $idPenerimaan = generateUniqueId('penerimaan_aset', 'Id_Penerimaan');

            // === SIMPAN FILE DI SERVER ===
            $file = $request->file('dokumen');
            $filename = "Dokumen_Pengaktifan_ID_{$idPenerimaan}_Tanggal_" . date('Y-m-d', strtotime($tanggalHariIni)) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/Dokumen_Penerimaan', $filename);

            // Buat URL untuk disimpan di DB
            // $fileUrl = $filename;

            // Simpan ke PenerimaanAset
            PenerimaanAset::create([
                'Id_Penerimaan' => $idPenerimaan,
                'Tanggal_Terima' => $tanggalHariIni,
                'Keterangan' =>
                    "=== Informasi Pengaktifan Aset ===\n"
                    . "Tanggal Pengaktifan : " . now()->format('d-m-Y H:i') . "\n"
                    . "ID Aset             : " . $aset->Id_Aset . "\n"
                    . "Nama Aset           : " . $aset->Nama_Aset . "\n"
                    . "Status Sebelumnya   : Tidak Aktif\n"
                    . "Kondisi Sebelumnya  : " . $aset->Kondisi . "\n"
                    . "Kondisi Setelah Pengaktifan : Baik\n"
                    . ($request->keterangan ? "\nCatatan Tambahan:\n" . $request->keterangan . "\n" : ''),
                'Dokumen_Penerimaan' => $filename,
                'User_Id' => auth()->id(),
            ]);

            $idDetail = generateUniqueId('detail_penerimaan_aset', 'Id_Detail_Penerimaan');

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
