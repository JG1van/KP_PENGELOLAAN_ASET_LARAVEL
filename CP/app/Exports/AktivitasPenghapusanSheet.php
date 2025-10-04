<?php

namespace App\Exports;

use App\Models\PenghapusanAset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class AktivitasPenghapusanSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    private $rows;

    public function __construct()
    {
        $data = PenghapusanAset::with('user', 'detail.aset')
            ->orderBy('Tanggal_Hapus')
            ->get();

        $this->rows = new Collection();

        foreach ($data as $penghapusan) {
            foreach ($penghapusan->detail as $detail) {
                $this->rows->push([
                    'Id_Penghapusan' => $penghapusan->Id_Penghapusan,
                    'Tanggal_Hapus' => $penghapusan->Tanggal_Hapus, // Carbon
                    'Petugas' => $penghapusan->user->name ?? '-',
                    'Id_Aset' => $detail->aset->Id_Aset ?? '-',
                    'Nama_Aset' => $detail->aset->Nama_Aset ?? '-',
                ]);
            }
        }
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'ID Penghapusan',
            'Tanggal Penghapusan',
            'Petugas',
            'ID Aset',
            'Nama Aset',
        ];
    }

    public function map($row): array
    {
        return [
            $row['Id_Penghapusan'],
            $row['Tanggal_Hapus'],
            $row['Petugas'],
            $row['Id_Aset'],
            $row['Nama_Aset'],
        ];
    }

    public function title(): string
    {
        return 'Penghapusan';
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

                // Title styling
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

                // Header style
                $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $highestRow = $sheet->getHighestRow();

                // Data rows style
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:{$lastCol}{$highestRow}")->applyFromArray([
                    'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                    'font' => ['bold' => false],
                ]);

                // Borders
                $sheet->getStyle("A3:{$lastCol}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}
