<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelaporan_id',
        'waktu_denda',
        'denda_ke',
        'denda',
        'keterangan',
    ];
    protected $casts = [
        'waktu_denda' => 'datetime',
        'denda' => 'decimal:2',
    ];

    public function pelaporan()
    {
        return $this->belongsTo(Pelaporan::class);
    }
    public function getFormattedWaktuDendaAttribute()
    {
        return $this->waktu_denda->format('d-m-Y H:i:s');
    }

    public function getFormattedDendaAttribute()
    {
        return 'Rp ' . number_format($this->denda, 2, ',', '.');
    }
    public function getFormattedDendaKeAttribute()
    {
        return 'Denda ke-' . $this->denda_ke;
    }
    public function getFormattedKeteranganAttribute()
    {
        return $this->keterangan ?? '-';
    }
}
