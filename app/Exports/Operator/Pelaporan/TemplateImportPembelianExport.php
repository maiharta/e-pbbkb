<?php

namespace App\Exports\Operator\Pelaporan;

use App\Exports\JenisBbmExport;
use App\Exports\KabupatenExport;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplateImportPembelianExport implements FromCollection, ShouldAutoSize, WithMultipleSheets, WithStyles, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect();
    }

    public function sheets(): array
    {
        $sheets = [
            // this export
            'Pembelian' => $this,
            'Kabupaten/Kota' => new KabupatenExport(),
            'Jenis BBM' => new JenisBbmExport(),
        ];

        return $sheets;
    }

    public function styles($sheet)
    {
        $numColumns = count($this->getHeader());
        $lastColumnLetter = Coordinate::stringFromColumnIndex($numColumns);
        $cellRange = 'A1:' . $lastColumnLetter . $sheet->getHighestRow();
        $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return $this->getHeader();
    }

    public function title(): string
    {
        return 'Pembelian';
    }

    private function getHeader()
    {
        return [
            'Penjual',
            'kabupaten_id',
            'jenis_bbm_id',
            'volume',
        ];
    }
}
