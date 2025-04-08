<?php

namespace App\Imports\Operator\Pelaporan;

use Carbon\Carbon;
use App\Models\Sektor;
use App\Models\JenisBbm;
use App\Models\Pelaporan;
use App\Models\Penjualan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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
        DB::beginTransaction();
        foreach ($collection as $row) {
            $tanggal_carbon = is_string($row['tanggal_penjualan']) ? Carbon::parse($row['tanggal_penjualan']) : Carbon::instance(Date::excelToDateTimeObject($row['tanggal_penjualan']));
            if ((int) $tanggal_carbon->format('m') != $this->pelaporan->bulan) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terdapat data pelaporan dengan bulan berbeda dengan bulan pelaporan pada file excel');
            }
            $jenis_bbm = $jenis_bbms->where('id', $row['jenis_bbm_id'])->first();
            $sektor = $sektors->where('id', $row['sektor_id'])->first();
            Penjualan::create([
                'pelaporan_id' => $this->pelaporan->id,
                'pembeli' => $row['pembeli'],
                'alamat' => $row['alamat'],
                'sektor_id' => $row['sektor_id'],
                'kode_sektor' => $sektor->kode,
                'nama_sektor' => $sektor->nama,
                'persentase_pengenaan_sektor' => $sektor->persentase_pengenaan,
                'jenis_bbm_id' => $row['jenis_bbm_id'],
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'lokasi_penyaluran' => $row['lokasi_penyaluran_id'] == 1 ? 'depot' : 'TBBM',
                'is_wajib_pajak' => $row['status_pajak_id'] == 2 ? 1 : 0,
                'volume' => $row['volume'],
                'dpp' => $row['dpp'],
                'pbbkb' => $row['pbbkb'],
                'nomor_kuitansi' => $row['nomor_kuitansi'],
                'tanggal' => $tanggal_carbon->format('Y-m-d'),
            ]);
        }
        DB::commit();
    }

    public function rules(): array
    {
        return [
            'pembeli' => 'required',
            'alamat' => 'required',
            'lokasi_penyaluran_id' => 'required|in:1,2',
            'status_pajak_id' => 'required|in:1,2',
            'sektor_id' => 'required|exists:sektors,id',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
            'dpp' => 'required',
            'pbbkb' => 'required',
            'nomor_kuitansi' => 'required',
            'tanggal_penjualan' => 'required',
        ];
    }
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = $this;

        return $sheets;
    }
}
