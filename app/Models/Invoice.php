<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelaporan_id',
        'invoice_number',
        'customer_npwpd',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'month',
        'year',
        'description',
        'amount',
        'items',
        'payment_status',

        'sipay_record_id',
        'sipay_virtual_account',
        'sipay_transaction_date',
        'sipay_expired_date',
        'sipay_nomor_tagihan',
        'sipay_status_invoice',
        'sipay_status_bpd',
        'sipay_payment_date_paid',
        'sipay_payment_date_kasda',
        'sipay_invoice',
        'sipay_response',
        'expires_at'
    ];

    protected $casts = [
        'items' => 'collection',
        'transaction_date' => 'datetime',
        'sipay_expired_date' => 'datetime',
        'expires_at' => 'datetime',
        'sipay_response' => 'collection',
    ];

    // generate invoice number, receipt number
    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $invoice->ulid = \Illuminate\Support\Str::ulid();
            $invoice->invoice_number = 'INV-' . strtoupper(uniqid());
        });
    }
    public function pelaporan()
    {
        return $this->belongsTo(Pelaporan::class);
    }
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->payment_status) {
            'paid' => '<span class="badge bg-success">Lunas</span>',
            'pending' => '<span class="badge bg-warning">Menunggu Pembayaran</span>',
            'expired' => '<span class="badge bg-danger">Kadaluarsa</span>',
            default => '<span class="badge bg-secondary">Tidak Diketahui</span>',
        };
    }
}
