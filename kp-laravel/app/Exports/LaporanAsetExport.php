<?php
namespace App\Exports;

use App\Models\Aset;
use App\Models\PenurunanAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanAsetExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $tahunList;

    public function __construct()
    {
        $this->tahunList = PenurunanAset::select('Tahun')->distinct()->orderBy('Tahun')->pluck('Tahun')->toArray();
    }

    public function collection()
    {
        return Aset::with([
            'kategori',
            'penurunans',
            'lokasiTerakhir',
        ])->get();
    }

    public function headings(): array
    {
        $tahunHeaders = array_map(function ($tahun) {
            return 'Nilai ' . $tahun;
        }, $this->tahunList);

        return array_merge([
            'ID Aset',
            'Nama Aset',
            'Kategori',
            'Lokasi',
            // 'Tanggal Penerimaan',
            'Kondisi',
            'Nilai Awal',
        ], $tahunHeaders);
    }

    public function map($aset): array
    {
        $formatRupiah = function ($nilai) {
            return is_numeric($nilai) ? 'Rp ' . number_format($nilai, 0, ',', '.') : '-';
        };
        $lokasi = optional($aset->lokasiTerakhir)->Nama_Lokasi ?? '-';

        $row = [
            $aset->Id_Aset,
            $aset->Nama_Aset,
            $aset->kategori->Nama_Kategori ?? '-',
            $lokasi,
            // optional(optional($aset->detailPenerimaan)->penerimaan)->Tanggal_Terima ?? '-',
            $aset->Kondisi ?? '-',
            $formatRupiah($aset->Nilai_Aset_Awal),
        ];

        foreach ($this->tahunList as $tahun) {
            $nilai = $aset->penurunans->where('Tahun', $tahun)->first();
            $row[] = $formatRupiah($nilai->Nilai_Saat_Ini ?? null);
        }

        return $row;
    }


    public function styles(Worksheet $sheet)
    {
        return [
            5 => ['font' => ['bold' => true]], // Baris header (setelah insert 2 baris)
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
