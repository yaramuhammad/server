<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_attempt_id',
        'question_id',
        'value',
        'scored_value',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'scored_value' => 'integer',
            'answered_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Response $response) {
            if (!isset($response->scored_value)) {
                $question = $response->question;
                $response->scored_value = $question->calculateScoredValue($response->value);
            }
            if (!isset($response->answered_at)) {
                $response->answered_at = now();
            }
        });
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
