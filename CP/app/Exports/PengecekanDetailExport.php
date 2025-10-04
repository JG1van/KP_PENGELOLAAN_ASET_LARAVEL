<?php

namespace App\Exports;

use App\Models\PengecekanAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class PengecekanDetailExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $pengecekan;

    public function __construct(PengecekanAset $pengecekan)
    {
        $this->pengecekan = $pengecekan->load('detail.aset.kategori');
    }


    public function collection()
    {
        return $this->pengecekan->detail;
    }

    public function headings(): array
    {
        return [
            'ID Aset',
            'Nama Aset',
            'Kategori',
            'Kondisi Sebelum',
            'Kondisi Sesudah',
        ];
    }

    public function map($item): array
    {
        $sebelumnya = $item->aset
            ->detailPengecekan()
            ->where('Id_Pengecekan', '<', $this->pengecekan->Id_Pengecekan)
            ->orderByDesc('Id_Pengecekan')
            ->first();

        return [
            $item->aset->Id_Aset ?? '-',
            $item->aset->Nama_Aset ?? '-',
            $item->aset->kategori->Nama_Kategori ?? '-',
            $sebelumnya ? ucfirst($sebelumnya->Kondisi) : 'Baru',
            ucfirst($item->Kondisi) ?? '-',
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

                // Insert 2 baris di atas
                $sheet->insertNewRowBefore(1, 2);

                // Judul dan subjudul
                $sheet->setCellValue('A1', 'Detail Pengecekan Aset');
                $sheet->setCellValue('A2', 'ID: ' . $this->pengecekan->Id_Pengecekan .
                    ' | Tanggal: ' . \Carbon\Carbon::parse($this->pengecekan->Tanggal_Pengecekan)->format('d M Y') .
                    ' | Petugas: ' . ($this->pengecekan->user->name ?? 'Unknown'));

                $colCount = count($this->headings());
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

                // Merge judul dan subjudul
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");

                // Style judul
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'font' => ['bold' => true, 'size' => 16],
                ]);

                // Style subjudul
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'font' => ['bold' => true, 'size' => 12],
                ]);

                // Tebal hanya header kolom (baris 3 setelah penambahan baris)
                $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                // Data (baris 4 ke bawah) tidak bold
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:{$lastCol}{$highestRow}")->applyFromArray([
                    'font' => ['bold' => false],
                ]);

                // Tambahkan border untuk seluruh isi tabel
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
