<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OtpService
{

    public static $plain_token;
    public static $expired_at;
    public static $created_at;
    public static function generate($email, string $token_name, $ip_address, int $max_attempt = 5, int $length = 6, $validity = 10): self
    {
        // invalidate all token before
        self::invalidate($email, $token_name);
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= random_int(0, 9);
        }

        // $expired_at = now()->addMinutes($validity);
        $expired_at = now()->addDay();

        $otp = Otp::create([
            'email' => $email,
            'token_name' => $token_name,
            'max_attempt' => $max_attempt,
            'otp_token' => Hash::make($token),
            'ip_address' => $ip_address,
            'expired_at' => $expired_at,
        ]);

        self::$created_at = $otp->created_at;
        self::$plain_token = $token;
        self::$expired_at = $expired_at;
        return new self;
    }

    public function validate($email, string $token_name, string $token, $ip_address): bool
    {
        $otp = Otp::where('email', $email)
            ->where('token_name', $token_name)
            ->where('is_valid', true)
            ->where('expired_at', '>', now())
            // ->where('ip_address', $ip_address)
            ->latest()
            ->first();

        if ($otp && Hash::check($token, $otp->otp_token)) {
            $otp->update(['is_valid' => false]);
            return true;
        }

        if ($otp) {
            $otp->increment('attempt');
            if ($otp->attempt >= $otp->max_attempt) {
                $otp->update(['is_valid' => false]);
            }
        }

        return false;
    }

    private static function invalidate($email, string $token_name): void
    {
        Otp::where('email', $email)
            ->where('token_name', $token_name)
            ->where('is_valid', true)
            ->update(['is_valid' => false]);
    }
}
