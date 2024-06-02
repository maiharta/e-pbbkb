<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sektor extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'persentase_pengenaan',
    ];

    protected $casts = [
        'persentase_pengenaan' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }
}
