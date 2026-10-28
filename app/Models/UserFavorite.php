<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'scenario_id',
    ];

    /**
     * User who favorited this scenario (null for guest).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Favorited scenario.
     */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }
}
