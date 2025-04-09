<?php

namespace App\Imports\Operator\Pelaporan;

use Carbon\Carbon;
use App\Models\JenisBbm;
use App\Models\Pelaporan;
use App\Models\Pembelian;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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

        $jenis_bbms = JenisBbm::get();
        DB::beginTransaction();
        foreach ($collection as $row) {
            $tanggal_carbon = is_string($row['tanggal_pembelian']) ? Carbon::parse($row['tanggal_pembelian']) : Carbon::instance(Date::excelToDateTimeObject($row['tanggal_pembelian']));
            if ((int) $tanggal_carbon->format('m') != $this->pelaporan->bulan) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terdapat data pelaporan dengan bulan berbeda dengan bulan pelaporan pada file excel');
            }
            $jenis_bbm = $jenis_bbms->where('id', $row['jenis_bbm_id'])->first();
            Pembelian::create([
                'pelaporan_id' => $this->pelaporan->id,
                'penjual' => $row['penjual'],
                'alamat' => $row['alamat'],
                'jenis_bbm_id' => $row['jenis_bbm_id'],
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'sisa_volume' => $row['sisa_volume'],
                'volume' => $row['volume'],
                'nomor_kuitansi' => $row['nomor_kuitansi'],
                'tanggal' => $tanggal_carbon->format('Y-m-d'),
            ]);
        }
        DB::commit();
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
