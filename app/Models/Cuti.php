<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'deskripsi'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }

    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->locale('id')->isoFormat('D MMMM Y');
    }
}
