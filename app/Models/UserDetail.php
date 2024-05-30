<?php

namespace App\Models;

use App\Models\User;
use App\Models\Kabupaten;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kabupaten_id',
        'npwpd',
        'nomor_telepon',
        'alamat',
        'filepath_berkas_persyaratan',
        'is_user_readonly',
        'catatan_revisi',
        'is_verified',
        'verified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }
}
