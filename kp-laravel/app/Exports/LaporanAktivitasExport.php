<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanAktivitasExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new AktivitasPenerimaanSheet(),
            new AktivitasPengecekanSheet(),
            new AktivitasPenghapusanSheet(),
        ];
    }
}
