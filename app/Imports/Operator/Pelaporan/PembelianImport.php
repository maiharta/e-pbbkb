<?php

namespace App\Imports\Operator\Pelaporan;

use App\Models\Pelaporan;
use App\Models\Pembelian;
use Carbon\Carbon;
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
            Pembelian::create([
                'pelaporan_id' => $this->pelaporan->id,
                'penjual' => $row['penjual'],
                'alamat' => $row['alamat'],
                'jenis_bbm_id' => $row['jenis_bbm_id'],
                'sisa_volume' => $row['sisa_volume'],
                'volume' => $row['volume'],
                'nomor_kuitansi' => $row['nomor_kuitansi'],
                'tanggal' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_pembelian']))->format('Y-m-d'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'penjual' => 'required',
            'alamat' => 'required',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'sisa_volume' => 'required',
            'volume' => 'required',
            'nomor_kuitansi' => 'required',
            'tanggal_pembelian' => 'required',
        ];
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = $this;

        return $sheets;
    }
}
