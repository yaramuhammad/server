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

class Assessment extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'instructions',
        'status',
        'show_results_to_participant',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'description' => 'array',
            'instructions' => 'array',
            'show_results_to_participant' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Assessment $assessment) {
            if (empty($assessment->uuid)) {
                $assessment->uuid = Str::uuid()->toString();
            }
        });
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function links(): HasMany
    {
        return $this->hasMany(AssessmentLink::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
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
}
