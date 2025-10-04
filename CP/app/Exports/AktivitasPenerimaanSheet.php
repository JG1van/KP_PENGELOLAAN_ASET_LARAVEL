<?php
namespace App\Exports;

use App\Models\PenerimaanAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class AktivitasPenerimaanSheet implements
    FromCollection,
    WithTitle,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    private $data;

    public function __construct()
    {
        // Ambil data penerimaan beserta detail aset dan user, urut berdasarkan Id_Penerimaan
        $this->data = PenerimaanAset::with('user', 'detailPenerimaan.aset')
            ->orderBy('Id_Penerimaan')
            ->get();
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->data as $penerimaan) {
            foreach ($penerimaan->detailPenerimaan as $detail) {
                $rows->push([
                    'Id_Penerimaan' => $penerimaan->Id_Penerimaan,
                    'Tanggal_Terima' => $penerimaan->Tanggal_Terima,
                    'Petugas' => $penerimaan->user->name ?? '-',
                    'Id_Aset' => $detail->aset->Id_Aset ?? '-',
                    'Nama_Aset' => $detail->aset->Nama_Aset ?? '-',
                    'Nilai_Awal' => $detail->aset->Nilai_Aset_Awal ?? '-',
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'ID Penerimaan',
            'Tanggal',
            'Petugas',
            'ID Aset',
            'Nama Aset',
            'Nilai Awal Aset',
        ];
    }

    public function map($row): array
    {
        return [
            $row['Id_Penerimaan'],
            $row['Tanggal_Terima'],
            $row['Petugas'],
            $row['Id_Aset'],
            $row['Nama_Aset'],
            'Rp ' . number_format($row['Nilai_Awal'], 0, ',', '.'),

        ];
    }

    public function title(): string
    {
        return 'Penerimaan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            3 => ['font' => ['bold' => true]], // header
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Tambahkan judul & subjudul
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'ASET SLB PAMARDI PUTRA');
                $sheet->setCellValue('A2', 'Tahun ' . date('Y'));

                $colCount = count($this->headings());
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

                // Styling judul
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

                // Styling header
                $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Styling data
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:{$lastCol}{$highestRow}")->applyFromArray([
                    'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
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
            },
        ];
    }
}
