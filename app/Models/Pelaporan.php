<?php

namespace App\Models;

use App\Models\User;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'is_sent_to_admin',
        'catatan_revisi',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_sent_to_admin' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function getBulanNameAttribute()
    {
        return Carbon::create()->month($this->bulan)->locale('id')->format('F');
    }

    public function getPembelianBadgeAttribute()
    {
        if (!$this->is_sent_to_admin) {
            $link = route('pelaporan.pembelian.index', ['ulid' => $this->ulid]);
            return "<a href='{$link}' class='fw-bold'><span class='fw-bold isax isax-document-upload text-primary'></span></a>";
        } else {
            return "<span class='fw-bold isax isax-document-upload text-success'></span>";
        }
    }
    public function getPenjualanBadgeAttribute()
    {
        if (!$this->is_sent_to_admin) {
            $link = route('pelaporan.penjualan.index', ['ulid' => $this->ulid]);
            return "<a href='{$link}' class='fw-bold'><span class='fw-bold isax isax-document-download text-primary'></span></a>";
        } else {
            return "<span class='fw-bold isax isax-document-download text-success'></span>";
        }
    }

    public function getSptpdBadgeAttribute()
    {
        if (!$this->is_verified) {
            return "<span class='fw-bold isax isax-minus text-disabled'></span>";
        } else {
            return "<span class='fw-bold isax isax-chart text-primary'></span>";
        }
    }

    public function getSspdBadgeAttribute()
    {
        if (!$this->is_verified) {
            return "<span class='fw-bold isax isax-minus text-primary'></span>";
        } else {
            return "<span class='fw-bold isax isax-chart text-primary'></span>";
        }
    }

    public function getSendBadgeAttribute()
    {
        if (!$this->is_sent_to_admin) {
            return "<button class='btn' type='button' onclick='sendPelaporan(\"{$this->ulid}\")'><span class='fw-bold isax isax-direct-right text-primary'></span></button>";
        } else {
            return "<span class='fw-bold isax isax-direct-right text-success'></span>";
        }
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->is_sent_to_admin && !$this->catatan_revisi) {
            return "<span class='badge bg-warning'>Draft</span>";
        } else if ($this->catatan_revisi && !$this->is_sent_to_admin) {
            return "<span class='badge bg-danger' title='".$this->catatan_revisi."'>Revisi</span>";
        } else if ($this->is_verified) {
            return "<span class='badge bg-info'>Terverifikasi - Pending SSPD</span>";
        } else {
            return "<span class='badge bg-secondary'>Verifikasi</span>";
        }
    }
}
