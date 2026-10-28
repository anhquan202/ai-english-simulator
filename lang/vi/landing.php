<?php

declare(strict_types=1);

return [
    'page_title' => 'mở lời. - AI Real-World English Simulator',

    // Floating Chapter HUD Tracker
    'hud' => [
        'chapter_1_title' => '01 Nỗi sợ đời thực',
        'chapter_1_label' => '01. Khoảnh khắc đông cứng',
        'chapter_2_title' => '02 Trải nghiệm mô phỏng',
        'chapter_2_label' => '02. Màn hình giả lập Living Simulator',
        'chapter_3_title' => '03 Triết lý CSI',
        'chapter_3_label' => '03. Thước đo thấu cảm CSI',
        'chapter_4_title' => '04 Mở lời thử ngay',
        'chapter_4_label' => '04. Mở lời thử ngay (Micro-Voice)',
    ],

    // Top Navigation
    'nav' => [
        'chapter_1' => '1. Nỗi sợ đời thực',
        'chapter_2' => '2. Trải nghiệm mô phỏng',
        'chapter_3' => '3. Triết lý CSI',
        'chapter_4' => '4. Mở lời thử ngay',
    ],

    // Chương 1: The Freeze & Hook
    'chapter_1' => [
        'sticker_left' => '“Could I get a flat white with...”',
        'sticker_right' => '“Take your time, no rush!”',
        'eyebrow_badge' => 'AI REAL-WORLD ENGLISH SIMULATOR',
        'eyebrow_text' => 'Vượt qua nỗi sợ mở lời.',
        'heading_line1' => 'Khoảnh khắc bạn muốn nói...',
        'heading_line2' => 'nhưng cổ họng bỗng nghẹn lại.',
        'subtitle' => 'Bạn đã làm bài tập ngữ pháp hàng năm trời. Nhưng khi đứng trước quầy cà phê ở Melbourne hay hải quan sân bay Tokyo, chỉ một câu hỏi bất ngờ cũng khiến não bộ rơi vào trạng thái <strong>&ldquo;đông cứng&rdquo;</strong>.',
        'scenario_badge' => 'TÌNH HUỐNG THỰC TẾ &bull; KHOẢNH KHẮC 3 GIÂY ĐẦU',
        'bubble_ai_speaker' => 'Barista Emma (Melbourne):',
        'bubble_ai_time' => '09:42 AM',
        'bubble_ai_text' => '&ldquo;Hi there! Sorry, we completely ran out of oat milk just now... Would you like soy or almond milk instead?&rdquo;',
        'bubble_user_label' => 'Bạn',
        'bubble_user_thought_header' => 'Cơn bão suy nghĩ trong đầu:',
        'bubble_user_thought_text' => '&ldquo;Ủa... oat milk hết thì mình đổi sang soy milk được không? Cấu trúc câu lịch sự là gì nhỉ? Nói sai thì nhân viên có nhăn mặt không... Thôi gật đầu đại cho xong...&rdquo;',
        'cause_text' => 'Nguyên nhân: <strong>Nỗi sợ phán xét</strong>, không phải do thiếu từ vựng.',
        'view_solution' => 'Xem giải pháp',
    ],

    // Chương 2: Living Simulator & Stepper
    'chapter_2' => [
        'eyebrow' => 'KHÔNG GIAN GIẢ LẬP ĐỜI THỰC &bull; 0% PHÁN XÉT',
        'heading_line1' => 'Mô phỏng 1 cuộc gọi video thật.',
        'heading_line2' => 'Nơi bạn được phép sai và nói lại.',
        'subtitle' => 'Không bài tập điền từ. Bạn đối thoại trực diện 1-1 với AI trong giao diện Video Call chân thật, có phao cứu sinh giàn giáo và không gian suy nghĩ thư thái.',
        'auto_slide_5s' => 'Tự động chuyển sau 5s',
        'paused_hover' => 'Tạm dừng (Rê chuột)',
        'click_hint' => 'Bấm thẻ hoặc nút mũi tên để xem ngay',
        'prev_step' => 'Bước trước',
        'next_step' => 'Bước tiếp theo',

        'card_1' => [
            'tag' => 'KHÔNG GIAN AN TOÀN',
            'title' => 'Đối tác kiên nhẫn &bull; 0% phán xét',
            'desc' => 'Barista Emma mỉm cười, ngữ điệu tự nhiên và không bao giờ ngắt lời hay phán xét khi bạn ấp úng.',
        ],
        'card_2' => [
            'tag' => 'CHIẾC PHAO GIÀN GIÁO',
            'title' => 'Đỡ lời tức thì khi gặp biến số',
            'desc' => 'Hết món hay đổi ý? Có sẵn ý định + khung câu rỗng + từ khóa then chốt [🔊] cứu nguy ngay.',
        ],
        'card_3' => [
            'tag' => '10-15S SUY NGHĨ & NÓI LẠI',
            'title' => 'Thư thái mở lời & Nút nói lại',
            'desc' => 'Không áp lực đếm ngược. Nếu lỡ nói vấp? Bấm nút [↺ Nói lại] xóa câu và đọc lại ngay lập tức.',
        ],

        'simulator' => [
            'calling' => 'Đang gọi 1-1: Emma &bull; Barista Melbourne',
            'step_1' => 'Bước 1',
            'step_2' => 'Bước 2',
            'step_3' => 'Bước 3',
            'time_level' => '⏱️ 01:24 &bull; Cấp độ A2',
            'live_speech' => '● Live Web Speech',
            'state_1_tag' => 'EMMA VỪA NÓI:',
            'state_1_text' => '&ldquo;Hi there! Welcome to Melbourne Roastery. What can I get started for you today?&rdquo;',
            'state_2_incident' => '<strong>Biến số đời thực:</strong> Quán hết sữa hạt, Emma hỏi bạn có muốn đổi sang sữa đậu nành không?',
            'state_2_scaffold_tag' => 'CHIẾC PHAO GIÀN GIÁO:',
            'state_2_tips_badge' => '[💡 Tips bật sẵn]',
            'state_2_sentence' => '&ldquo;Sure, <span class="text-amber-300 underline font-mono bg-amber-400/10 px-1 rounded">soy milk is fine</span>, thanks!&rdquo;',
            'state_2_keywords_label' => 'Từ khóa:',
            'keyword_soy_milk' => 'soy milk [🔊]',
            'keyword_instead' => 'instead [🔊]',
            'state_3_tag' => 'ĐẾN LƯỢT BẠN MỞ LỜI:',
            'state_3_status' => 'Micro đang lắng nghe... Thư thái nói nhé!',
            'retry_btn' => 'Nói lại',
            'done_btn' => 'Xong',
            'footer_private' => 'Phòng luyện tập riêng tư 100%',
            'footer_speed' => 'Phản hồi &le; 1.2s',
        ],
    ],

    // Chương 3: Triết lý CSI
    'chapter_3' => [
        'eyebrow' => 'TRIẾT LÝ ĐÁNH GIÁ NHÂN VĂN',
        'heading' => 'Không Điểm Số Đỏ Lòm. Chỉ Có Chỉ Số CSI Thấu Cảm.',
        'subtitle' => 'Trong giao tiếp thực tế, quan trọng nhất là: <strong>Bạn có đạt được mục tiêu và kết nối được với con người hay không?</strong> Không ai trừ điểm bạn vì chia sai một mạo từ nhỏ.',
        'card_tag' => 'KẾT QUẢ PHIÊN ĐỐI THOẠI MẪU',
        'card_title' => 'Chỉ Số Thành Công Giao Tiếp (CSI Score)',
        'card_feedback' => '&ldquo;Bạn đã gọi được ly cà phê đúng ý mình chỉ sau 2 phút, xử lý linh hoạt khi đổi sang sữa đậu nành!&rdquo;',
        'pillar_1_tag' => '40% RESOLUTION',
        'pillar_1_title' => 'Giải Quyết Mục Tiêu',
        'pillar_1_desc' => 'Bạn đã truyền đạt đúng thông điệp và nhận được thứ mình cần (ly cafe, phòng khách sạn, hỏi đường).',
        'pillar_2_tag' => '35% ADAPTABILITY',
        'pillar_2_title' => 'Ứng Biến Linh Hoạt',
        'pillar_2_desc' => 'Khả năng giữ bình tĩnh và chuyển hướng tự nhiên khi có biến số đời thường phát sinh.',
        'pillar_3_tag' => '25% FLOW',
        'pillar_3_title' => 'Nhịp Điệu Tự Nhiên',
        'pillar_3_desc' => 'Tốc độ phản xạ, sự tự tin và giảm dần khoảng lặng ngập ngừng qua từng ngày.',
    ],

    // Chương 4: Mở lời thử ngay (Micro-Voice & CTA)
    'chapter_4' => [
        'eyebrow' => 'TRẢI NGHIỆM TƯƠNG TÁC TẠI CHỖ',
        'heading_line1' => 'Đừng chờ đợi sự hoàn hảo.',
        'heading_line2' => 'Hãy mở lời ngay bây giờ.',
        'subtitle' => 'Bấm vào nút bên dưới để nghe câu chào từ Emma hoặc nói thử một câu ngắn vào micro của bạn ngay trên trang này:',
        'emma_ask' => 'Emma hỏi bạn:',
        'listen_emma_btn' => 'Nghe Emma nói [🔊]',
        'emma_question' => '&ldquo;Hi there! What\'s your favorite drink in the morning?&rdquo;',
        'user_sentence_tag' => 'CÂU BẠN NÓI:',
        'user_sentence_default' => 'Bấm nút micro và nói: I love a cup of black coffee.',
        'recording_status' => 'Đang lắng nghe...',
        'record_button' => 'Nói thử 1 câu',
        'cta_button' => 'VÀO PHÒNG LUYỆN NÓI NGAY &mdash; 100% MIỄN PHÍ',
        'badge_no_card' => 'Không cần thẻ ngân hàng',
        'badge_no_login' => 'Không bắt buộc đăng nhập',
        'badge_in_browser' => '100% Chạy trên trình duyệt',

        'js' => [
            'prompt_speech' => '🎙️ Hãy nói vào micro một câu tiếng Anh bất kỳ...',
            'speech_success' => '👏 Tuyệt vời! ":transcript" phản xạ rất tự nhiên và dứt khoát!',
            'speech_fallback' => '🎉 Nhận diện thành công: Bạn phát âm rất rõ và tự tin!',
            'speech_error' => '👍 Nhận diện tốt! Bạn thử vào phòng thật để đối thoại sâu hơn nhé.',
            'speech_not_supported' => 'Trình duyệt chưa hỗ trợ Web Speech Synthesis.',
        ],
    ],

    // Footer
    'footer' => [
        'tagline' => 'AI Real-World English Communication Simulator',
        'scenarios' => 'Phòng kịch bản',
        'notes' => 'Giấy Note thông minh',
        'admin_costs' => 'Quản trị chi phí',
        'rights_reserved' => 'All rights reserved.',
        'security_notice' => 'Xử lý âm thanh Web Speech API &bull; 100% Bảo mật dữ liệu người học',
    ],
];
