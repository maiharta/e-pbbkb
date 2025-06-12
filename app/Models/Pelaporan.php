<?php

namespace App\Models;

use App\Models\User;
use App\Models\Bunga;
use App\Models\Denda;
use App\Models\Sptpd;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Support\Str;
use App\Models\PelaporanNote;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelaporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'is_sent_to_admin',
        'catatan_revisi',
        'is_verified',
        'verified_at',
        'is_sptpd_approved',
        'is_sptpd_canceled',
        'sptpd_approved_at',
        'batas_pelaporan',
        'batas_pembayaran',
        'first_send_at',
        'is_expired',
        'is_paid',
    ];

    protected $casts = [
        'is_sent_to_admin' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_sptpd_approved' => 'boolean',
        'is_sptpd_canceled' => 'boolean',
        'sptpd_approved_at' => 'datetime',
        'batas_pelaporan' => 'datetime',
        'batas_pembayaran' => 'datetime',
        'first_send_at' => 'datetime',
        'is_expired' => 'boolean',
        'is_paid' => 'boolean',
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

    public function sptpd()
    {
        return $this->hasOne(Sptpd::class);
    }
    public function pelaporanNote()
    {
        return $this->hasMany(PelaporanNote::class);
    }

    public function denda()
    {
        return $this->hasMany(Denda::class);
    }

    public function bunga()
    {
        return $this->hasMany(Bunga::class);
    }

    public function transactions()
    {
        return $this->hasMany(Invoice::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getBulanNameAttribute()
    {
        return Carbon::create()->month($this->bulan)->locale('id')->isoFormat('MMMM');
    }

    public function getPembelianBadgeAttribute()
    {
        // If already sent to admin, show green checkmark
        // if ($this->is_sent_to_admin) {
        //     return "<span class='fw-bold isax isax-document-upload text-success'></span>";
        // }

        // Check if pembelian data exists
        $hasPembelianData = $this->pembelian()->count() > 0;

        if (!$hasPembelianData) {
            // No data yet - show yellow warning
            $link = route('pelaporan.pembelian.index', ['ulid' => $this->ulid]);
            return "<a href='{$link}' class='fw-bold'><span class='fw-bold isax isax-document-upload text-warning'></span></a>";
        } else {
            // Has data but not sent - show blue icon
            $link = route('pelaporan.pembelian.index', ['ulid' => $this->ulid]);
            return "<a href='{$link}' class='fw-bold'><span class='fw-bold isax isax-document-upload text-primary'></span></a>";
        }
    }
    public function getPenjualanBadgeAttribute()
    {
        // If already sent to admin, show green checkmark
        // if ($this->is_sent_to_admin) {
        //     return "<span class='fw-bold isax isax-document-download text-success'></span>";
        // }

        // Check if penjualan data exists
        $hasPenjualanData = $this->penjualan()->count() > 0;

        if (!$hasPenjualanData) {
            // No data yet - show yellow warning
            $link = route('pelaporan.penjualan.index', ['ulid' => $this->ulid]);
            return "<a href='{$link}' class='fw-bold'><span class='fw-bold isax isax-document-download text-warning'></span></a>";
        } else {
            // Has data but not sent - show blue icon
            $link = route('pelaporan.penjualan.index', ['ulid' => $this->ulid]);
            return "<a href='{$link}' class='fw-bold'><span class='fw-bold isax isax-document-download text-primary'></span></a>";
        }
    }

    public function getSptpdBadgeAttribute()
    {
        if (!$this->is_verified) {
            return "<span class='fw-bold isax isax-minus text-disabled'></span>";
        } else {
            $link = route('pelaporan.sptpd.index', ['ulid' => $this->ulid]);
            return "<a href='{$link}' class='fw-bold'><span class='fw-bold isax isax-chart text-primary'></span></a>";
        }
    }

    public function getSspdBadgeAttribute()
    {
        if (!$this->is_sptpd_approved) {
            return "<span class='fw-bold isax isax-minus text-primary'></span>";
        } else {
            $link = route('pelaporan.sspd.index', ['ulid' => $this->ulid]);
            return "<a href='{$link}'><span class='fw-bold isax isax-chart text-primary'></span></a>";
        }
    }

    public function getSendBadgeAttribute()
    {
         // Check if penjualan data exists
         $hasPenjualanData = $this->penjualan()->count() > 0;

        if (!$this->is_sent_to_admin) {
            if($hasPenjualanData){
                return "<button class='btn' type='button' onclick='sendPelaporan(\"{$this->ulid}\")'><span class='fw-bold isax isax-direct-right text-primary'></span></button>";
            }else{
                return "<span class='fw-bold isax isax-direct-right text-success'></span>";
            }
        } else {
            return "<span class='fw-bold isax isax-direct-right text-success'></span>";
        }
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->is_sent_to_admin && !$this->catatan_revisi) {
            return "<span class='badge bg-warning'>Draft</span>";
        } else if ($this->catatan_revisi && !$this->is_sent_to_admin) {
            return "<span class='badge bg-danger' title='" . $this->catatan_revisi . "'>Revisi</span>";
        } else if ($this->is_verified && !$this->is_sptpd_approved) {
            return "<span class='badge bg-info'>Terverifikasi - Pending SPTPD</span>";
        } else if ($this->is_paid) {
            return "<span class='badge bg-success'>Lunas</span>";
        } else if ($this->is_sptpd_approved) {
            return "<span class='badge bg-info'>Pending Pembayaran SSPD</span>";
        } else {
            return "<span class='badge bg-secondary'>Verifikasi Admin</span>";
        }
    }

    public function getBatasPelaporanFormattedAttribute()
    {
        return $this->batas_pelaporan ? Carbon::parse($this->batas_pelaporan)->locale('id')->isoFormat('D MMMM Y') : '-';
    }
    public function getBatasPembayaranFormattedAttribute()
    {
        return $this->batas_pembayaran ? Carbon::parse($this->batas_pembayaran)->locale('id')->isoFormat('D MMMM Y') : '-';
    }

    public function getMonthAttribute($value)
    {
        return (int) $this->bulan;
    }
    public function getYearAttribute($value)
    {
        return (int) $this->tahun;
    }
}
