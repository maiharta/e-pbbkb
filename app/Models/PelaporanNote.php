<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaporanNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelaporan_id',
        'penjualan_id',
        'deskripsi',
        'status',
        'is_active',
        'step'
    ];

    public function pelaporan()
    {
        return $this->belongsTo(Pelaporan::class);
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
