<?php

namespace App\Exports;

use App\Models\PenempatanAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class AktivitasPenempatanSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    protected $lokasiUnik = [];

    public function __construct()
    {
        // Ambil semua lokasi unik dari detail penempatan
        $this->lokasiUnik = \App\Models\DetailPenempatan::with('lokasi')
            ->get()
            ->pluck('lokasi.Nama_Lokasi')
            ->unique()
            ->filter()
            ->values()
            ->toArray();
    }

    public function collection()
    {
        return PenempatanAset::with('user', 'detail.lokasi')->orderBy('Tanggal_Penempatan')->get();
    }

    public function title(): string
    {
        return 'Penempatan';
    }

    public function headings(): array
    {
        return array_merge(
            ['ID Penempatan', 'Tanggal', 'Petugas', 'Jumlah Aset'],
            $this->lokasiUnik
        );
    }

    public function map($penempatan): array
    {
        $lokasiCount = array_fill_keys($this->lokasiUnik, 0);

        foreach ($penempatan->detail as $d) {
            $namaLokasi = $d->lokasi->Nama_Lokasi ?? 'Tidak Diketahui';
            if (isset($lokasiCount[$namaLokasi])) {
                $lokasiCount[$namaLokasi]++;
            }
        }

        // Convert semua nilai jadi string agar Excel tidak menghilangkan angka 0
        $lokasiCountString = array_map(fn($val) => (string) $val, $lokasiCount);

        return array_merge([
            $penempatan->Id_Penempatan,
            $penempatan->Tanggal_Penempatan,
            $penempatan->user->name ?? '-',
            (string) $penempatan->detail->count(),
        ], $lokasiCountString);
    }


    public function styles(Worksheet $sheet)
    {
        return [
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'ASET SLB PAMARDI PUTRA');
                $sheet->setCellValue('A2', 'Tahun ' . date('Y'));

                $colCount = count($this->headings());
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");

                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'font' => ['bold' => true, 'size' => 16],
                ]);

                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'font' => ['bold' => true, 'size' => 12],
                ]);

                $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A4:{$lastCol}{$highestRow}")->applyFromArray([
                    'font' => ['bold' => false],
                ]);

                $sheet->getStyle("A3:{$lastCol}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
