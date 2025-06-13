<?php

namespace App\Exports;

use App\Models\PenempatanAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class PenempatanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $penempatan;

    public function __construct(PenempatanAset $penempatan)
    {
        $this->penempatan = $penempatan->load('detail.aset.kategori', 'detail.lokasi');
    }

    public function collection()
    {
        return $this->penempatan->detail;
    }

    public function headings(): array
    {
        return [
            'ID Aset',
            'Nama Aset',
            'Kategori',
            'Lokasi Sebelumnya',
            'Lokasi Sekarang',
        ];
    }

    public function map($item): array
    {
        // Lokasi sebelumnya: ambil dari DetailPenempatan sebelum ini
        $lokasiSebelumnya = $item->aset
            ->penempatanDetail()
            ->where('Id_Penempatan', '<', $this->penempatan->Id_Penempatan)
            ->orderByDesc('Id_Penempatan')
            ->first();

        return [
            $item->aset->Id_Aset ?? '-',
            $item->aset->Nama_Aset ?? '-',
            $item->aset->kategori->Nama_Kategori ?? '-',
            $lokasiSebelumnya->lokasi->Nama_Lokasi ?? 'Baru',
            $item->lokasi->Nama_Lokasi ?? '-',
        ];
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

                $sheet->setCellValue('A1', 'Detail Penempatan Aset');
                $sheet->setCellValue('A2', 'ID: ' . $this->penempatan->Id_Penempatan .
                    ' | Tanggal: ' . \Carbon\Carbon::parse($this->penempatan->Tanggal_Penempatan)->format('d M Y') .
                    ' | Petugas: ' . ($this->penempatan->user->name ?? 'Unknown'));

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
