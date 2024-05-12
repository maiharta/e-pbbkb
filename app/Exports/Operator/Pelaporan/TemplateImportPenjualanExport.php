<?php

namespace App\Exports\Operator\Pelaporan;

use App\Exports\SektorExport;
use App\Exports\JenisBbmExport;
use App\Exports\KabupatenExport;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplateImportPenjualanExport implements FromCollection, ShouldAutoSize, WithMultipleSheets, WithStyles, WithHeadings, WithTitle
{
    use Exportable;
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
            'Penjualan' => $this,
            'Kabupaten/Kota' => new KabupatenExport(),
            'Sektor' => new SektorExport(),
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
        return 'Penjualan';
    }

    private function getHeader()
    {
        return [
            'Pembeli',
            'kabupaten_id',
            'sektor_id',
            'jenis_bbm_id',
            'volume',
            'dpp'
        ];
    }
}
