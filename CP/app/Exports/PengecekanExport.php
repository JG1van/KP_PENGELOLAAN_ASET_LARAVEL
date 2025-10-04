<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PengecekanExport implements FromView
{
    protected $pengecekan;

    public function __construct($pengecekan)
    {
        $this->pengecekan = $pengecekan;
    }

    public function view(): View
    {
        return view('pengecekan.export', [
            'pengecekan' => $this->pengecekan,
        ]);
    }
}
