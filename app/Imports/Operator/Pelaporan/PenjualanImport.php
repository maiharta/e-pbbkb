<?php

namespace App\Imports\Operator\Pelaporan;

use App\Exceptions\ServiceException;
use Carbon\Carbon;
use App\Models\Sektor;
use App\Models\JenisBbm;
use App\Models\Kabupaten;
use App\Models\Pelaporan;
use App\Models\Penjualan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Contracts\Validation\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Symfony\Component\Uid\Ulid;

class PenjualanImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    WithMultipleSheets,
    WithChunkReading,
    WithBatchInserts
{

    public $pelaporan;
    private static $validKabupatenIds;
    private static $validSektorIds;
    private static $validJenisBbmIds;

    public function __construct(Pelaporan $pelaporan)
    {
        $this->pelaporan = $pelaporan;

        // Cache valid IDs once per import to avoid repeated queries during validation
        if (is_null(self::$validKabupatenIds)) {
            self::$validKabupatenIds = Kabupaten::pluck('id')->toArray();
        }
        if (is_null(self::$validSektorIds)) {
            self::$validSektorIds = Sektor::pluck('id')->toArray();
        }
        if (is_null(self::$validJenisBbmIds)) {
            self::$validJenisBbmIds = JenisBbm::pluck('id')->toArray();
        }
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 500; // Insert 500 rows at a time
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // Pre-load reference data once and index by ID for O(1) lookup
        $sektors = Sektor::get()->keyBy('id');
        $jenis_bbms = JenisBbm::get()->keyBy('id');

        // Get existing penjualan records for this pelaporan to handle updates
        $existingPenjualans = Penjualan::where('pelaporan_id', $this->pelaporan->id)
            ->get()
            ->keyBy('nomor_kuitansi');

        $dataToInsert = [];
        $dataToUpdate = [];
        $now = now();

        foreach ($collection as $row) {
            $tanggal_carbon = is_string($row['tanggal_penjualan'])
                ? Carbon::parse($row['tanggal_penjualan'])
                : Carbon::instance(Date::excelToDateTimeObject($row['tanggal_penjualan']));

            // Validate date belongs to the correct reporting period
            if (((int) $tanggal_carbon->format('m') != $this->pelaporan->bulan) ||
                ((int) $tanggal_carbon->format('Y') != $this->pelaporan->tahun)) {
                throw new ServiceException('Terdapat data pelaporan dengan bulan berbeda dengan bulan pelaporan pada file excel, nomor kuitansi: ' . $row['nomor_kuitansi'] . ', tanggal penjualan: ' . $tanggal_carbon->format('Y-m-d'));
            }

            $jenis_bbm = $jenis_bbms->get($row['jenis_bbm_id']);
            $sektor = $sektors->get($row['sektor_id']);

            if (!$jenis_bbm || !$sektor) {
                continue; // Skip invalid references
            }

            $data = [
                'ulid' => (string) Ulid::generate(),
                'pelaporan_id' => $this->pelaporan->id,
                'nomor_kuitansi' => $row['nomor_kuitansi'],
                'pembeli' => $row['pembeli'],
                'alamat' => $row['alamat'],
                'kabupaten_id' => $row['kabupaten_id'],
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
                'tanggal' => $tanggal_carbon->format('Y-m-d'),
                'updated_at' => $now,
            ];

            if (isset($existingPenjualans[$row['nomor_kuitansi']])) {
                // Prepare for update
                $data['id'] = $existingPenjualans[$row['nomor_kuitansi']]->id;
                $dataToUpdate[] = $data;
            } else {
                // Prepare for insert
                $data['created_at'] = $now;
                $dataToInsert[] = $data;
            }
        }

        // Perform batch operations within a transaction
        DB::transaction(function () use ($dataToInsert, $dataToUpdate) {
            // Batch insert new records in chunks
            if (!empty($dataToInsert)) {
                $chunks = array_chunk($dataToInsert, 500);
                foreach ($chunks as $chunk) {
                    Penjualan::insert($chunk);
                }
            }

            // Batch update existing records using case-when statements
            if (!empty($dataToUpdate)) {
                $this->batchUpdate($dataToUpdate);
            }
        });
    }

    /**
     * Perform batch update using raw SQL with CASE WHEN
     */
    private function batchUpdate(array $dataToUpdate)
    {
        if (empty($dataToUpdate)) {
            return;
        }

        $chunks = array_chunk($dataToUpdate, 500);

        foreach ($chunks as $chunk) {
            $ids = collect($chunk)->pluck('id')->toArray();

            // Build CASE statements for each column
            $cases = [];
            $columns = [
                'pembeli', 'alamat', 'kabupaten_id', 'sektor_id',
                'kode_sektor', 'nama_sektor', 'persentase_pengenaan_sektor',
                'jenis_bbm_id', 'kode_jenis_bbm', 'nama_jenis_bbm',
                'is_subsidi', 'persentase_tarif_jenis_bbm', 'lokasi_penyaluran',
                'is_wajib_pajak', 'volume', 'dpp', 'pbbkb', 'tanggal', 'updated_at'
            ];

            foreach ($columns as $column) {
                $case = "CASE id ";
                foreach ($chunk as $row) {
                    $value = $row[$column];
                    if (is_null($value)) {
                        $case .= "WHEN {$row['id']} THEN NULL ";
                    } elseif (is_numeric($value)) {
                        $case .= "WHEN {$row['id']} THEN {$value} ";
                    } else {
                        $value = addslashes($value);
                        $case .= "WHEN {$row['id']} THEN '{$value}' ";
                    }
                }
                $case .= "ELSE `{$column}` END";
                $cases[] = "`{$column}` = {$case}";
            }

            $idsString = implode(',', $ids);
            $updateStatement = implode(', ', $cases);

            DB::statement("UPDATE penjualans SET {$updateStatement} WHERE id IN ({$idsString})");
        }
    }

    public function rules(): array
    {
        $kabupatenIds = implode(',', self::$validKabupatenIds);
        $sektorIds = implode(',', self::$validSektorIds);
        $jenisBbmIds = implode(',', self::$validJenisBbmIds);

        return [
            'pembeli' => 'required',
            'alamat' => 'required',
            'kabupaten_id' => "required|in:{$kabupatenIds}",
            'lokasi_penyaluran_id' => 'required|in:1,2',
            'status_pajak_id' => 'required|in:1,2',
            'sektor_id' => "required|in:{$sektorIds}",
            'jenis_bbm_id' => "required|in:{$jenisBbmIds}",
            'volume' => 'required|numeric',
            'dpp' => 'required|numeric',
            'pbbkb' => 'required|numeric',
            'nomor_kuitansi' => 'required|string',
            'tanggal_penjualan' => 'required',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kabupaten_id.in' => 'Kabupaten ID tidak valid',
            'sektor_id.in' => 'Sektor ID tidak valid',
            'jenis_bbm_id.in' => 'Jenis BBM ID tidak valid',
        ];
    }
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = $this;

        return $sheets;
    }
}
