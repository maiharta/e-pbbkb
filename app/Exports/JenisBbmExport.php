<?php

namespace App\Exports;

use App\Models\JenisBbm;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class JenisBbmExport extends DefaultValueBinder implements WithCustomValueBinder, FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
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
        return JenisBbm::all();
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
        return 'Jenis BBM';
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->kode,
            $data->nama,
            $data->is_subsidi ? 'Subsidi' : 'Non Subsidi',
            $data->persentase_tarif . '%'
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    private function getHeader()
    {
        return [
            'jenis_bbm_id',
            'Kode',
            'Nama',
            'Subsidi/Non Subsidi',
            'Persentase Tarif'
        ];
    }
}
