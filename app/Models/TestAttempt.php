<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'test_id',
        'assessment_id',
        'assessment_link_id',
        'status',
        'started_at',
        'completed_at',
        'score_raw',
        'score_max',
        'score_percentage',
        'score_average',
        'score_details',
        'time_spent_seconds',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'score_raw' => 'decimal:2',
            'score_max' => 'decimal:2',
            'score_percentage' => 'decimal:2',
            'score_average' => 'decimal:2',
            'score_details' => 'array',
            'time_spent_seconds' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TestAttempt $attempt) {
            if (empty($attempt->uuid)) {
                $attempt->uuid = Str::uuid()->toString();
            }
        });
    }

    // --- Relationships ---

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assessmentLink(): BelongsTo
    {
        return $this->belongsTo(AssessmentLink::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    // --- Scopes ---

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // --- Helpers ---

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isTimedOut(): bool
    {
        $test = $this->test;
        if (!$test->time_limit_minutes) {
            return false;
        }

        return $this->started_at->addMinutes($test->time_limit_minutes)->isPast();
    }

    public function getRemainingSeconds(): ?int
    {
        $test = $this->test;
        if (!$test->time_limit_minutes) {
            return null;
        }

        $deadline = $this->started_at->addMinutes($test->time_limit_minutes);
        return max(0, (int) now()->diffInSeconds($deadline, false));
    }

    public function calculateScores(): void
    {
        $engine = app(\App\Services\ScoringEngine::class);
        $result = $engine->calculate($this);

        $this->score_raw = $result['summary']['score_raw'];
        $this->score_max = $result['summary']['score_max'];
        $this->score_percentage = $result['summary']['score_percentage'];
        $this->score_average = $result['summary']['score_average'];
        $this->score_details = $result['details'];
        $this->time_spent_seconds = $this->started_at->diffInSeconds($this->completed_at ?? now());
        $this->save();
    }
}
