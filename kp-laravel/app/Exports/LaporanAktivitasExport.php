<?php

namespace App\Exports;
use App\Exports\AktivitasPenempatanSheet;
use App\Exports\AktivitasPenerimaanSheet;
use App\Exports\AktivitasPengecekanSheet;
use App\Exports\AktivitasPenghapusanSheet;
use App\Models\PenempatanAset;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanAktivitasExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new AktivitasPenerimaanSheet(),
            new AktivitasPengecekanSheet(),
            new AktivitasPenghapusanSheet(),
            new AktivitasPenempatanSheet(),
        ];
    }
}
