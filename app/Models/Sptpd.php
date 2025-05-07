<?php

namespace App\Models;

use App\Models\Pelaporan;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sptpd extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelaporan_id',
        'nomor',
        'tanggal',
        'total_pbbkb',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_pbbkb' => 'decimal:2',
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
}
