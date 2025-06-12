<?php

namespace App\Exports;

use App\Models\PengecekanAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class AktivitasPengecekanSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    public function collection()
    {
        return PengecekanAset::with(['user', 'detail'])->orderBy('Tanggal_Pengecekan')->get();
    }

    public function headings(): array
    {
        return [
            'ID Pengecekan',
            'Tanggal',
            'Petugas',
            'Jumlah Barang Diperiksa',
            'Baik',
            'Rusak Sedang',
            'Rusak Berat',
            'Hilang',
            'Diremajakan',
        ];
    }

    public function map($pengecekan): array
    {
        $kondisiCount = [
            'baik' => 0,
            'rusak sedang' => 0,
            'rusak berat' => 0,
            'hilang' => 0,
            'diremajakan' => 0,
            'lainnya' => 0, // Untuk kondisi tidak valid / kosong
        ];

        foreach ($pengecekan->detail as $d) {
            $k = strtolower(trim($d->Kondisi));
            if (isset($kondisiCount[$k])) {
                $kondisiCount[$k]++;
            } else {
                $kondisiCount['lainnya']++; // Jika null, kosong, atau tidak cocok
            }
        }

        return [
            $pengecekan->Id_Pengecekan,
            $pengecekan->Tanggal_Pengecekan,
            $pengecekan->user->name ?? '-',
            (string) $pengecekan->detail->count(),
            (string) ($kondisiCount['baik'] ?? '0'),
            (string) ($kondisiCount['rusak sedang'] ?? '0'),
            (string) ($kondisiCount['rusak berat'] ?? '0'),
            (string) ($kondisiCount['hilang'] ?? '0'),
            (string) ($kondisiCount['diremajakan'] ?? '0'),
        ];



    }


    public function title(): string
    {
        return 'Pengecekan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            3 => ['font' => ['bold' => true]], // Baris header (setelah insert 2 baris)
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Tambah Judul
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'ASET SLB PAMARDI PUTRA');
                $sheet->setCellValue('A2', 'Tahun ' . date('Y'));

                $colCount = count($this->headings());
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

                // Merge cell judul dan subjudul
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");

                // Style judul
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                ]);

                // Style subjudul
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                ]);

                // Header tabel (baris ke-3)
                $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                // Data (baris ke-4 dan seterusnya) -> pastikan tidak bold
                $startDataRow = 4;
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A{$startDataRow}:{$lastCol}{$highestRow}")->applyFromArray([
                    'font' => ['bold' => false],
                ]);

                // Border seluruh isi tabel (termasuk header)
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
