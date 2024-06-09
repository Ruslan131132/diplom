<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public function todayLogs(): HasMany
    {
        return $this->hasMany(Log::class)->whereDate('created_at', Carbon::now());
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    protected $appends = [
        'last_activation'
    ];

    public function getLastActivationAttribute() {
        return QrUser::where('active', 0)->where('user_id', $this->id)->orderBy('updated_at', 'DESC')->first()?->updated_at->format('Y-m-d H:i:s') ?? null;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active'
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
