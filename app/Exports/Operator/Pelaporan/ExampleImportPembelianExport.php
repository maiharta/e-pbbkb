<?php

namespace App\Exports\Operator\Pelaporan;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExampleImportPembelianExport implements FromCollection, ShouldAutoSize, WithStyles, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return new Collection([[
            'PT. Pertamina',
            'Jl. Tukad Ayung No. 5, Denpasar, Bali',
            1,
            500,
            150000,
            'INV_12345',
            '30-03-2024',
        ]]);
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
        return 'Contoh Data';
    }

    private function getHeader()
    {
        return [
            'penjual',
            'alamat',
            'jenis_bbm_id',
            'sisa_volume',
            'volume',
            'nomor_kuitansi',
            'tanggal_pembelian'
        ];
    }
}
