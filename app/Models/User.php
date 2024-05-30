<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\UserDetail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Jobs\QueuedPasswordResetJob;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }

    public function sendPasswordResetNotification($token)
    {
        QueuedPasswordResetJob::dispatch($this, $token);
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function getPhotoProfileAttribute()
    {
        return 'https://ui-avatars.com/api/?name=' . $this->email . '&background=random&color=fff';
    }

    public function getRoleAttribute()
    {
        if($this->hasRole('administrator')) {
            return 'Admin';
        }else{
            return 'Operator';
        }
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('administrator');
    }

    public function getIsOperatorAttribute()
    {
        return $this->hasRole('operator');
    }
}
