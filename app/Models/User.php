<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'name',
        'email',
        'password',
        'is_admin',
        'tier',
        'reminder_enabled',
        'reminder_time',
        'reminder_days',
        'native_language',
        'target_language',
        'target_level',
        'default_level',
    ];

    /**
     * Bootstrap model events.
     */
    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if (empty($user->attributes['name'])) {
                $full = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
                $user->attributes['name'] = ! empty($full) ? $full : ($user->username ?? 'Người học');
            }
        });
    }

    /**
     * Compute full display name fallback to username.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $full = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));

                return ! empty($full) ? $full : ($this->username ?? $value ?? 'Người học');
            },
            set: fn ($value) => $value
        );
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'is_admin' => 'boolean',
            'reminder_enabled' => 'boolean',
            'reminder_days' => 'array',
        ];
    }

    /**
     * Practice sessions initiated by this user.
     */
    public function practiceSessions(): HasMany
    {
        return $this->hasMany(PracticeSession::class);
    }

    /**
     * Favorite records pinned by this user.
     */
    public function userFavorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    /**
     * Scenarios pinned as favorite by this user.
     */
    public function favoriteScenarios(): BelongsToMany
    {
        return $this->belongsToMany(Scenario::class, 'user_favorites')->withTimestamps();
    }

    /**
     * API cost logs attributed to this user.
     */
    public function apiCostLogs(): HasMany
    {
        return $this->hasMany(ApiCostLog::class);
    }

    /**
     * Custom scenarios created by this user.
     */
    public function scenarios(): HasMany
    {
        return $this->hasMany(Scenario::class, 'user_id');
    }

    /**
     * Alias for custom scenarios.
     */
    public function customScenarios(): HasMany
    {
        return $this->hasMany(Scenario::class, 'user_id');
    }

    /**
     * Check if user has unlimited tier or is admin.
     */
    public function isUnlimitedTier(): bool
    {
        return (bool) $this->is_admin || ($this->tier === 'unlimited');
    }

    /**
     * All sticky notes created by this user.
     */
    public function stickyNotes(): HasMany
    {
        return $this->hasMany(StickyNote::class);
    }

    /**
     * Sticky notes currently due for review.
     */
    public function dueStickyNotes(): HasMany
    {
        return $this->hasMany(StickyNote::class)
            ->where('is_mastered', false)
            ->where('next_review_at', '<=', now());
    }

    /**
     * Number of sticky notes due today for this user.
     */
    public function getDueNotesCount(): int
    {
        return $this->dueStickyNotes()->count();
    }
}
