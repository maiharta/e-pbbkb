<?php

namespace App\Models;

use App\Models\JenisBbm;
use App\Models\Kabupaten;
use App\Models\Pelaporan;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelaporan_id',
        'jenis_bbm_id',
        'kode_jenis_bbm',
        'nama_jenis_bbm',
        'persentase_tarif_jenis_bbm',
        'is_subsidi',
        'penjual',
        'volume',
        'sisa_volume',
        'nomor_kuitansi',
        'tanggal',
        'alamat'
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

    public function getTanggalFormattedAttribute(){
        return Carbon::parse($this->tanggal)->locale('id')->isoFormat('D MMMM Y');
    }
}
