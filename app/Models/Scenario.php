<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Scenario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'category',
        'cover_image',
        'estimated_minutes',
        'supported_levels',
        'location_name',
        'context_note',
        'detailed_description',
        'twist_prompt',
        'is_custom',
        'is_public',
        'is_active',
    ];

    protected $appends = [
        'twist_prompt_vi',
    ];

    /**
     * Vietnamese translation for twist prompt.
     */
    public function getTwistPromptViAttribute(): string
    {
        $translations = [
            'supermarket-refund' => 'Sarah kiểm tra hệ thống và giải thích rằng theo chính sách siêu thị khi thanh toán qua thẻ, việc hoàn tiền vào thẻ mất từ 3 đến 5 ngày làm việc, HOẶC cô ấy có thể phát ngay phiếu mua hàng tích lũy tại quầy kèm ưu đãi thêm 5% hôm nay. Cô ấy lịch sự hỏi bạn muốn chọn phương án nào.',
            'coffee-shop-order' => 'Máy xay espresso đang tạm thời khởi động lại trong 5 phút. Emma lịch sự hỏi bạn có phiền đợi 5 phút để lấy ly espresso không, hoặc bạn có muốn đổi sang một ly cold brew thơm ngon đã sẵn sàng ngay bây giờ.',
            'hotel-checkin' => 'Phòng hướng biển superior bạn đặt đang bảo trì điều hòa đột xuất trong 2 tiếng. Jack lịch sự đề xuất nâng hạng miễn phí ngay cho bạn lên phòng Deluxe Garden Suite ở tầng thương gia kèm bữa sáng tại phòng chờ, hoặc bạn có thể đợi phòng hướng biển sẵn sàng.',
            'metro-station' => 'Tuyến số 1 đang tạm dừng do bảo trì ray khẩn cấp. David thông báo bạn cần đi Tuyến số 3 đến phố 14 rồi chuyển sang xe buýt trung chuyển, hoặc mua thẻ MetroCard thanh toán chạm để đổi sang lộ trình khác.',
            'daily-standup' => 'Michael thông báo rằng khách hàng vừa yêu cầu một điều chỉnh nhỏ trong luồng xác thực. Anh ấy hỏi liệu bạn có thể đưa vào xử lý trước thứ Sáu không, hay chúng ta nên dời sang sprint tiếp theo để bảo vệ thời hạn ra mắt.',
            'clothing-store' => 'Nhân viên tư vấn thông báo rằng áo size M màu đó hiện tạm hết trên kệ, nhưng cô ấy có thể vào kho kiểm tra HOẶC giới thiệu một chiếc áo khoác tương tự kèm ưu đãi giảm 15% dành cho thành viên.',
        ];

        return $translations[$this->slug] ?? ($this->twist_prompt ?? '');
    }

    protected function casts(): array
    {
        return [
            'supported_levels' => 'array',
            'is_custom' => 'boolean',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
            'estimated_minutes' => 'integer',
        ];
    }

    /**
     * Owner user for custom scenarios.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope custom scenarios.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_custom', true);
    }

    /**
     * Scope scenarios for a specific user.
     */
    public function scopeForUser($query, ?int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * All characters/personas associated with this scenario.
     */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    /**
     * Default character for this scenario.
     */
    public function defaultCharacter(): HasOne
    {
        return $this->hasOne(Character::class)->where('is_default', true);
    }

    /**
     * Scaffolding hints ordered by level and sequence.
     */
    public function scenarioHints(): HasMany
    {
        return $this->hasMany(ScenarioHint::class)->orderBy('sort_order');
    }

    /**
     * Alias for scenarioHints.
     */
    public function hints(): HasMany
    {
        return $this->scenarioHints();
    }

    /**
     * Practice sessions conducted under this scenario.
     */
    public function practiceSessions(): HasMany
    {
        return $this->hasMany(PracticeSession::class);
    }

    /**
     * User favorite records.
     */
    public function userFavorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    /**
     * Users who favorited this scenario.
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorites')->withTimestamps();
    }
}
