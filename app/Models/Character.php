<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'scenario_id',
        'name',
        'role',
        'role_title',
        'avatar_path',
        'avatar_url',
        'voice_id',
        'voice_name',
        'personality',
        'tone_of_voice',
        'speech_rate',
        'first_message',
        'first_message_translation',
        'system_prompt',
        'system_persona_prompt',
        'is_default',
    ];

    protected $appends = [
        'role_vi',
    ];

    /**
     * Vietnamese role title attribute accessor.
     */
    public function getRoleViAttribute(): string
    {
        $map = [
            'Customer Service Representative' => 'Chuyên viên Chăm sóc Khách hàng',
            'Store Supervisor' => 'Giám sát Cửa hàng',
            'Senior Cashier' => 'Thu ngân Trưởng',
            'Junior Cashier' => 'Thu ngân',
            'Barista Lead' => 'Trưởng nhóm Pha chế',
            'Friendly Barista' => 'Barista Thân thiện',
            'Busy Morning Barista' => 'Barista Buổi sáng',
            'Coffee Specialist' => 'Chuyên gia Cà phê',
            'Coffee Enthusiast & Regular' => 'Khách hàng thân thiết',
            'Front Desk Manager' => 'Quản lý Lễ tân',
            'Front Desk Receptionist' => 'Lễ tân Khách sạn',
            'VIP Guest Concierge' => 'Chuyên viên Chăm sóc VIP',
            'Night Shift Clerk' => 'Nhân viên Ca đêm',
            'Concierge Specialist' => 'Chuyên viên Hỗ trợ Khách hàng',
            'Station Agent' => 'Nhân viên Nhà ga',
            'Transit Assistant' => 'Nhân viên Điều phối Ga',
            'Tourist Help Desk' => 'Bàn Hướng dẫn Du khách',
            'Ticketing Officer' => 'Nhân viên Bán vé',
            'Tech Lead & Scrum Master' => 'Trưởng nhóm Kỹ thuật',
            'Senior Project Lead' => 'Trưởng nhóm Dự án',
            'Product Manager' => 'Quản lý Sản phẩm',
            'Engineering Lead' => 'Trưởng nhóm Kỹ thuật',
            'Senior Fashion Stylist' => 'Chuyên viên Tư vấn Thời trang',
            'Fashion Stylist' => 'Chuyên viên Tư vấn Thời trang',
            'Store Associate' => 'Nhân viên Bán hàng',
        ];

        $currentRole = $this->role ?? $this->role_title ?? '';

        return $map[$currentRole] ?? $currentRole;
    }

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'speech_rate' => 'float',
        ];
    }

    /**
     * Parent scenario for this character.
     */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    /**
     * Practice sessions featuring this character.
     */
    public function practiceSessions(): HasMany
    {
        return $this->hasMany(PracticeSession::class);
    }

    /**
     * Role title attribute accessor/mutator fallback.
     */
    protected function roleTitle(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['role'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * Avatar URL attribute accessor fallback.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['avatar_path'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * Voice name attribute accessor fallback.
     */
    protected function voiceName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['voice_id'] ?? null,
            set: fn ($value) => $value
        );
    }

    /**
     * System persona prompt accessor fallback.
     */
    protected function systemPersonaPrompt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->attributes['system_prompt'] ?? null,
            set: fn ($value) => $value
        );
    }
}
