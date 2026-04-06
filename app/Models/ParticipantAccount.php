<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class ParticipantAccount extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'department',
        'age',
        'gender',
        'preferred_locale',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'age' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ParticipantAccount $account) {
            if (empty($account->uuid)) {
                $account->uuid = Str::uuid()->toString();
            }
        });
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }
}
