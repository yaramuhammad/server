<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AssessmentLink extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'assessment_id',
        'created_by',
        'token',
        'title',
        'starts_at',
        'expires_at',
        'max_participants',
        'is_active',
        'password',
        'collect_name',
        'collect_email',
        'collect_phone',
        'collect_company',
        'collect_job_title',
        'collect_age',
        'collect_gender',
        'custom_fields',
        'welcome_message',
        'completion_message',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'max_participants' => 'integer',
            'is_active' => 'boolean',
            'password' => 'hashed',
            'collect_name' => 'boolean',
            'collect_email' => 'boolean',
            'collect_phone' => 'boolean',
            'collect_company' => 'boolean',
            'collect_job_title' => 'boolean',
            'collect_age' => 'boolean',
            'collect_gender' => 'boolean',
            'custom_fields' => 'array',
            'welcome_message' => 'array',
            'completion_message' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AssessmentLink $link) {
            if (empty($link->uuid)) {
                $link->uuid = Str::uuid()->toString();
            }
            if (empty($link->token)) {
                $link->token = Str::random(64);
            }
        });
    }

    // --- Relationships ---

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function attempts(): HasManyThrough
    {
        return $this->hasManyThrough(TestAttempt::class, Participant::class);
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    // --- Helpers ---

    public function isAccessible(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_participants && $this->participants()->count() >= $this->max_participants) {
            return false;
        }

        return true;
    }

    public function getUrl(): string
    {
        return config('app.frontend_url') . '/assess/' . $this->token;
    }

    public function getRequiredFields(): array
    {
        $fields = [];
        if ($this->collect_name) $fields[] = 'name';
        if ($this->collect_email) $fields[] = 'email';
        if ($this->collect_phone) $fields[] = 'phone';
        if ($this->collect_company) $fields[] = 'company';
        if ($this->collect_job_title) $fields[] = 'job_title';
        if ($this->collect_age) $fields[] = 'age';
        if ($this->collect_gender) $fields[] = 'gender';

        return $fields;
    }
}
