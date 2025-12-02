<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisBbm extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'is_subsidi',
        'persentase_tarif',
    ];

    protected $casts = [
        'is_subsidi' => 'boolean',
        'persentase_tarif' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }
}
