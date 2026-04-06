<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RetakeGrant extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'assessment_id',
        'granted_by',
        'granted_at',
        'used_at',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'granted_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RetakeGrant $grant) {
            if (empty($grant->uuid)) {
                $grant->uuid = Str::uuid()->toString();
            }
        });
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }
}
