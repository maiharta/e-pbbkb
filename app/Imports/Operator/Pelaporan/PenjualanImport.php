<?php

namespace App\Imports\Operator\Pelaporan;

use App\Models\JenisBbm;
use App\Models\Sektor;
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
        $sektors = Sektor::get();
        $jenis_bbms = JenisBbm::get();
        foreach ($collection as $row) {
            $jenis_bbm = $jenis_bbms->where('id', $row['jenis_bbm_id'])->first();
            $sektor = $sektors->where('id', $row['sektor_id'])->first();
            Penjualan::create([
                'pelaporan_id' => $this->pelaporan->id,
                'pembeli' => $row['pembeli'],
                'kabupaten_id' => $row['kabupaten_id'],
                'sektor_id' => $row['sektor_id'],
                'jenis_bbm_id' => $row['jenis_bbm_id'],
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'kode_sektor' => $sektor->kode,
                'nama_sektor' => $sektor->nama,
                'persentase_tarif_sektor' => $sektor->persentase_tarif,
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
