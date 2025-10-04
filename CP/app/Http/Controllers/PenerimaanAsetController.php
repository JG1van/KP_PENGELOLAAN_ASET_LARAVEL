<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanAset;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PenerimaanAsetController extends Controller
{
    public function index()
    {
        $data = PenerimaanAset::with('user')->get();

        return view('penerimaan.index', compact('data'));
    }


    public function show($id)
    {
        $penerimaan = PenerimaanAset::with(['user', 'detailPenerimaan.aset.kategori'])->findOrFail($id);
        $files = [];

        if ($penerimaan->Dokumen_Penerimaan) {
            $dokumen = $penerimaan->Dokumen_Penerimaan;
            if (is_array($dokumen)) {
                foreach ($dokumen as $file) {
                    $files[] = [
                        'url' => asset('storage/dokumen_penerimaan/' . $file),
                        'name' => $file
                    ];
                }
            } else {
                // single file
                $files[] = [
                    'url' => asset('storage/dokumen_penerimaan/' . $penerimaan->Dokumen_Penerimaan),
                    'name' => $penerimaan->Dokumen_Penerimaan
                ];
            }
        }

        return view('penerimaan.show', compact('penerimaan', 'files'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'Tanggal_Terima' => 'required|date',
            'Keterangan' => 'required|string',
        ]);

        $penerimaan = PenerimaanAset::findOrFail($id);
        $penerimaan->update($request->only('Tanggal_Terima', 'Keterangan'));

        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penerimaan = PenerimaanAset::with('detailPenerimaan.aset')->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($penerimaan->detailPenerimaan as $detail) {
                $aset = $detail->aset;

                if ($aset->penghapusanDetails()->exists()) {
                    return back()->with('error', 'Aset tidak dapat dihapus karena sudah terdaftar dalam proses penghapusan.');
                }

                if ($aset->pengecekanDetails()->exists()) {
                    return back()->with('error', 'Aset tidak dapat dihapus karena sudah melewati proses pengecekan.');
                }

                if ($aset->detailPenempatan()->exists()) {
                    return back()->with('error', 'Aset tidak dapat dihapus karena sudah memiliki riwayat penempatan.');
                }

                $aset->delete();
                $detail->delete(); // hapus detail_penerimaan
            }

            $penerimaan->delete(); // hapus data utama
            DB::commit();

            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }



    public function qr($id)
    {
        $penerimaan = PenerimaanAset::with('detailPenerimaan.aset')->findOrFail($id);
        $asetList = $penerimaan->detailPenerimaan->pluck('aset');

        return view('qr', compact('penerimaan', 'asetList'));
    }

    public function exportQrPdf($id)
    {
        $penerimaan = PenerimaanAset::with('detailPenerimaan.aset')->findOrFail($id);
        $asetList = $penerimaan->detailPenerimaan->pluck('aset');

        foreach ($asetList as $aset) {
            $filename = 'qrcodes/' . $aset->Id_Aset . '.png';

            if (!Storage::disk('public')->exists($filename)) {
                $qrImage = QrCode::format('svg')->size(200)->generate($aset->Id_Aset);

                Storage::disk('public')->put($filename, $qrImage);
            }

            // Simpan path absolut untuk digunakan di view PDF
            $aset->qrPath = public_path('storage/' . $filename);
        }

        return Pdf::loadView('qr_pdf', compact('penerimaan', 'asetList'))
            ->setPaper('a4', 'portrait')
            ->stream('qr_penerimaan_' . $penerimaan->Id_Penerimaan . '.pdf');
    }
}
