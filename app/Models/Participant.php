<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_link_id',
        'participant_account_id',
        'name',
        'email',
        'phone',
        'department',
        'age',
        'gender',
        'custom_data',
        'ip_address',
        'user_agent',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'custom_data' => 'array',
            'age' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Participant $participant) {
            if (empty($participant->uuid)) {
                $participant->uuid = Str::uuid()->toString();
            }
        });
    }

    public function assessmentLink(): BelongsTo
    {
        return $this->belongsTo(AssessmentLink::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ParticipantAccount::class, 'participant_account_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function retakeGrants(): HasMany
    {
        return $this->hasMany(RetakeGrant::class);
    }

    /**
     * Get the latest retake grant for a specific assessment.
     */
    public function latestRetakeGrant(int $assessmentId): ?RetakeGrant
    {
        return $this->retakeGrants()
            ->where('assessment_id', $assessmentId)
            ->latest('granted_at')
            ->first();
    }

    /**
     * Get attempts that are "current" — created after the latest retake grant (if any).
     */
    public function currentAttempts(int $assessmentId)
    {
        $latestGrant = $this->latestRetakeGrant($assessmentId);

        $query = $this->attempts()->where('assessment_id', $assessmentId);

        if ($latestGrant) {
            $query->where('created_at', '>=', $latestGrant->granted_at);
        }

        return $query;
    }
}
