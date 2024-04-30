<?php

namespace App\Models;

use App\Models\Pelaporan;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelaporan_id',
        'kabupaten_id',
        'jenis_bbm_id',
        'sektor_id',
        'kode_jenis_bbm',
        'nama_jenis_bbm',
        'is_subsidi',
        'persentase_tarif_jenis_bbm',
        'kode_sektor',
        'nama_sektor',
        'persentase_tarif_sektor',
        'pembeli',
        'volume',
        'dpp',
    ];

    protected $casts = [
        // 'volume' => 'decimal',
        // 'dpp' => 'decimal',
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

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }
}
