<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Test extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'instructions',
        'status',
        'scale_config',
        'scoring_type',
        'scoring_config',
        'chart_type',
        'time_limit_minutes',
        'randomize_questions',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'description' => 'array',
            'instructions' => 'array',
            'scale_config' => 'array',
            'scoring_type' => 'string',
            'scoring_config' => 'array',
            'chart_type' => 'string',
            'time_limit_minutes' => 'integer',
            'randomize_questions' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Test $test) {
            if (empty($test->uuid)) {
                $test->uuid = Str::uuid()->toString();
            }
        });
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class)->withPivot('sort_order');
    }

    // --- Scopes ---

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeOwnedBy($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    // --- Helpers ---

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getMaxPossibleScore(): float
    {
        $scaleMax = $this->scale_config['max'] ?? 5;
        return $this->questions()->count() * $scaleMax;
    }
}
