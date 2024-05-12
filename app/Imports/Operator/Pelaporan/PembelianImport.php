<?php

namespace App\Imports\Operator\Pelaporan;

use App\Models\Pelaporan;
use App\Models\Pembelian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PembelianImport implements ToCollection, WithHeadingRow, WithValidation, WithMultipleSheets
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
            Pembelian::create([
                'pelaporan_id' => $this->pelaporan->id,
                'penjual' => $row['penjual'],
                'kabupaten_id' => $row['kabupaten_id'],
                'jenis_bbm_id' => $row['jenis_bbm_id'],
                'volume' => $row['volume']
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'penjual' => 'required',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
        ];
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = $this;

        return $sheets;
    }
}
