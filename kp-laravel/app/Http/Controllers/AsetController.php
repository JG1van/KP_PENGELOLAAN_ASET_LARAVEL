<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Kategori;
use App\Models\PenerimaanAset;
use App\Models\DetailPenerimaanAset;
use App\Models\PenurunanAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AsetController extends Controller
{
    public function index()
    {
        $data = Aset::with([
            'kategori:Id_Kategori,Nama_Kategori',
            'detailPenerimaan.penerimaan:Id_Penerimaan,Tanggal_Terima',
            'PenurunanTerbaru:Id_Penurunan,Id_Aset,Nilai_Saat_Ini',
            'penempatanTerakhir.lokasi:Id_Lokasi,Nama_Lokasi'
        ])->get();

        $kategoriList = cache()->remember('kategori_list', 60, fn() => Kategori::all());

        $total_nilai_aset_aktif = $data->reduce(function ($total, $aset) {
            return $total + (($aset->STATUS === 'Aktif' && $aset->PenurunanTerbaru) ? $aset->PenurunanTerbaru->Nilai_Saat_Ini : 0);
        }, 0);

        $total_nilai_aset_keseluruhan = $data->sum(fn($aset) => $aset->PenurunanTerbaru->Nilai_Saat_Ini ?? 0);

        return view('aset.index', compact(
            'data',
            'kategoriList',
            'total_nilai_aset_aktif',
            'total_nilai_aset_keseluruhan'
        ));
    }

    public function create()
    {
        $kategori = cache()->remember('kategori_list', 60, fn() => Kategori::all());
        return view('aset.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_penerimaan' => 'required|date',
            'nama' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'barang_json' => 'required|json',
        ]);

        DB::beginTransaction();
        try {
            $tanggal = $request->tanggal_penerimaan;
            $tahun = date('Y', strtotime($tanggal));
            $prefix = '1P' . date('y', strtotime($tanggal));

            $last = PenerimaanAset::where('Id_Penerimaan', 'like', "$prefix%")
                ->orderByDesc('Id_Penerimaan')->lockForUpdate()->first();

            $idPenerimaan = $prefix . str_pad($last ? intval(substr($last->Id_Penerimaan, 4)) + 1 : 1, 2, '0', STR_PAD_LEFT);

            $filename = null;
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $publicId = "Dokumen_Penerimaan_" . date('Y-m-d', strtotime($tanggal)) . "_$idPenerimaan";
                $uploaded = Cloudinary::upload($file->getRealPath(), [
                    'folder' => 'dokumen_Sekolah/Dokumen_Penerimaan',
                    'public_id' => $publicId,
                    'resource_type' => 'auto'
                ]);
                $filename = $uploaded->getSecurePath();
            }

            $items = json_decode($request->barang_json);
            if (!is_array($items) || empty($items))
                throw new \Exception("Data barang tidak valid.");

            foreach ($items as $item) {
                if (
                    !isset($item->nama, $item->kategori, $item->kategori_text, $item->jumlah, $item->nilai) ||
                    !is_numeric($item->jumlah) || $item->jumlah < 1 ||
                    !is_numeric($item->nilai) || $item->nilai < 0
                ) {
                    throw new \Exception("Format data barang tidak valid.");
                }
            }

            $penerimaan = PenerimaanAset::create([
                'Id_Penerimaan' => $idPenerimaan,
                'Tanggal_Terima' => $tanggal,
                'Keterangan' => '',
                'Dokumen_Penerimaan' => $filename,
                'User_Id' => auth()->id(),
            ]);

            $asetUrut = optional(Aset::select('Id_Aset')->orderByDesc('Id_Aset')->lockForUpdate()->first())->Id_Aset;
            $asetUrut = $asetUrut ? intval(substr($asetUrut, 1)) : 0;

            $detailUrut = optional(DetailPenerimaanAset::select('Id_Detail_Penerimaan')->orderByDesc('Id_Detail_Penerimaan')->lockForUpdate()->first())->Id_Detail_Penerimaan;
            $detailUrut = $detailUrut ? intval(substr($detailUrut, 2)) + 1 : 1;

            $index = 1;
            $totalAsetBaru = 0;
            $keteranganLines = [
                "Sumber:",
                "Nama: {$request->nama}",
                "Telepon: {$request->telepon}",
                "",
                "Daftar Barang:"
            ];

            foreach ($items as $item) {
                $jumlah = (int) $item->jumlah;
                $nilai = (float) $item->nilai;
                $total = $jumlah * $nilai;

                $keteranganLines[] = sprintf(
                    "%-3s %-25s %-15s %-6s %-10s %-12s",
                    $index++,
                    $item->nama,
                    $item->kategori_text,
                    $jumlah,
                    $nilai,
                    $total
                );

                for ($i = 0; $i < $jumlah; $i++) {
                    $idAset = 'A' . str_pad(++$asetUrut, 4, '0', STR_PAD_LEFT);
                    $idDetail = '1D' . str_pad($detailUrut++, 4, '0', STR_PAD_LEFT);

                    Aset::create([
                        'Id_Aset' => $idAset,
                        'Nama_Aset' => $item->nama,
                        'Id_Kategori' => $item->kategori,
                        'Tanggal_Penerimaan' => $tanggal,
                        'Nilai_Aset_Awal' => $nilai,
                        'Kondisi' => 'Baik',
                        'STATUS' => 'Aktif',
                    ]);

                    DetailPenerimaanAset::create([
                        'Id_Detail_Penerimaan' => $idDetail,
                        'Id_Penerimaan' => $idPenerimaan,
                        'Id_Aset' => $idAset,
                    ]);

                    PenurunanAset::create([
                        'Id_Penurunan' => $this->generateUniqueIdPenurunan(),
                        'Tahun' => $tahun,
                        'Id_Aset' => $idAset,
                        'Nilai_Saat_Ini' => $nilai,
                    ]);

                    $totalAsetBaru++;
                }
            }

            $penerimaan->update(['Keterangan' => implode("\n", $keteranganLines)]);
            DB::commit();
            return redirect()->route('aset.index')->with('success', "Berhasil menambahkan $totalAsetBaru aset.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $aset = Aset::findOrFail($id);
        $kategori = cache()->remember('kategori_list', 60, fn() => Kategori::all());
        return view('aset.edit', compact('aset', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Aset' => 'required|string|max:100',
            'Id_Kategori' => 'required|exists:kategori,Id_Kategori',
            'Kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'STATUS' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $aset = Aset::findOrFail($id);
        $aset->update($request->only('Nama_Aset', 'Id_Kategori', 'Kondisi', 'STATUS'));

        return redirect()->route('aset.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            Aset::findOrFail($id)->delete();
            return back()->with('success', 'Aset berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $aset = Aset::with(['penurunans', 'kategori', 'detailPenerimaan.penerimaan'])->findOrFail($id);
        $kategori = cache()->remember('kategori_list', 60, fn() => Kategori::all());
        return view('aset.show', compact('aset', 'kategori'));
    }

    public function updateDetail(Request $request, $id)
    {
        $request->validate([
            'Nama_Aset' => 'required|string|max:100',
            'Id_Kategori' => 'required|exists:kategori,Id_Kategori',
        ]);

        Aset::findOrFail($id)->update($request->only('Nama_Aset', 'Id_Kategori'));
        return back()->with('success', 'Detail aset berhasil diperbarui.');
    }

    public function prosesPenurunan(Request $request)
    {
        $request->validate([
            'tahun' => 'required|digits:4',
            'persen' => 'required|numeric|min:1|max:100',
        ]);

        DB::beginTransaction();
        try {
            $asets = Aset::with(['penurunans:Id_Penurunan,Id_Aset,Tahun', 'PenurunanTerbaru:Id_Penurunan,Id_Aset,Nilai_Saat_Ini'])->get();
            $tahunSekarang = date('Y');
            $jumlahDiproses = 0;

            foreach ($asets as $aset) {
                $nilaiSaatIni = $aset->PenurunanTerbaru->Nilai_Saat_Ini ?? $aset->Nilai_Aset_Awal;
                if ($nilaiSaatIni <= 0)
                    continue;

                $tahunMulai = $aset->penurunans->max('Tahun') ?? date('Y', strtotime($aset->Tanggal_Penerimaan));
                $sudahDiproses = false;

                for ($tahun = $tahunMulai + 1; $tahun <= $tahunSekarang; $tahun++) {
                    if ($aset->penurunans->contains('Tahun', $tahun))
                        continue;

                    $nilaiSaatIni -= $nilaiSaatIni * ($request->persen / 100);
                    $nilaiSaatIni = max(round($nilaiSaatIni), 0);

                    PenurunanAset::create([
                        'Id_Penurunan' => $this->generateUniqueIdPenurunan(),
                        'Tahun' => $tahun,
                        'Id_Aset' => $aset->Id_Aset,
                        'Nilai_Saat_Ini' => $nilaiSaatIni,
                    ]);

                    $sudahDiproses = true;
                    if ($nilaiSaatIni === 0)
                        break;
                }

                if ($sudahDiproses)
                    $jumlahDiproses++;
            }

            DB::commit();
            $totalAset = $asets->count();
            return back()->with('success', "Penurunan nilai berhasil. Diproses: $jumlahDiproses dari $totalAset aset.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    private function generateUniqueIdPenurunan()
    {
        do {
            $id = $this->generateRandomId(4);
        } while (PenurunanAset::where('Id_Penurunan', $id)->exists());
        return $id;
    }

    private function generateRandomId($length = 4)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
    }
}
