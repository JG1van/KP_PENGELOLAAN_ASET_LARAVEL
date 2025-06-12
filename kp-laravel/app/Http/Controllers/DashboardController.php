<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aset;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Aset
        $totalAset = Aset::count();

        // Aset Aktif dan Tidak Aktif
        $asetAktif = Aset::where('Status', 'Aktif')->count();
        $asetTidakAktif = Aset::where('Status', '!=', 'Aktif')->count();

        // Total Nilai Saat Ini Aset Aktif
        $totalNilaiAsetAktif = Aset::where('Status', 'Aktif')
            ->with('PenurunanTerbaru')
            ->get()
            ->sum(fn($aset) => optional($aset->PenurunanTerbaru)->Nilai_Saat_Ini ?? 0);

        // Total Nilai Saat Ini Aset Tidak Aktif
        $totalNilaiAsetTidakAktif = Aset::where('Status', '!=', 'Aktif')
            ->with('PenurunanTerbaru')
            ->get()
            ->sum(fn($aset) => optional($aset->PenurunanTerbaru)->Nilai_Saat_Ini ?? 0);

        // Total Nilai Awal Aset (Tanpa depresiasi)
        $totalNilaiAwalAset = Aset::sum('Nilai_Aset_Awal');


        // Data Kondisi Aset untuk Pie Chart
        $dataKondisi = Aset::select('Kondisi', DB::raw('count(*) as total'))
            ->groupBy('Kondisi')
            ->pluck('total', 'Kondisi');

        // Data Kategori Aset untuk Bar Chart
        $dataKategori = DB::table('aset')
            ->join('kategori', 'aset.ID_Kategori', '=', 'kategori.Id_Kategori')
            ->select('kategori.Nama_Kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori.Nama_Kategori')
            ->pluck('total', 'Nama_Kategori');

        // Jumlah Aset per Tahun untuk ditampilkan dalam tabel
        $dataAsetPerTahun = DB::table('aset')
            ->join('detail_penerimaan_aset', 'aset.Id_Aset', '=', 'detail_penerimaan_aset.Id_Aset')
            ->join('penerimaan_aset', 'detail_penerimaan_aset.Id_Penerimaan', '=', 'penerimaan_aset.Id_Penerimaan')
            ->selectRaw('YEAR(penerimaan_aset.Tanggal_Terima) as tahun, COUNT(*) as jumlah, SUM(aset.Nilai_Aset_Awal) as nilai_awal')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->get()
            ->keyBy('tahun')
            ->map(function ($item) {
                return [
                    'jumlah' => $item->jumlah,
                    'nilai_awal' => $item->nilai_awal,
                ];
            });
        $totalNilaiAsetSaatIni = $totalNilaiAsetAktif + $totalNilaiAsetTidakAktif;

        // Kirim semua data ke view
        return view('dashboard', compact(
            'totalAset',
            'asetAktif',
            'asetTidakAktif',
            'totalNilaiAsetSaatIni',
            'totalNilaiAsetAktif',
            'totalNilaiAsetTidakAktif',
            'totalNilaiAwalAset',
            'dataKondisi',
            'dataKategori',
            'dataAsetPerTahun'
        ));
    }
}
