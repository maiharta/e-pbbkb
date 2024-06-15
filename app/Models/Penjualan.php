<?php

namespace App\Models;

use App\Models\Sektor;
use App\Models\JenisBbm;
use App\Models\Pelaporan;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelaporan_id',
        'jenis_bbm_id',
        'sektor_id',
        'kode_jenis_bbm',
        'nama_jenis_bbm',
        'is_subsidi',
        'persentase_tarif_jenis_bbm',
        'kode_sektor',
        'nama_sektor',
        'persentase_pengenaan_sektor',
        'pembeli',
        'volume',
        'dpp',
        'alamat',
        'tanggal',
        'nomor_kuitansi',
        'pbbkb',
        'lokasi_penyaluran',
        'is_wajib_pajak',
        'pbbkb_sistem',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }

    public function pelaporan()
    {
        return $this->belongsTo(Pelaporan::class);
    }

    public function jenisBbm()
    {
        return $this->belongsTo(JenisBbm::class);
    }

    public function sektor()
    {
        return $this->belongsTo(Sektor::class);
    }
    public function pelaporanNote()
    {
        return $this->belongsTo(PelaporanNote::class);
    }

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->locale('id')->isoFormat('D MMMM Y');
    }
}
