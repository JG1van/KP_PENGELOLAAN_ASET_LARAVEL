<?php

namespace App\Exports;

use App\Models\PenghapusanAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AktivitasPenghapusanSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents,
    WithDrawings
{
    private $data;
    private $qrPaths = [];

    public function __construct()
    {
        $this->data = PenghapusanAset::with('user', 'detail.aset')->orderBy('Tanggal_Hapus')->get();
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID Penghapusan',
            'Tanggal Penghapusan',
            'Petugas',
            'Jumlah Aset Dihapus',
            'Dokumen (QR)',
        ];
    }

    public function map($penghapusan): array
    {
        return [
            $penghapusan->Id_Penghapusan,
            $penghapusan->Tanggal_Hapus,
            $penghapusan->user->name ?? '-',
            $penghapusan->detail->count(),
            '', // QR akan ditambahkan di drawings()
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $row = 2;

        foreach ($this->data as $penghapusan) {
            $dokumen = $penghapusan->Dokumen_Penghapusan;
            if (!$dokumen) {
                $row++;
                continue;
            }

            $qrPath = storage_path("app/public/qrcodes/qrdoc_" . $penghapusan->Id_Penghapusan . ".png");
            QrCode::format('png')->size(300)->generate($dokumen, $qrPath);
            $this->qrPaths[] = $qrPath;

            $drawing = new Drawing();
            $drawing->setName('QR');
            $drawing->setDescription('QR Dokumen');
            $drawing->setPath($qrPath);
            $drawing->setHeight(80);
            $drawing->setWidth(80);
            $drawing->setCoordinates('E' . $row);
            $drawing->setOffsetX(25);
            $drawing->setOffsetY(15);
            $drawings[] = $drawing;

            $row++;
        }

        return $drawings;
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
                $sheet->getStyle("A4:{$lastCol}{$highestRow}")->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
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

                // Column & Row size adjustment for QR
                $sheet->getColumnDimension('E')->setWidth(80);
                for ($i = 4; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(80);
                }
            },
        ];
    }

    public function __destruct()
    {
        foreach ($this->qrPaths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
