<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LokasiPenyaluranExport implements FromCollection, ShouldAutoSize, WithStyles, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return new Collection([
            [
                1,
                'Depot'
            ],
            [
                2,
                'TBBM'
            ]
        ]);
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
        return 'Lokasi Penyaluran';
    }

    private function getHeader()
    {
        return [
            'lokasi_penyaluran_id',
            'nama'
        ];
    }
}
