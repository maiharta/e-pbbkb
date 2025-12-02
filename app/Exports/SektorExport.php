<?php

namespace App\Exports;

use App\Models\Sektor;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class SektorExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    use Exportable;

    public function __construct()
    {
        //
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Sektor::all();
    }

    public function headings(): array
    {
        return $this->getHeader();
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

    public function title(): string
    {
        return 'Sektor';
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->kode,
            $data->nama,
            $data->persentase_tarif . '%'
        ];
    }

    private function getHeader()
    {
        return [
            'sektor_id',
            'Kode',
            'Nama',
            'Persentase Tarif'
        ];
    }
}
