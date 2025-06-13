<?php

// 📁 File: app/Exports/PenempatanSheet.php

namespace App\Exports;

use App\Models\PenempatanAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class PenempatanSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        $lokasi = \App\Models\Lokasi::pluck('Nama_Lokasi', 'Id_Lokasi')->toArray();

        $data = PenempatanAset::with('detail.lokasi')
            ->get()
            ->map(function ($item) use ($lokasi) {
                $jumlahPerLokasi = [];

                foreach ($lokasi as $id => $nama) {
                    $jumlah = $item->detail->where('Id_Lokasi', $id)->count();
                    $jumlahPerLokasi[$nama] = $jumlah;
                }

                return array_merge([
                    'ID Penempatan' => $item->Id_Penempatan,
                    'Tanggal' => $item->Tanggal_Penempatan,
                    'Petugas' => $item->user->name ?? '-',
                ], $jumlahPerLokasi);
            });

        return new Collection($data);
    }

    public function title(): string
    {
        return 'Aktivitas Penempatan';
    }
}
