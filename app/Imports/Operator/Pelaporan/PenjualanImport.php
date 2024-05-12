<?php

namespace App\Imports\Operator\Pelaporan;

use App\Models\Pelaporan;
use App\Models\Penjualan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PenjualanImport implements ToCollection, WithHeadingRow, WithValidation, WithMultipleSheets
{

    public $pelaporan;

    public function __construct(Pelaporan $pelaporan)
    {
        $this->pelaporan = $pelaporan;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // dd($row);
            Penjualan::create([
                'pelaporan_id' => $this->pelaporan->id,
                'pembeli' => $row['pembeli'],
                'kabupaten_id' => $row['kabupaten_id'],
                'sektor_id' => $row['sektor_id'],
                'jenis_bbm_id' => $row['jenis_bbm_id'],
                'volume' => $row['volume'],
                'dpp' => $row['dpp'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'pembeli' => 'required',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'sektor_id' => 'required|exists:sektors,id',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
            'dpp' => 'required'
        ];
    }
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = $this;

        return $sheets;
    }
}
