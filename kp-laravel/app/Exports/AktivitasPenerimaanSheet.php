<?php
namespace App\Exports;

use App\Models\PenerimaanAset;
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

class AktivitasPenerimaanSheet implements
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
        $this->data = PenerimaanAset::with('user', 'detailPenerimaan')->orderBy('Tanggal_Terima')->get();
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID Penerimaan',
            'Tanggal',
            'Petugas',
            'Jumlah Barang',
            'Dokumen (QR)',
        ];
    }

    public function map($penerimaan): array
    {
        return [
            $penerimaan->Id_Penerimaan,
            $penerimaan->Tanggal_Terima,
            $penerimaan->user->name ?? '-',
            $penerimaan->detailPenerimaan->count(),
            '', // QR code akan dimasukkan melalui drawings()
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $row = 2; // Data dimulai dari baris ke-4 (karena ada 2 baris judul + 1 baris header)

        foreach ($this->data as $penerimaan) {
            $dokumen = $penerimaan->Dokumen_Penerimaan;
            if (!$dokumen) {
                $row++;
                continue;
            }

            $qrPath = storage_path("app/public/qrcodes/qrdoc_" . $penerimaan->Id_Penerimaan . ".png");
            QrCode::format('png')->size(300)->generate($dokumen, $qrPath);
            $this->qrPaths[] = $qrPath;

            $drawing = new Drawing();
            $drawing->setName('QR');
            $drawing->setDescription('QR Dokumen');
            $drawing->setPath($qrPath);
            $drawing->setHeight(80);
            $drawing->setWidth(80);
            $drawing->setCoordinates('E' . $row);
            $drawing->setOffsetX(25); // <-- ini yang disesuaikan agar QR benar-benar center horizontal
            $drawing->setOffsetY(15);  // sudah center vertical
            $drawings[] = $drawing;
            $row++;
        }

        return $drawings;
    }

    public function title(): string
    {
        return 'Penerimaan';
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

                // Tambahkan judul dan subjudul
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'ASET SLB PAMARDI PUTRA');
                $sheet->setCellValue('A2', 'Tahun ' . date('Y'));

                $colCount = count($this->headings());
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

                // Judul
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

                // Header
                $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Data style
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:{$lastCol}{$highestRow}")->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'font' => ['bold' => false],
                ]);

                // Border
                $sheet->getStyle("A3:{$lastCol}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $sheet->getStyle("E4:E{$highestRow}")->getAlignment()->applyFromArray([
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]);

                // Ukuran baris & kolom QR
                for ($i = 4; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(80);
                }
                $sheet->getColumnDimension('E')->setWidth(80);
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
