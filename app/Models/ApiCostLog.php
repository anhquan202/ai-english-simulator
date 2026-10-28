<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiCostLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_cost_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'session_id',
        'user_id',
        'guest_token',
        'scenario_id',
        'endpoint',
        'model_name',
        'prompt_tokens',
        'candidate_tokens',
        'total_tokens',
        'cost_usd',
        'latency_ms',
        'status_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prompt_tokens' => 'integer',
            'candidate_tokens' => 'integer',
            'total_tokens' => 'integer',
            'cost_usd' => 'float',
            'latency_ms' => 'integer',
            'status_code' => 'integer',
        ];
    }

    /**
     * Practice session associated with this API call.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(PracticeSession::class, 'session_id');
    }

    /**
     * User who initiated this API call.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scenario context for this API call.
     */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    /**
     * Cost converted to Vietnamese Dong (VND).
     */
    protected function costVnd(): Attribute
    {
        return Attribute::make(
            get: fn () => round(($this->cost_usd ?? 0.0) * 25400, 2)
        );
    }
}
