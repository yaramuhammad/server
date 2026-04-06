<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'test_id',
        'text',
        'image_path',
        'sort_order',
        'is_reverse_scored',
        'scale_override',
        'is_required',
        'category_key',
        'weight',
        'correct_answer',
    ];

    protected function casts(): array
    {
        return [
            'text' => 'array',
            'scale_override' => 'array',
            'is_reverse_scored' => 'boolean',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
            'weight' => 'decimal:2',
        ];
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function getEffectiveScaleConfig(): array
    {
        return $this->scale_override ?? $this->test->scale_config;
    }

    public function calculateScoredValue(int $rawValue): int
    {
        // If correct_answer is set, score as 1 (correct) or 0 (incorrect)
        if ($this->correct_answer !== null) {
            return $rawValue === $this->correct_answer ? 1 : 0;
        }

        $scale = $this->getEffectiveScaleConfig();

        // If a score_map exists, use it for direct mapping (MCQ/SJT scoring)
        if (!empty($scale['score_map']) && isset($scale['score_map'][(string) $rawValue])) {
            return (int) $scale['score_map'][(string) $rawValue];
        }

        // Otherwise, fall back to reverse scoring
        if (!$this->is_reverse_scored) {
            return $rawValue;
        }

        return ($scale['max'] + $scale['min']) - $rawValue;
    }
}
