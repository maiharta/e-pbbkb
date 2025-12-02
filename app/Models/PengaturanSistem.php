<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengaturanSistem extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'deskripsi'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }
}
