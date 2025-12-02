<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "email",
        "token_name",
        "otp_token",
        "max_attempt",
        "attempt",
        "is_valid",
        "ip_address",
        "expired_at",
    ];

    protected $casts = [
        "is_valid" => "boolean",
        "expired_at" => "datetime",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
