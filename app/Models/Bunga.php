<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bunga extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelaporan_id',
        'waktu_bunga',
        'bunga_ke',
        'persentase_bunga',
        'bunga',
        'keterangan',
    ];

    protected $casts = [
        'waktu_bunga' => 'datetime',
        'bunga' => 'decimal:2',
        'persentase_bunga' => 'decimal:2',
    ];

    public function pelaporan()
    {
        return $this->belongsTo(Pelaporan::class);
    }

    public function getFormattedWaktuBungaAttribute()
    {
        return $this->waktu_bunga->format('d-m-Y H:i:s');
    }

    public function getFormattedBungaAttribute()
    {
        return 'Rp ' . number_format($this->bunga, 2, ',', '.');
    }

    public function getFormattedBungaKeAttribute()
    {
        return 'Bunga ke-' . $this->bunga_ke;
    }

    public function getFormattedKeteranganAttribute()
    {
        return $this->keterangan ?? '-';
    }

    public function getFormattedPersentaseDendaAttribute()
    {
        return $this->persentase_denda . ' %';
    }
}
