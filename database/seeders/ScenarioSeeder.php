<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Character;
use App\Models\Scenario;
use App\Models\ScenarioHint;
use Illuminate\Database\Seeder;

class ScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scenariosData = [
            // -------------------------------------------------------------
            // KỊCH BẢN 1: MUA SẮM (SHOPPING) - TRẢ HÀNG TẠI SIÊU THỊ & HOÀN TIỀN
            // -------------------------------------------------------------
            [
                'scenario' => [
                    'slug' => 'supermarket-refund',
                    'title' => 'Trả Hàng Tại Siêu Thị & Hoàn Tiền',
                    'category' => 'shopping',
                    'cover_image' => '/images/scenarios/supermarket.jpg',
                    'estimated_minutes' => 3,
                    'supported_levels' => ['A1', 'A2', 'B1', 'B2'],
                    'location_name' => 'FreshMart Central Supermarket, Chicago',
                    'context_note' => 'Bạn mua nhầm một món đồ gia dụng/thực phẩm đóng hộp tại siêu thị và muốn mang hóa đơn đến quầy dịch vụ khách hàng để trả hàng và đề nghị hoàn tiền.',
                    'detailed_description' => 'Bạn đang có mặt tại quầy Dịch vụ Khách hàng (Customer Service Counter) của siêu thị FreshMart. Bạn mang theo món đồ còn nguyên tem mác cùng biên lai thanh toán. Nhân viên Sarah rất lịch sự nhưng cần kiểm tra quy định trả hàng của siêu thị.',
                    'twist_prompt' => 'Sarah checks the system and explains that per store policy for card payments, card refund takes 3 to 5 business days, OR she can issue an immediate store credit voucher with a 5% bonus today. She politely asks which option you prefer.',
                    'is_active' => true,
                ],
                'characters' => [
                    [
                        'name' => 'Sarah',
                        'role' => 'Customer Service Representative',
                        'role_title' => 'Customer Service Representative',
                        'avatar_path' => '/images/avatars/sarah.png',
                        'avatar_url' => '/images/avatars/sarah.png',
                        'voice_id' => 'en-US-JennyNeural',
                        'voice_name' => 'en-US-JennyNeural',
                        'personality' => 'Nhã nhặn, kiên nhẫn, tác phong dịch vụ khách hàng chuyên nghiệp, giải thích rõ ràng các chính sách đổi trả.',
                        'tone_of_voice' => 'Polite & Helpful',
                        'speech_rate' => 1.00,
                        'first_message' => 'Hello! Welcome to FreshMart Customer Service. How can I assist you with your purchase today?',
                        'first_message_translation' => 'Xin chào! Chào mừng bạn đến với quầy Dịch vụ Khách hàng FreshMart. Tôi có thể hỗ trợ gì cho món đồ bạn đã mua hôm nay?',
                        'system_prompt' => 'You are Sarah, a professional and courteous customer service representative at FreshMart Supermarket in Chicago. Keep replies spoken, natural, concise (1-2 sentences), and help the customer process their return comfortably.',
                        'system_persona_prompt' => 'You are Sarah, a professional and courteous customer service representative at FreshMart Supermarket in Chicago. Keep replies spoken, natural, concise (1-2 sentences), and help the customer process their return comfortably.',
                        'is_default' => true,
                    ],
                    [
                        'name' => 'David',
                        'role' => 'Store Supervisor',
                        'role_title' => 'Store Supervisor',
                        'avatar_path' => '/images/avatars/david.png',
                        'avatar_url' => '/images/avatars/david.png',
                        'voice_id' => 'en-US-GuyNeural',
                        'voice_name' => 'en-US-GuyNeural',
                        'personality' => 'Điềm đạm, quyết đoán, tuân thủ nguyên tắc quản lý cửa hàng nhưng sẵn sàng hỗ trợ khách hàng nhanh chóng.',
                        'tone_of_voice' => 'Authoritative & Firm',
                        'speech_rate' => 0.95,
                        'first_message' => 'Good day! I am David, the store supervisor. How may I resolve your return request today?',
                        'first_message_translation' => 'Chào bạn! Tôi là David, giám sát cửa hàng. Tôi có thể giải quyết yêu cầu trả hàng của bạn như thế nào hôm nay?',
                        'system_prompt' => 'You are David, a direct and principled store supervisor at FreshMart. You speak calmly and authoritatively in 1-2 concise sentences, focusing on store guidelines and efficient customer solutions.',
                        'system_persona_prompt' => 'You are David, a direct and principled store supervisor at FreshMart. You speak calmly and authoritatively in 1-2 concise sentences, focusing on store guidelines and efficient customer solutions.',
                        'is_default' => false,
                    ],
                    [
                        'name' => 'Alex',
                        'role' => 'Junior Cashier',
                        'role_title' => 'Junior Cashier',
                        'avatar_path' => '/images/avatars/jack.png',
                        'avatar_url' => '/images/avatars/jack.png',
                        'voice_id' => 'en-US-TonyNeural',
                        'voice_name' => 'en-US-TonyNeural',
                        'personality' => 'Trẻ trung, năng động, nhiệt tình giúp đỡ, phát âm nhanh và tự nhiên như nhân viên trẻ.',
                        'tone_of_voice' => 'Energetic & Cheerful',
                        'speech_rate' => 1.05,
                        'first_message' => 'Hi there! Alex here at the front counter. Got something you need to return or exchange?',
                        'first_message_translation' => 'Chào bạn! Mình là Alex ở quầy thu ngân. Bạn có món đồ nào cần trả hoặc đổi không?',
                        'system_prompt' => 'You are Alex, an enthusiastic and friendly junior cashier at FreshMart. Keep replies brisk, conversational, and helpful in 1-2 short sentences.',
                        'system_persona_prompt' => 'You are Alex, an enthusiastic and friendly junior cashier at FreshMart. Keep replies brisk, conversational, and helpful in 1-2 short sentences.',
                        'is_default' => false,
                    ],
                ],
                'hints' => [
                    [
                        'level' => 'A1',
                        'intent_label' => 'Chào và thông báo bạn muốn trả lại món hàng cùng hóa đơn',
                        'vietnamese_intent' => 'Chào và thông báo bạn muốn trả lại món hàng cùng hóa đơn',
                        'sentence_frame' => 'Hello, I want to return this item. Here is my receipt.',
                        'keywords_data' => [
                            ['word' => 'return', 'ipa' => '/rɪˈtɜːrn/', 'meaning' => 'trả lại món đồ', 'audio' => '/audio/keywords/return.mp3'],
                            ['word' => 'receipt', 'ipa' => '/rɪˈsiːt/', 'meaning' => 'hóa đơn mua hàng', 'audio' => '/audio/keywords/receipt.mp3'],
                            ['word' => 'please', 'ipa' => '/pliːz/', 'meaning' => 'làm ơn / lịch sự', 'audio' => '/audio/keywords/please.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'return', 'ipa' => '/rɪˈtɜːrn/', 'meaning' => 'trả lại món đồ', 'audio' => '/audio/keywords/return.mp3'],
                            ['word' => 'receipt', 'ipa' => '/rɪˈsiːt/', 'meaning' => 'hóa đơn mua hàng', 'audio' => '/audio/keywords/receipt.mp3'],
                            ['word' => 'please', 'ipa' => '/pliːz/', 'meaning' => 'làm ơn / lịch sự', 'audio' => '/audio/keywords/please.mp3'],
                        ],
                        'sort_order' => 1,
                        'step_order' => 1,
                    ],
                    [
                        'level' => 'A2',
                        'intent_label' => 'Giải thích mua nhầm món hàng và muốn hoàn lại tiền',
                        'vietnamese_intent' => 'Giải thích mua nhầm món hàng và muốn hoàn lại tiền',
                        'sentence_frame' => 'I bought the wrong item by mistake. Could I get a refund, please?',
                        'keywords_data' => [
                            ['word' => 'wrong item', 'ipa' => '/rɔːŋ ˈaɪ.təm/', 'meaning' => 'món hàng nhầm lẫn', 'audio' => '/audio/keywords/wrong_item.mp3'],
                            ['word' => 'refund', 'ipa' => '/ˈriː.fʌnd/', 'meaning' => 'hoàn tiền', 'audio' => '/audio/keywords/refund.mp3'],
                            ['word' => 'by mistake', 'ipa' => '/baɪ mɪˈsteɪk/', 'meaning' => 'do sơ suất nhầm lẫn', 'audio' => '/audio/keywords/by_mistake.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'wrong item', 'ipa' => '/rɔːŋ ˈaɪ.təm/', 'meaning' => 'món hàng nhầm lẫn', 'audio' => '/audio/keywords/wrong_item.mp3'],
                            ['word' => 'refund', 'ipa' => '/ˈriː.fʌnd/', 'meaning' => 'hoàn tiền', 'audio' => '/audio/keywords/refund.mp3'],
                            ['word' => 'by mistake', 'ipa' => '/baɪ mɪˈsteɪk/', 'meaning' => 'do sơ suất nhầm lẫn', 'audio' => '/audio/keywords/by_mistake.mp3'],
                        ],
                        'sort_order' => 2,
                        'step_order' => 2,
                    ],
                    [
                        'level' => 'B1',
                        'intent_label' => 'Hỏi về chính sách hoàn tiền vào thẻ tín dụng hay nhận tiền mặt',
                        'vietnamese_intent' => 'Hỏi về chính sách hoàn tiền vào thẻ tín dụng hay nhận tiền mặt',
                        'sentence_frame' => 'Will the refund go back to my credit card directly, or can I receive cash instead?',
                        'keywords_data' => [
                            ['word' => 'credit card', 'ipa' => '/ˈkrɛd.ɪt kɑːrd/', 'meaning' => 'thẻ tín dụng', 'audio' => '/audio/keywords/credit_card.mp3'],
                            ['word' => 'store policy', 'ipa' => '/stɔːr ˈpɑː.lə.si/', 'meaning' => 'chính sách cửa hàng', 'audio' => '/audio/keywords/store_policy.mp3'],
                            ['word' => 'process', 'ipa' => '/ˈprɑː.sɛs/', 'meaning' => 'xử lý thủ tục', 'audio' => '/audio/keywords/process.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'credit card', 'ipa' => '/ˈkrɛd.ɪt kɑːrd/', 'meaning' => 'thẻ tín dụng', 'audio' => '/audio/keywords/credit_card.mp3'],
                            ['word' => 'store policy', 'ipa' => '/stɔːr ˈpɑː.lə.si/', 'meaning' => 'chính sách cửa hàng', 'audio' => '/audio/keywords/store_policy.mp3'],
                            ['word' => 'process', 'ipa' => '/ˈprɑː.sɛs/', 'meaning' => 'xử lý thủ tục', 'audio' => '/audio/keywords/process.mp3'],
                        ],
                        'sort_order' => 3,
                        'step_order' => 3,
                    ],
                    [
                        'level' => 'B2',
                        'intent_label' => 'Xử lý tình huống chính sách 3-5 ngày thẻ vs voucher nhận ngay kèm ưu đãi',
                        'vietnamese_intent' => 'Xử lý tình huống chính sách 3-5 ngày thẻ vs voucher nhận ngay kèm ưu đãi',
                        'sentence_frame' => 'The store voucher with 5% bonus sounds great, I will take that option!',
                        'keywords_data' => [
                            ['word' => 'store voucher', 'ipa' => '/stɔːr ˈvaʊ.tʃər/', 'meaning' => 'phiếu mua hàng ưu đãi', 'audio' => '/audio/keywords/store_voucher.mp3'],
                            ['word' => 'business days', 'ipa' => '/ˈbɪz.nɪs deɪz/', 'meaning' => 'ngày làm việc', 'audio' => '/audio/keywords/business_days.mp3'],
                            ['word' => 'bonus', 'ipa' => '/ˈboʊ.nəs/', 'meaning' => 'ưu đãi thưởng thêm', 'audio' => '/audio/keywords/bonus.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'store voucher', 'ipa' => '/stɔːr ˈvaʊ.tʃər/', 'meaning' => 'phiếu mua hàng ưu đãi', 'audio' => '/audio/keywords/store_voucher.mp3'],
                            ['word' => 'business days', 'ipa' => '/ˈbɪz.nɪs deɪz/', 'meaning' => 'ngày làm việc', 'audio' => '/audio/keywords/business_days.mp3'],
                            ['word' => 'bonus', 'ipa' => '/ˈboʊ.nəs/', 'meaning' => 'ưu đãi thưởng thêm', 'audio' => '/audio/keywords/bonus.mp3'],
                        ],
                        'sort_order' => 4,
                        'step_order' => 4,
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // KỊCH BẢN 2: ĂN UỐNG (DINING) - GỌI CÀ PHÊ TẠI QUÁN LOCAL ROASTERY
            // -------------------------------------------------------------
            [
                'scenario' => [
                    'slug' => 'local-roastery-coffee',
                    'title' => 'Gọi Cà Phê Tại Quán Local Roastery',
                    'category' => 'dining',
                    'cover_image' => '/images/scenarios/coffee-roastery.jpg',
                    'estimated_minutes' => 3,
                    'supported_levels' => ['A1', 'A2', 'B1', 'B2'],
                    'location_name' => 'Central Brew Roastery, Seattle',
                    'context_note' => 'Một buổi sáng ghé quán cà phê rang xay thủ công quen thuộc, hương hạt Arabica thoang thoảng.',
                    'detailed_description' => 'Quán rang xay cà phê thủ công địa phương nhộn nhịp vào buổi sáng. Bạn muốn order một ly cà phê theo khẩu vị riêng (loại sữa, lượng đá, độ ngọt). Barista rất thân thiện nhưng quán khá đông, tạo cảm giác giao tiếp đời thực tự nhiên.',
                    'twist_prompt' => 'The espresso grinder is having a temporary reset for 5 minutes. Emma politely asks if you would mind waiting 5 minutes for your espresso or if you would prefer to switch to a smooth cold brew that is ready right now.',
                    'is_active' => true,
                ],
                'characters' => [
                    [
                        'name' => 'Emma',
                        'role' => 'Friendly Barista',
                        'role_title' => 'Friendly Barista',
                        'avatar_path' => '/images/avatars/emma.png',
                        'avatar_url' => '/images/avatars/emma.png',
                        'voice_id' => 'en-US-EmmaNeural',
                        'voice_name' => 'en-US-EmmaNeural',
                        'personality' => 'Thân thiện, niềm nở, kiên nhẫn với người học, phát âm tiếng Anh chuẩn Mỹ rõ ràng.',
                        'tone_of_voice' => 'Warm & Friendly',
                        'speech_rate' => 1.00,
                        'first_message' => 'Good morning! Welcome to Central Brew Roastery. What can I craft for you today?',
                        'first_message_translation' => 'Chào buổi sáng! Chào mừng bạn đến với Central Brew Roastery. Hôm nay tôi có thể pha chế món gì cho bạn nhỉ?',
                        'system_prompt' => 'You are Emma, a warm and welcoming barista at Central Brew Roastery in Seattle. Speak in natural spoken English with concise 1-2 sentence replies. Help the user order their drink comfortably.',
                        'system_persona_prompt' => 'You are Emma, a warm and welcoming barista at Central Brew Roastery in Seattle. Speak in natural spoken English with concise 1-2 sentence replies. Help the user order their drink comfortably.',
                        'is_default' => true,
                    ],
                    [
                        'name' => 'Jack',
                        'role' => 'Busy Morning Barista',
                        'role_title' => 'Busy Morning Barista',
                        'avatar_path' => '/images/avatars/jack.png',
                        'avatar_url' => '/images/avatars/jack.png',
                        'voice_id' => 'en-US-TonyNeural',
                        'voice_name' => 'en-US-TonyNeural',
                        'personality' => 'Nhanh nhẹn, dứt khoát, quen với nhịp độ đông đúc buổi sáng, giọng điệu thúc đẩy đặt món nhanh gọn.',
                        'tone_of_voice' => 'Fast & Direct',
                        'speech_rate' => 1.10,
                        'first_message' => 'Morning! Line moves fast today—what drink size and roast can I get started for you?',
                        'first_message_translation' => 'Chào buổi sáng! Quán đang vào giờ cao điểm—bạn chọn món gì, cỡ nào để mình làm ngay nhé?',
                        'system_prompt' => 'You are Jack, a fast-paced morning barista handling a long queue at Central Brew Roastery. Keep your responses sharp, lively, and 1-2 sentences maximum.',
                        'system_persona_prompt' => 'You are Jack, a fast-paced morning barista handling a long queue at Central Brew Roastery. Keep your responses sharp, lively, and 1-2 sentences maximum.',
                        'is_default' => false,
                    ],
                    [
                        'name' => 'Oliver',
                        'role' => 'Coffee Specialist',
                        'role_title' => 'Coffee Specialist',
                        'avatar_path' => '/images/avatars/david.png',
                        'avatar_url' => '/images/avatars/david.png',
                        'voice_id' => 'en-US-DavisNeural',
                        'voice_name' => 'en-US-DavisNeural',
                        'personality' => 'Sâu sắc, đam mê cà phê đặc sản, thích tư vấn hương vị hạt rang và phong cách pour-over.',
                        'tone_of_voice' => 'Passionate & Calm',
                        'speech_rate' => 0.95,
                        'first_message' => 'Hello coffee lover! We have some fresh Ethiopian single-origin beans roasted yesterday. What notes are you craving today?',
                        'first_message_translation' => 'Xin chào tín đồ cà phê! Hôm nay quán có mẻ hạt Ethiopia rang mộc mới toanh từ hôm qua. Bạn muốn thưởng thức nốt hương vị nào?',
                        'system_prompt' => 'You are Oliver, a passionate coffee roaster and specialist at Central Brew Roastery. You speak smoothly, guiding the customer through tasting profiles in 1-2 elegant sentences.',
                        'system_persona_prompt' => 'You are Oliver, a passionate coffee roaster and specialist at Central Brew Roastery. You speak smoothly, guiding the customer through tasting profiles in 1-2 elegant sentences.',
                        'is_default' => false,
                    ],
                ],
                'hints' => [
                    [
                        'level' => 'A1',
                        'intent_label' => 'Chào và gọi một cốc cà phê sữa nhỏ',
                        'vietnamese_intent' => 'Chào và gọi một cốc cà phê sữa nhỏ',
                        'sentence_frame' => 'Hello, one small latte, please.',
                        'keywords_data' => [
                            ['word' => 'latte', 'ipa' => '/ˈlɑː.teɪ/', 'meaning' => 'cà phê sữa', 'audio' => '/audio/keywords/latte.mp3'],
                            ['word' => 'small', 'ipa' => '/smɔːl/', 'meaning' => 'kích cỡ nhỏ', 'audio' => '/audio/keywords/small.mp3'],
                            ['word' => 'please', 'ipa' => '/pliːz/', 'meaning' => 'làm ơn / lịch sự', 'audio' => '/audio/keywords/please.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'latte', 'ipa' => '/ˈlɑː.teɪ/', 'meaning' => 'cà phê sữa', 'audio' => '/audio/keywords/latte.mp3'],
                            ['word' => 'small', 'ipa' => '/smɔːl/', 'meaning' => 'kích cỡ nhỏ', 'audio' => '/audio/keywords/small.mp3'],
                            ['word' => 'please', 'ipa' => '/pliːz/', 'meaning' => 'làm ơn / lịch sự', 'audio' => '/audio/keywords/please.mp3'],
                        ],
                        'sort_order' => 1,
                        'step_order' => 1,
                    ],
                    [
                        'level' => 'A2',
                        'intent_label' => 'Gọi một ly latte ít đường và chọn loại sữa',
                        'vietnamese_intent' => 'Gọi một ly latte ít đường và chọn loại sữa',
                        'sentence_frame' => 'Could I have a [size] latte with [milk_type] and less sugar, please?',
                        'keywords_data' => [
                            ['word' => 'latte', 'ipa' => '/ˈlɑː.teɪ/', 'meaning' => 'cà phê latte', 'audio' => '/audio/keywords/latte.mp3'],
                            ['word' => 'less sugar', 'ipa' => '/lɛs ˈʃʊɡ.ər/', 'meaning' => 'ít đường', 'audio' => '/audio/keywords/less_sugar.mp3'],
                            ['word' => 'oat milk', 'ipa' => '/oʊt mɪlk/', 'meaning' => 'sữa yến mạch', 'audio' => '/audio/keywords/oat_milk.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'latte', 'ipa' => '/ˈlɑː.teɪ/', 'meaning' => 'cà phê latte', 'audio' => '/audio/keywords/latte.mp3'],
                            ['word' => 'less sugar', 'ipa' => '/lɛs ˈʃʊɡ.ər/', 'meaning' => 'ít đường', 'audio' => '/audio/keywords/less_sugar.mp3'],
                            ['word' => 'oat milk', 'ipa' => '/oʊt mɪlk/', 'meaning' => 'sữa yến mạch', 'audio' => '/audio/keywords/oat_milk.mp3'],
                        ],
                        'sort_order' => 2,
                        'step_order' => 2,
                    ],
                    [
                        'level' => 'B1',
                        'intent_label' => 'Hỏi về loại hạt cà phê đặc trưng của quán và gọi mang đi',
                        'vietnamese_intent' => 'Hỏi về loại hạt cà phê đặc trưng của quán và gọi mang đi',
                        'sentence_frame' => "What kind of roast do you recommend today? I'd like to take it to go, please.",
                        'keywords_data' => [
                            ['word' => 'house blend', 'ipa' => '/haʊs blɛnd/', 'meaning' => 'hạt pha trộn đặc trưng', 'audio' => '/audio/keywords/house_blend.mp3'],
                            ['word' => 'to go', 'ipa' => '/tuː ɡoʊ/', 'meaning' => 'mang đi', 'audio' => '/audio/keywords/to_go.mp3'],
                            ['word' => 'medium roast', 'ipa' => '/ˈmiː.di.əm roʊst/', 'meaning' => 'rang vừa', 'audio' => '/audio/keywords/medium_roast.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'house blend', 'ipa' => '/haʊs blɛnd/', 'meaning' => 'hạt pha trộn đặc trưng', 'audio' => '/audio/keywords/house_blend.mp3'],
                            ['word' => 'to go', 'ipa' => '/tuː ɡoʊ/', 'meaning' => 'mang đi', 'audio' => '/audio/keywords/to_go.mp3'],
                            ['word' => 'medium roast', 'ipa' => '/ˈmiː.di.əm roʊst/', 'meaning' => 'rang vừa', 'audio' => '/audio/keywords/medium_roast.mp3'],
                        ],
                        'sort_order' => 3,
                        'step_order' => 3,
                    ],
                    [
                        'level' => 'B2',
                        'intent_label' => 'Xử lý tình huống máy pha cà phê reset: đồng ý chờ hoặc chuyển sang cold brew',
                        'vietnamese_intent' => 'Xử lý tình huống máy pha cà phê reset: đồng ý chờ hoặc chuyển sang cold brew',
                        'sentence_frame' => "No problem at all, I don't mind waiting five minutes! / Actually, I'll switch to the cold brew then.",
                        'keywords_data' => [
                            ['word' => 'no problem at all', 'ipa' => '/noʊ ˈprɑː.bləm æt ɔːl/', 'meaning' => 'không thành vấn đề', 'audio' => '/audio/keywords/no_problem.mp3'],
                            ['word' => 'cold brew', 'ipa' => '/koʊld bruː/', 'meaning' => 'cà phê ủ lạnh', 'audio' => '/audio/keywords/cold_brew.mp3'],
                            ['word' => "don't mind waiting", 'ipa' => '/doʊnt maɪnd ˈweɪ.tɪŋ/', 'meaning' => 'không ngại chờ đợi', 'audio' => '/audio/keywords/waiting.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'no problem at all', 'ipa' => '/noʊ ˈprɑː.bləm æt ɔːl/', 'meaning' => 'không thành vấn đề', 'audio' => '/audio/keywords/no_problem.mp3'],
                            ['word' => 'cold brew', 'ipa' => '/koʊld bruː/', 'meaning' => 'cà phê ủ lạnh', 'audio' => '/audio/keywords/cold_brew.mp3'],
                            ['word' => "don't mind waiting", 'ipa' => '/doʊnt maɪnd ˈweɪ.tɪŋ/', 'meaning' => 'không ngại chờ đợi', 'audio' => '/audio/keywords/waiting.mp3'],
                        ],
                        'sort_order' => 4,
                        'step_order' => 4,
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // KỊCH BẢN 3: DU LỊCH & KHÁCH SẠN (TRAVEL) - CHECK-IN KHÁCH SẠN
            // -------------------------------------------------------------
            [
                'scenario' => [
                    'slug' => 'hotel-check-in',
                    'title' => 'Check-in Khách Sạn & Nhận Phòng',
                    'category' => 'travel',
                    'cover_image' => '/images/scenarios/hotel-checkin.jpg',
                    'estimated_minutes' => 4,
                    'supported_levels' => ['A1', 'A2', 'B1', 'B2'],
                    'location_name' => 'The Grand Waterfront Hotel, San Francisco',
                    'context_note' => 'Bạn vừa đến khách sạn vào một buổi chiều mưa sau chuyến bay dài và cần làm thủ tục nhận phòng.',
                    'detailed_description' => 'Bạn đã đặt trước một phòng Superior hướng biển tại The Grand Waterfront Hotel. Bạn đến quầy lễ tân để xuất trình hộ chiếu, làm thủ tục check-in, hỏi về mật khẩu Wi-Fi và giờ phục vụ bữa sáng. Lễ tân Jack rất lịch thiệp và chuyên nghiệp.',
                    'twist_prompt' => 'The superior sea-view room you booked is undergoing unexpected AC maintenance for 2 hours. Jack politely offers to upgrade you immediately to a Deluxe Garden Suite on the executive floor with complimentary lounge breakfast, or you can wait for the sea view room.',
                    'is_active' => true,
                ],
                'characters' => [
                    [
                        'name' => 'Jack',
                        'role' => 'Front Desk Receptionist',
                        'role_title' => 'Front Desk Receptionist',
                        'avatar_path' => '/images/avatars/jack.png',
                        'avatar_url' => '/images/avatars/jack.png',
                        'voice_id' => 'en-US-GuyNeural',
                        'voice_name' => 'en-US-GuyNeural',
                        'personality' => 'Lịch sự, chu đáo, nghiệp vụ khách sạn chuẩn quốc tế, giọng tiếng Anh dễ nghe.',
                        'tone_of_voice' => 'Professional & Courteous',
                        'speech_rate' => 1.00,
                        'first_message' => 'Good afternoon! Welcome to The Grand Waterfront Hotel. How may I assist you with your reservation today?',
                        'first_message_translation' => 'Chào buổi chiều! Chào mừng quý khách đến với Khách sạn The Grand Waterfront. Tôi có thể hỗ trợ quý khách làm thủ tục đặt phòng hôm nay chứ?',
                        'system_prompt' => 'You are Jack, a polished and attentive front desk receptionist at The Grand Waterfront Hotel in San Francisco. Keep your verbal replies concise (1-2 sentences), professional, and warm.',
                        'system_persona_prompt' => 'You are Jack, a polished and attentive front desk receptionist at The Grand Waterfront Hotel in San Francisco. Keep your verbal replies concise (1-2 sentences), professional, and warm.',
                        'is_default' => true,
                    ],
                    [
                        'name' => 'Sarah',
                        'role' => 'VIP Guest Concierge',
                        'role_title' => 'VIP Guest Concierge',
                        'avatar_path' => '/images/avatars/sarah.png',
                        'avatar_url' => '/images/avatars/sarah.png',
                        'voice_id' => 'en-US-JennyNeural',
                        'voice_name' => 'en-US-JennyNeural',
                        'personality' => 'Tinh tế, sang trọng, quan tâm đến từng chi tiết dịch vụ và tiện nghi phòng của khách.',
                        'tone_of_voice' => 'Elegant & Attentive',
                        'speech_rate' => 0.95,
                        'first_message' => 'Welcome to Grand Waterfront VIP Lounge! It is an absolute pleasure to assist with your check-in and stay preferences today.',
                        'first_message_translation' => 'Chào mừng quý khách đến sảnh VIP Grand Waterfront! Rất hân hạnh được hỗ trợ thủ tục nhận phòng và đáp ứng các yêu cầu riêng của quý khách.',
                        'system_prompt' => 'You are Sarah, a VIP concierge at Grand Waterfront Hotel. You speak with refined hospitality, offering thoughtful assistance in 1-2 articulate sentences.',
                        'system_persona_prompt' => 'You are Sarah, a VIP concierge at Grand Waterfront Hotel. You speak with refined hospitality, offering thoughtful assistance in 1-2 articulate sentences.',
                        'is_default' => false,
                    ],
                    [
                        'name' => 'Michael',
                        'role' => 'Night Shift Clerk',
                        'role_title' => 'Night Shift Clerk',
                        'avatar_path' => '/images/avatars/michael.png',
                        'avatar_url' => '/images/avatars/michael.png',
                        'voice_id' => 'en-US-ChristopherNeural',
                        'voice_name' => 'en-US-ChristopherNeural',
                        'personality' => 'Điềm đạm, ân cần, giúp khách nhận phòng nhanh chóng sau chặng bay dài mệt mỏi.',
                        'tone_of_voice' => 'Calm & Accommodating',
                        'speech_rate' => 1.00,
                        'first_message' => 'Good evening! Hope you had a safe flight. Let me get your keycards ready right away so you can rest.',
                        'first_message_translation' => 'Chào buổi tối! Hy vọng bạn có chuyến bay an toàn. Để tôi làm thủ tục thẻ khóa ngay để bạn sớm nghỉ ngơi nhé.',
                        'system_prompt' => 'You are Michael, a calm and accommodating night shift desk agent at Grand Waterfront Hotel. Keep your spoken replies concise, comforting, and helpful (1-2 sentences).',
                        'system_persona_prompt' => 'You are Michael, a calm and accommodating night shift desk agent at Grand Waterfront Hotel. Keep your spoken replies concise, comforting, and helpful (1-2 sentences).',
                        'is_default' => false,
                    ],
                ],
                'hints' => [
                    [
                        'level' => 'A1',
                        'intent_label' => 'Chào lễ tân và thông báo bạn có đặt phòng trước',
                        'vietnamese_intent' => 'Chào lễ tân và thông báo bạn có đặt phòng trước',
                        'sentence_frame' => 'Hello, I have a reservation.',
                        'keywords_data' => [
                            ['word' => 'hello', 'ipa' => '/hɛˈloʊ/', 'meaning' => 'xin chào', 'audio' => '/audio/keywords/hello.mp3'],
                            ['word' => 'reservation', 'ipa' => '/ˌrɛz.ərˈveɪ.ʃən/', 'meaning' => 'đặt phòng trước', 'audio' => '/audio/keywords/reservation.mp3'],
                            ['word' => 'passport', 'ipa' => '/ˈpæs.pɔːrt/', 'meaning' => 'hộ chiếu', 'audio' => '/audio/keywords/passport.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'hello', 'ipa' => '/hɛˈloʊ/', 'meaning' => 'xin chào', 'audio' => '/audio/keywords/hello.mp3'],
                            ['word' => 'reservation', 'ipa' => '/ˌrɛz.ərˈveɪ.ʃən/', 'meaning' => 'đặt phòng trước', 'audio' => '/audio/keywords/reservation.mp3'],
                            ['word' => 'passport', 'ipa' => '/ˈpæs.pɔːrt/', 'meaning' => 'hộ chiếu', 'audio' => '/audio/keywords/passport.mp3'],
                        ],
                        'sort_order' => 1,
                        'step_order' => 1,
                    ],
                    [
                        'level' => 'A2',
                        'intent_label' => 'Thông báo đã đặt phòng trước dưới tên của bạn và xuất trình hộ chiếu',
                        'vietnamese_intent' => 'Thông báo đã đặt phòng trước dưới tên của bạn và xuất trình hộ chiếu',
                        'sentence_frame' => 'Hi, I have a reservation under [your_name]. Here is my passport.',
                        'keywords_data' => [
                            ['word' => 'reservation', 'ipa' => '/ˌrɛz.ərˈveɪ.ʃən/', 'meaning' => 'đặt chỗ trước', 'audio' => '/audio/keywords/reservation.mp3'],
                            ['word' => 'passport', 'ipa' => '/ˈpæs.pɔːrt/', 'meaning' => 'hộ chiếu', 'audio' => '/audio/keywords/check_in.mp3'],
                            ['word' => 'check in', 'ipa' => '/tʃɛk ɪn/', 'meaning' => 'nhận phòng', 'audio' => '/audio/keywords/check_in.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'reservation', 'ipa' => '/ˌrɛz.ərˈveɪ.ʃən/', 'meaning' => 'đặt chỗ trước', 'audio' => '/audio/keywords/reservation.mp3'],
                            ['word' => 'passport', 'ipa' => '/ˈpæs.pɔːrt/', 'meaning' => 'hộ chiếu', 'audio' => '/audio/keywords/check_in.mp3'],
                            ['word' => 'check in', 'ipa' => '/tʃɛk ɪn/', 'meaning' => 'nhận phòng', 'audio' => '/audio/keywords/check_in.mp3'],
                        ],
                        'sort_order' => 2,
                        'step_order' => 2,
                    ],
                    [
                        'level' => 'B1',
                        'intent_label' => 'Hỏi thông tin về giờ phục vụ bữa sáng và vị trí phòng tập thể hình',
                        'vietnamese_intent' => 'Hỏi thông tin về giờ phục vụ bữa sáng và vị trí phòng tập thể hình',
                        'sentence_frame' => 'Could you tell me what time breakfast is served and where the fitness center is located?',
                        'keywords_data' => [
                            ['word' => 'complimentary breakfast', 'ipa' => '/ˌkɑːm.pləˈmɛn.tər.i ˈbrɛk.fəst/', 'meaning' => 'bữa sáng miễn phí', 'audio' => '/audio/keywords/complimentary_breakfast.mp3'],
                            ['word' => 'fitness center', 'ipa' => '/ˈfɪt.nəs ˈsɛn.tər/', 'meaning' => 'trung tâm thể hình', 'audio' => '/audio/keywords/fitness_center.mp3'],
                            ['word' => 'wifi password', 'ipa' => '/ˈwaɪ.faɪ ˈpæs.wɜːrd/', 'meaning' => 'mật khẩu wifi', 'audio' => '/audio/keywords/wifi_password.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'complimentary breakfast', 'ipa' => '/ˌkɑːm.pləˈmɛn.tər.i ˈbrɛk.fəst/', 'meaning' => 'bữa sáng miễn phí', 'audio' => '/audio/keywords/complimentary_breakfast.mp3'],
                            ['word' => 'fitness center', 'ipa' => '/ˈfɪt.nəs ˈsɛn.tər/', 'meaning' => 'trung tâm thể hình', 'audio' => '/audio/keywords/fitness_center.mp3'],
                            ['word' => 'wifi password', 'ipa' => '/ˈwaɪ.faɪ ˈpæs.wɜːrd/', 'meaning' => 'mật khẩu wifi', 'audio' => '/audio/keywords/wifi_password.mp3'],
                        ],
                        'sort_order' => 3,
                        'step_order' => 3,
                    ],
                    [
                        'level' => 'B2',
                        'intent_label' => 'Phản xạ trước đề xuất đổi phòng: đồng ý nhận suite nâng cấp và hỏi về quyền lợi trả phòng trễ',
                        'vietnamese_intent' => 'Phản xạ trước đề xuất đổi phòng: đồng ý nhận suite nâng cấp và hỏi về quyền lợi trả phòng trễ',
                        'sentence_frame' => 'The garden suite upgrade sounds great, does that include late checkout tomorrow?',
                        'keywords_data' => [
                            ['word' => 'complimentary upgrade', 'ipa' => '/ˌkɑːm.pləˈmɛn.tər.i ʌpˈɡreɪd/', 'meaning' => 'nâng hạng phòng miễn phí', 'audio' => '/audio/keywords/upgrade.mp3'],
                            ['word' => 'late checkout', 'ipa' => '/leɪt ˈtʃɛk.aʊt/', 'meaning' => 'trả phòng trễ', 'audio' => '/audio/keywords/late_checkout.mp3'],
                            ['word' => 'executive lounge', 'ipa' => '/ɪɡˈzɛk.jə.tɪv laʊndʒ/', 'meaning' => 'phòng chờ thương gia', 'audio' => '/audio/keywords/executive_lounge.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'complimentary upgrade', 'ipa' => '/ˌkɑːm.pləˈmɛn.tər.i ʌpˈɡreɪd/', 'meaning' => 'nâng hạng phòng miễn phí', 'audio' => '/audio/keywords/upgrade.mp3'],
                            ['word' => 'late checkout', 'ipa' => '/leɪt ˈtʃɛk.aʊt/', 'meaning' => 'trả phòng trễ', 'audio' => '/audio/keywords/late_checkout.mp3'],
                            ['word' => 'executive lounge', 'ipa' => '/ɪɡˈzɛk.jə.tɪv laʊndʒ/', 'meaning' => 'phòng chờ thương gia', 'audio' => '/audio/keywords/executive_lounge.mp3'],
                        ],
                        'sort_order' => 4,
                        'step_order' => 4,
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // KỊCH BẢN 4: GIAO THÔNG CÔNG CỘNG (TRANSIT) - MUA VÉ TÀU ĐIỆN NGẦM
            // -------------------------------------------------------------
            [
                'scenario' => [
                    'slug' => 'subway-ticket-purchase',
                    'title' => 'Mua Vé Tàu Điện Ngầm Tại Ga Trung Tâm',
                    'category' => 'transit',
                    'cover_image' => '/images/scenarios/subway-station.jpg',
                    'estimated_minutes' => 3,
                    'supported_levels' => ['A1', 'A2', 'B1', 'B2'],
                    'location_name' => 'Grand Central Metro Station, New York',
                    'context_note' => 'Giờ cao điểm tại ga tàu điện ngầm đông đúc, bạn cần mua vé đến điểm tham quan nổi tiếng.',
                    'detailed_description' => 'Bạn đang đứng trước quầy dịch vụ khách hàng tại ga tàu điện ngầm trung tâm. Bạn muốn mua thẻ đi lại trong ngày hoặc vé khứ hồi đến Downtown, đồng thời hỏi về cổng chuyển tuyến nhanh nhất.',
                    'twist_prompt' => 'Line 1 is currently suspended due to urgent track maintenance. David informs you that you need to take Line 3 to 14th Street and transfer to the shuttle bus, or purchase a contactless MetroCard for alternative routes.',
                    'is_active' => true,
                ],
                'characters' => [
                    [
                        'name' => 'David',
                        'role' => 'Transit Assistant',
                        'role_title' => 'Transit Assistant',
                        'avatar_path' => '/images/avatars/david.png',
                        'avatar_url' => '/images/avatars/david.png',
                        'voice_id' => 'en-US-GuyNeural',
                        'voice_name' => 'en-US-GuyNeural',
                        'personality' => 'Rõ ràng, dứt khoát, quen hướng dẫn hàng trăm hành khách mỗi ngày, chỉ dẫn chính xác các tuyến tàu.',
                        'tone_of_voice' => 'Clear & Direct',
                        'speech_rate' => 1.00,
                        'first_message' => 'MTA Customer Assistance. Where are you heading today, and do you need a MetroCard or contactless pay?',
                        'first_message_translation' => 'Ban Hỗ trợ Khách hàng MTA xin nghe. Hôm nay bạn muốn đi đâu, và bạn cần mua thẻ MetroCard hay dùng chạm thanh toán không tiếp xúc?',
                        'system_prompt' => 'You are David, a transit assistant at Fulton Transit Center in New York. Speak with clear, direct subway directions in 1-2 concise sentences.',
                        'system_persona_prompt' => 'You are David, a transit assistant at Fulton Transit Center in New York. Speak with clear, direct subway directions in 1-2 concise sentences.',
                        'is_default' => true,
                    ],
                    [
                        'name' => 'Emma',
                        'role' => 'Tourist Help Desk',
                        'role_title' => 'Tourist Help Desk',
                        'avatar_path' => '/images/avatars/emma.png',
                        'avatar_url' => '/images/avatars/emma.png',
                        'voice_id' => 'en-US-EmmaNeural',
                        'voice_name' => 'en-US-EmmaNeural',
                        'personality' => 'Ân cần, cởi mở, chuyên giúp đỡ khách du lịch lần đầu đi tàu điện ngầm New York không bị lạc.',
                        'tone_of_voice' => 'Friendly & Patient',
                        'speech_rate' => 0.95,
                        'first_message' => 'Hi! Navigating NYC subways can be tricky. Where would you like to travel, and I will show you the easiest route!',
                        'first_message_translation' => 'Chào bạn! Hệ thống tàu điện NYC đôi khi hơi rối. Bạn muốn đến đâu, mình sẽ chỉ bạn tuyến đường thuận tiện nhất nhé!',
                        'system_prompt' => 'You are Emma, a patient tourist assistance officer at Fulton Transit Center. Guide the traveler step-by-step with warm, friendly 1-2 sentence replies.',
                        'system_persona_prompt' => 'You are Emma, a patient tourist assistance officer at Fulton Transit Center. Guide the traveler step-by-step with warm, friendly 1-2 sentence replies.',
                        'is_default' => false,
                    ],
                    [
                        'name' => 'Jack',
                        'role' => 'Ticketing Officer',
                        'role_title' => 'Ticketing Officer',
                        'avatar_path' => '/images/avatars/jack.png',
                        'avatar_url' => '/images/avatars/jack.png',
                        'voice_id' => 'en-US-TonyNeural',
                        'voice_name' => 'en-US-TonyNeural',
                        'personality' => 'Nhanh gọn, thực tế, thao tác nạp tiền và đổi vé tốc độ cao tại quầy bán vé.',
                        'tone_of_voice' => 'Brisk & Practical',
                        'speech_rate' => 1.10,
                        'first_message' => 'Step up, please! Single ride, 7-day unlimited, or adding value to an existing card?',
                        'first_message_translation' => 'Mời bạn lại quầy! Bạn mua vé lượt, vé 7 ngày không giới hạn hay nạp thêm tiền vào thẻ có sẵn?',
                        'system_prompt' => 'You are Jack, a fast-working ticketing officer at MTA Fulton Center. Keep responses brisk, practical, and under 2 sentences.',
                        'system_persona_prompt' => 'You are Jack, a fast-working ticketing officer at MTA Fulton Center. Keep responses brisk, practical, and under 2 sentences.',
                        'is_default' => false,
                    ],
                ],
                'hints' => [
                    [
                        'level' => 'A1',
                        'intent_label' => 'Hỏi mua một vé tàu và hỏi giá',
                        'vietnamese_intent' => 'Hỏi mua một vé tàu và hỏi giá',
                        'sentence_frame' => 'One ticket, please. How much is it?',
                        'keywords_data' => [
                            ['word' => 'ticket', 'ipa' => '/ˈtɪk.ɪt/', 'meaning' => 'vé tàu', 'audio' => '/audio/keywords/ticket.mp3'],
                            ['word' => 'please', 'ipa' => '/pliːz/', 'meaning' => 'làm ơn', 'audio' => '/audio/keywords/please.mp3'],
                            ['word' => 'how much', 'ipa' => '/haʊ mʌtʃ/', 'meaning' => 'giá bao nhiêu', 'audio' => '/audio/keywords/how_much.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'ticket', 'ipa' => '/ˈtɪk.ɪt/', 'meaning' => 'vé tàu', 'audio' => '/audio/keywords/ticket.mp3'],
                            ['word' => 'please', 'ipa' => '/pliːz/', 'meaning' => 'làm ơn', 'audio' => '/audio/keywords/please.mp3'],
                            ['word' => 'how much', 'ipa' => '/haʊ mʌtʃ/', 'meaning' => 'giá bao nhiêu', 'audio' => '/audio/keywords/how_much.mp3'],
                        ],
                        'sort_order' => 1,
                        'step_order' => 1,
                    ],
                    [
                        'level' => 'A2',
                        'intent_label' => 'Hỏi mua vé khứ hồi hoặc thẻ đi lại 1 ngày đến Downtown',
                        'vietnamese_intent' => 'Hỏi mua vé khứ hồi hoặc thẻ đi lại 1 ngày đến Downtown',
                        'sentence_frame' => "I'd like a [ticket_type] to [destination], please. How much is it?",
                        'keywords_data' => [
                            ['word' => 'round-trip ticket', 'ipa' => '/ˌraʊndˈtrɪp ˈtɪk.ɪt/', 'meaning' => 'vé khứ hồi', 'audio' => '/audio/keywords/round_trip.mp3'],
                            ['word' => 'day pass', 'ipa' => '/deɪ pæs/', 'meaning' => 'thẻ đi cả ngày', 'audio' => '/audio/keywords/day_pass.mp3'],
                            ['word' => 'platform', 'ipa' => '/ˈplæt.fɔːrm/', 'meaning' => 'sân ga đón tàu', 'audio' => '/audio/keywords/platform.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'round-trip ticket', 'ipa' => '/ˌraʊndˈtrɪp ˈtɪk.ɪt/', 'meaning' => 'vé khứ hồi', 'audio' => '/audio/keywords/round_trip.mp3'],
                            ['word' => 'day pass', 'ipa' => '/deɪ pæs/', 'meaning' => 'thẻ đi cả ngày', 'audio' => '/audio/keywords/day_pass.mp3'],
                            ['word' => 'platform', 'ipa' => '/ˈplæt.fɔːrm/', 'meaning' => 'sân ga đón tàu', 'audio' => '/audio/keywords/platform.mp3'],
                        ],
                        'sort_order' => 2,
                        'step_order' => 2,
                    ],
                    [
                        'level' => 'B1',
                        'intent_label' => 'Hỏi cách thanh toán thẻ không tiếp xúc và vị trí cổng quẹt thẻ',
                        'vietnamese_intent' => 'Hỏi cách thanh toán thẻ không tiếp xúc và vị trí cổng quẹt thẻ',
                        'sentence_frame' => 'Can I tap my contactless card directly at the turnstile, or do I need a paper ticket?',
                        'keywords_data' => [
                            ['word' => 'contactless card', 'ipa' => '/ˈkɑːn.tækt.ləs kɑːrd/', 'meaning' => 'thẻ không tiếp xúc', 'audio' => '/audio/keywords/contactless.mp3'],
                            ['word' => 'turnstile', 'ipa' => '/ˈtɜːrn.staɪl/', 'meaning' => 'cổng xoay soát vé', 'audio' => '/audio/keywords/turnstile.mp3'],
                            ['word' => 'transfer station', 'ipa' => '/ˈtræns.fɜːr ˈsteɪ.ʃən/', 'meaning' => 'ga chuyển tuyến', 'audio' => '/audio/keywords/transfer_station.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'contactless card', 'ipa' => '/ˈkɑːn.tækt.ləs kɑːrd/', 'meaning' => 'thẻ không tiếp xúc', 'audio' => '/audio/keywords/contactless.mp3'],
                            ['word' => 'turnstile', 'ipa' => '/ˈtɜːrn.staɪl/', 'meaning' => 'cổng xoay soát vé', 'audio' => '/audio/keywords/turnstile.mp3'],
                            ['word' => 'transfer station', 'ipa' => '/ˈtræns.fɜːr ˈsteɪ.ʃən/', 'meaning' => 'ga chuyển tuyến', 'audio' => '/audio/keywords/transfer_station.mp3'],
                        ],
                        'sort_order' => 3,
                        'step_order' => 3,
                    ],
                    [
                        'level' => 'B2',
                        'intent_label' => 'Xử lý sự cố gián đoạn tuyến tàu: hỏi điểm đón xe bus thay thế và thời gian di chuyển',
                        'vietnamese_intent' => 'Xử lý sự cố gián đoạn tuyến tàu: hỏi điểm đón xe bus thay thế và thời gian di chuyển',
                        'sentence_frame' => 'Since Line 1 is suspended, where exactly does the connecting shuttle bus pick up?',
                        'keywords_data' => [
                            ['word' => 'service suspension', 'ipa' => '/ˈsɜːrvɪs səˈspɛn.ʃən/', 'meaning' => 'tạm ngừng hoạt động', 'audio' => '/audio/keywords/service_suspension.mp3'],
                            ['word' => 'connecting shuttle', 'ipa' => '/kəˈnɛk.tɪŋ ˈʃʌt.əl/', 'meaning' => 'xe buýt chuyển tiếp', 'audio' => '/audio/keywords/connecting_shuttle.mp3'],
                            ['word' => 'alternative route', 'ipa' => '/ɔːlˈtɜːr.nə.tɪv ruːt/', 'meaning' => 'lộ trình thay thế', 'audio' => '/audio/keywords/alternative_route.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'service suspension', 'ipa' => '/ˈsɜːrvɪs səˈspɛn.ʃən/', 'meaning' => 'tạm ngừng hoạt động', 'audio' => '/audio/keywords/service_suspension.mp3'],
                            ['word' => 'connecting shuttle', 'ipa' => '/kəˈnɛk.tɪŋ ˈʃʌt.əl/', 'meaning' => 'xe buýt chuyển tiếp', 'audio' => '/audio/keywords/connecting_shuttle.mp3'],
                            ['word' => 'alternative route', 'ipa' => '/ɔːlˈtɜːr.nə.tɪv ruːt/', 'meaning' => 'lộ trình thay thế', 'audio' => '/audio/keywords/alternative_route.mp3'],
                        ],
                        'sort_order' => 4,
                        'step_order' => 4,
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // KỊCH BẢN 5: CÔNG SỞ & SỰ NGHIỆP (WORKPLACE) - THẢO LUẬN TIẾN ĐỘ DỰ ÁN NHÓM
            // -------------------------------------------------------------
            [
                'scenario' => [
                    'slug' => 'project-progress-meeting',
                    'title' => 'Thảo Luận Tiến Độ Dự Án Nhóm',
                    'category' => 'workplace',
                    'cover_image' => '/images/scenarios/project-meeting.jpg',
                    'estimated_minutes' => 4,
                    'supported_levels' => ['A1', 'A2', 'B1', 'B2'],
                    'location_name' => 'TechCorp Innovation Hub, Austin',
                    'context_note' => 'Cuộc trao đổi 1-1 nhanh vào đầu tuần với trưởng nhóm dự án về các đầu việc tồn đọng và thời hạn bàn giao.',
                    'detailed_description' => 'Bạn tham gia buổi họp ngắn 1-1 với Michael - Trưởng nhóm dự án (Project Lead). Bạn cần báo cáo tiến độ tính năng mình đang phụ trách, thảo luận về tài liệu API và xác nhận kế hoạch bàn giao bản thử nghiệm trước thứ Sáu.',
                    'twist_prompt' => 'Michael mentions that the client just requested a minor change in the authentication flow. He asks if you can incorporate it before Friday, or if we should push it to next sprint to protect our release deadline.',
                    'is_active' => true,
                ],
                'characters' => [
                    [
                        'name' => 'Michael',
                        'role' => 'Senior Project Lead',
                        'role_title' => 'Senior Project Lead',
                        'avatar_path' => '/images/avatars/michael.png',
                        'avatar_url' => '/images/avatars/michael.png',
                        'voice_id' => 'en-US-ChristopherNeural',
                        'voice_name' => 'en-US-ChristopherNeural',
                        'personality' => 'Tác phong lãnh đạo chuyên nghiệp, tập trung vào tiến độ, deadline và giải pháp xử lý rủi ro.',
                        'tone_of_voice' => 'Professional & Focused',
                        'speech_rate' => 1.00,
                        'first_message' => 'Good morning team. Thanks for joining. Let us quickly go around the table for current sprint updates.',
                        'first_message_translation' => 'Chào buổi sáng cả nhóm. Cảm ơn mọi người đã tham gia. Chúng ta hãy đi một vòng để cập nhật tiến độ sprint hiện tại nhé.',
                        'system_prompt' => 'You are Michael, a senior project lead at NovaTech. Keep replies professional, clear, focused on delivery milestones, and concise (1-2 sentences).',
                        'system_persona_prompt' => 'You are Michael, a senior project lead at NovaTech. Keep replies professional, clear, focused on delivery milestones, and concise (1-2 sentences).',
                        'is_default' => true,
                    ],
                    [
                        'name' => 'Sarah',
                        'role' => 'Product Manager',
                        'role_title' => 'Product Manager',
                        'avatar_path' => '/images/avatars/sarah.png',
                        'avatar_url' => '/images/avatars/sarah.png',
                        'voice_id' => 'en-US-JennyNeural',
                        'voice_name' => 'en-US-JennyNeural',
                        'personality' => 'Năng động, nhạy bén, luôn đặt trải nghiệm người dùng và giá trị sản phẩm lên hàng đầu.',
                        'tone_of_voice' => 'Dynamic & User-centric',
                        'speech_rate' => 1.05,
                        'first_message' => 'Morning everyone! From the product side, we want to ensure the upcoming release delivers real user value. What is our core blocker?',
                        'first_message_translation' => 'Chào mọi người! Về phía sản phẩm, chúng tôi muốn đảm bảo bản phát hành tới mang lại giá trị thực chất cho người dùng. Đâu là vướng mắc cốt lõi của chúng ta?',
                        'system_prompt' => 'You are Sarah, an energetic Product Manager at NovaTech. You ask sharp questions about product scope and user impact in 1-2 engaging sentences.',
                        'system_persona_prompt' => 'You are Sarah, an energetic Product Manager at NovaTech. You ask sharp questions about product scope and user impact in 1-2 engaging sentences.',
                        'is_default' => false,
                    ],
                    [
                        'name' => 'David',
                        'role' => 'Engineering Lead',
                        'role_title' => 'Engineering Lead',
                        'avatar_path' => '/images/avatars/david.png',
                        'avatar_url' => '/images/avatars/david.png',
                        'voice_id' => 'en-US-GuyNeural',
                        'voice_name' => 'en-US-GuyNeural',
                        'personality' => 'Thực tế, chú trọng kiến trúc kỹ thuật và giải pháp kiểm thử an toàn trước khi đẩy production.',
                        'tone_of_voice' => 'Technical & Pragmatic',
                        'speech_rate' => 0.95,
                        'first_message' => 'Hey team, engineering here. Let us examine the technical stability and test coverage before committing to the release date.',
                        'first_message_translation' => 'Chào nhóm, phía kỹ thuật đây. Chúng ta hãy rà soát độ ổn định kỹ thuật và tỷ lệ test coverage trước khi chốt ngày phát hành.',
                        'system_prompt' => 'You are David, a pragmatic Engineering Lead at NovaTech. You focus on code quality, testing safeguards, and architectural trade-offs in 1-2 direct sentences.',
                        'system_persona_prompt' => 'You are David, a pragmatic Engineering Lead at NovaTech. You focus on code quality, testing safeguards, and architectural trade-offs in 1-2 direct sentences.',
                        'is_default' => false,
                    ],
                ],
                'hints' => [
                    [
                        'level' => 'A1',
                        'intent_label' => 'Báo cáo ngắn gọn công việc đang tiến triển tốt và đúng hạn',
                        'vietnamese_intent' => 'Báo cáo ngắn gọn công việc đang tiến triển tốt và đúng hạn',
                        'sentence_frame' => 'Good morning! Everything is on track and going well.',
                        'keywords_data' => [
                            ['word' => 'on track', 'ipa' => '/ɑːn træk/', 'meaning' => 'đúng tiến độ', 'audio' => '/audio/keywords/on_track.mp3'],
                            ['word' => 'progress', 'ipa' => '/ˈprɑː.ɡrɛs/', 'meaning' => 'tiến độ công việc', 'audio' => '/audio/keywords/progress.mp3'],
                            ['word' => 'well', 'ipa' => '/wɛl/', 'meaning' => 'suôn sẻ / tốt', 'audio' => '/audio/keywords/well.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'on track', 'ipa' => '/ɑːn træk/', 'meaning' => 'đúng tiến độ', 'audio' => '/audio/keywords/on_track.mp3'],
                            ['word' => 'progress', 'ipa' => '/ˈprɑː.ɡrɛs/', 'meaning' => 'tiến độ công việc', 'audio' => '/audio/keywords/progress.mp3'],
                            ['word' => 'well', 'ipa' => '/wɛl/', 'meaning' => 'suôn sẻ / tốt', 'audio' => '/audio/keywords/well.mp3'],
                        ],
                        'sort_order' => 1,
                        'step_order' => 1,
                    ],
                    [
                        'level' => 'A2',
                        'intent_label' => 'Cập nhật tính năng API đang phát triển và thời gian hoàn thành',
                        'vietnamese_intent' => 'Cập nhật tính năng API đang phát triển và thời gian hoàn thành',
                        'sentence_frame' => 'I am currently working on the API integration and should finish it by tomorrow.',
                        'keywords_data' => [
                            ['word' => 'working on', 'ipa' => '/ˈwɜːr.kɪŋ ɑːn/', 'meaning' => 'đang thực hiện', 'audio' => '/audio/keywords/working_on.mp3'],
                            ['word' => 'finish', 'ipa' => '/ˈfɪn.ɪʃ/', 'meaning' => 'hoàn thành', 'audio' => '/audio/keywords/finish.mp3'],
                            ['word' => 'tomorrow', 'ipa' => '/təˈmɑːr.oʊ/', 'meaning' => 'ngày mai', 'audio' => '/audio/keywords/tomorrow.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'working on', 'ipa' => '/ˈwɜːr.kɪŋ ɑːn/', 'meaning' => 'đang thực hiện', 'audio' => '/audio/keywords/working_on.mp3'],
                            ['word' => 'finish', 'ipa' => '/ˈfɪn.ɪʃ/', 'meaning' => 'hoàn thành', 'audio' => '/audio/keywords/finish.mp3'],
                            ['word' => 'tomorrow', 'ipa' => '/təˈmɑːr.oʊ/', 'meaning' => 'ngày mai', 'audio' => '/audio/keywords/tomorrow.mp3'],
                        ],
                        'sort_order' => 2,
                        'step_order' => 2,
                    ],
                    [
                        'level' => 'B1',
                        'intent_label' => 'Hỏi về tài liệu kỹ thuật chi tiết để tích hợp tính năng',
                        'vietnamese_intent' => 'Hỏi về tài liệu kỹ thuật chi tiết để tích hợp tính năng',
                        'sentence_frame' => 'The core module is ready, but could you share the updated API specs for authentication?',
                        'keywords_data' => [
                            ['word' => 'specs', 'ipa' => '/spɛks/', 'meaning' => 'thông số kỹ thuật', 'audio' => '/audio/keywords/specs.mp3'],
                            ['word' => 'authentication', 'ipa' => '/ɔːˌθɛn.tɪˈkeɪ.ʃən/', 'meaning' => 'xác thực bảo mật', 'audio' => '/audio/keywords/authentication.mp3'],
                            ['word' => 'milestone', 'ipa' => '/ˈmaɪl.stoʊn/', 'meaning' => 'cột mốc bàn giao', 'audio' => '/audio/keywords/milestone.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'specs', 'ipa' => '/spɛks/', 'meaning' => 'thông số kỹ thuật', 'audio' => '/audio/keywords/specs.mp3'],
                            ['word' => 'authentication', 'ipa' => '/ɔːˌθɛn.tɪˈkeɪ.ʃən/', 'meaning' => 'xác thực bảo mật', 'audio' => '/audio/keywords/authentication.mp3'],
                            ['word' => 'milestone', 'ipa' => '/ˈmaɪl.stoʊn/', 'meaning' => 'cột mốc bàn giao', 'audio' => '/audio/keywords/milestone.mp3'],
                        ],
                        'sort_order' => 3,
                        'step_order' => 3,
                    ],
                    [
                        'level' => 'B2',
                        'intent_label' => 'Đánh giá rủi ro deadline và đề xuất hoãn thay đổi sang sprint sau',
                        'vietnamese_intent' => 'Đánh giá rủi ro deadline và đề xuất hoãn thay đổi sang sprint sau',
                        'sentence_frame' => "To protect Friday's release, I recommend pushing the auth change to next sprint.",
                        'keywords_data' => [
                            ['word' => 'deadline', 'ipa' => '/ˈdɛd.laɪn/', 'meaning' => 'hạn chót bàn giao', 'audio' => '/audio/keywords/deadline.mp3'],
                            ['word' => 'recommend', 'ipa' => '/ˌrɛk.əˈmɛnd/', 'meaning' => 'khuyến nghị / đề xuất', 'audio' => '/audio/keywords/recommend.mp3'],
                            ['word' => 'next sprint', 'ipa' => '/nɛkst sprɪnt/', 'meaning' => 'chu kỳ sprint kế tiếp', 'audio' => '/audio/keywords/next_sprint.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'deadline', 'ipa' => '/ˈdɛd.laɪn/', 'meaning' => 'hạn chót bàn giao', 'audio' => '/audio/keywords/deadline.mp3'],
                            ['word' => 'recommend', 'ipa' => '/ˌrɛk.əˈmɛnd/', 'meaning' => 'khuyến nghị / đề xuất', 'audio' => '/audio/keywords/recommend.mp3'],
                            ['word' => 'next sprint', 'ipa' => '/nɛkst sprɪnt/', 'meaning' => 'chu kỳ sprint kế tiếp', 'audio' => '/audio/keywords/next_sprint.mp3'],
                        ],
                        'sort_order' => 4,
                        'step_order' => 4,
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // KỊCH BẢN 6: MUA SẮM (SHOPPING) - MUA SẮM QUẦN ÁO & CHỌN SIZE
            // -------------------------------------------------------------
            [
                'scenario' => [
                    'slug' => 'clothing-shopping',
                    'title' => 'Mua Sắm Quần Áo & Chọn Size',
                    'category' => 'shopping',
                    'cover_image' => '/images/scenarios/clothing.jpg',
                    'estimated_minutes' => 3,
                    'supported_levels' => ['A1', 'A2', 'B1', 'B2'],
                    'location_name' => 'Zara Flagship Store, 5th Avenue, New York',
                    'context_note' => 'Bạn đang tìm mua một chiếc áo khoác/áo sơ mi cho sự kiện sắp tới và cần nhân viên tư vấn về size, màu sắc và vị trí phòng thử đồ.',
                    'detailed_description' => 'Bạn đang ở trong một cửa hàng thời trang bận rộn tại New York. Bạn muốn hỏi nhân viên xem chiếc áo bạn thích có size M hoặc màu khác không, thử đồ và thanh toán.',
                    'twist_prompt' => 'The stylist informs you that size M in that color is currently out of stock on the rack, but she can check the back storage OR offer a similar jacket with a 15% discount for members.',
                    'is_active' => true,
                ],
                'characters' => [
                    [
                        'name' => 'Sophie',
                        'role' => 'Fashion Stylist',
                        'role_title' => 'Fashion Stylist',
                        'avatar_path' => '/images/avatars/sarah.png',
                        'avatar_url' => '/images/avatars/sarah.png',
                        'voice_id' => 'en-US-JennyNeural',
                        'voice_name' => 'en-US-JennyNeural',
                        'personality' => 'Thanh lịch, có gu thẩm mỹ thời trang cao, tư vấn trang phục khéo léo và chu đáo với khách hàng.',
                        'tone_of_voice' => 'Chic & Helpful',
                        'speech_rate' => 1.00,
                        'first_message' => 'Hello! Welcome to Zara on 5th Avenue. Are you looking for a specific outfit or browsing our new collection today?',
                        'first_message_translation' => 'Xin chào! Chào mừng bạn đến với Zara trên Đại lộ số 5. Bạn đang tìm kiếm trang phục cụ thể nào hay muốn dạo xem bộ sưu tập mới hôm nay?',
                        'system_prompt' => 'You are Sophie, an elegant fashion stylist at Zara 5th Avenue in New York. Keep your replies stylish, polite, and spoken naturally in 1-2 concise sentences.',
                        'system_persona_prompt' => 'You are Sophie, an elegant fashion stylist at Zara 5th Avenue in New York. Keep your replies stylish, polite, and spoken naturally in 1-2 concise sentences.',
                        'is_default' => true,
                    ],
                    [
                        'name' => 'Oliver',
                        'role' => 'Store Associate',
                        'role_title' => 'Store Associate',
                        'avatar_path' => '/images/avatars/david.png',
                        'avatar_url' => '/images/avatars/david.png',
                        'voice_id' => 'en-US-GuyNeural',
                        'voice_name' => 'en-US-GuyNeural',
                        'personality' => 'Cởi mở, tháo vát, hỗ trợ nhanh chóng về kiểm tra size trong kho và hướng dẫn phòng thử đồ.',
                        'tone_of_voice' => 'Upbeat & Supportive',
                        'speech_rate' => 1.05,
                        'first_message' => 'Hi! Let me know if you need another size or want me to start a fitting room for you!',
                        'first_message_translation' => 'Chào bạn! Cứ nhắn mình nếu bạn cần đổi size khác hoặc muốn mình giữ sẵn một phòng thử đồ nhé!',
                        'system_prompt' => 'You are Oliver, an upbeat store associate at Zara 5th Avenue. Assist the customer efficiently with fitting rooms, sizes, and stock checks in 1-2 quick sentences.',
                        'system_persona_prompt' => 'You are Oliver, an upbeat store associate at Zara 5th Avenue. Assist the customer efficiently with fitting rooms, sizes, and stock checks in 1-2 quick sentences.',
                        'is_default' => false,
                    ],
                ],
                'hints' => [
                    [
                        'level' => 'A1',
                        'intent_label' => 'Chào và hỏi xem chiếc áo này có size M không',
                        'vietnamese_intent' => 'Chào và hỏi xem chiếc áo này có size M không',
                        'sentence_frame' => 'Excuse me, do you have this in medium size?',
                        'keywords_data' => [
                            ['word' => 'size', 'ipa' => '/saɪz/', 'meaning' => 'kích cỡ', 'audio' => '/audio/keywords/size.mp3'],
                            ['word' => 'medium', 'ipa' => '/ˈmiː.di.əm/', 'meaning' => 'size vừa (M)', 'audio' => '/audio/keywords/medium.mp3'],
                            ['word' => 'excuse me', 'ipa' => '/ɪkˈskjuːz miː/', 'meaning' => 'xin lỗi / làm phiền', 'audio' => '/audio/keywords/excuse_me.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'size', 'ipa' => '/saɪz/', 'meaning' => 'kích cỡ', 'audio' => '/audio/keywords/size.mp3'],
                            ['word' => 'medium', 'ipa' => '/ˈmiː.di.əm/', 'meaning' => 'size vừa (M)', 'audio' => '/audio/keywords/medium.mp3'],
                            ['word' => 'excuse me', 'ipa' => '/ɪkˈskjuːz miː/', 'meaning' => 'xin lỗi / làm phiền', 'audio' => '/audio/keywords/excuse_me.mp3'],
                        ],
                        'sort_order' => 1,
                        'step_order' => 1,
                    ],
                    [
                        'level' => 'A2',
                        'intent_label' => 'Hỏi vị trí phòng thử đồ để mặc thử chiếc áo',
                        'vietnamese_intent' => 'Hỏi vị trí phòng thử đồ để mặc thử chiếc áo',
                        'sentence_frame' => 'Could I try this on? Where is the fitting room?',
                        'keywords_data' => [
                            ['word' => 'try on', 'ipa' => '/traɪ ɒn/', 'meaning' => 'mặc thử đồ', 'audio' => '/audio/keywords/try_on.mp3'],
                            ['word' => 'fitting room', 'ipa' => '/ˈfɪt.ɪŋ ruːm/', 'meaning' => 'phòng thử đồ', 'audio' => '/audio/keywords/fitting_room.mp3'],
                            ['word' => 'jacket', 'ipa' => '/ˈdʒæk.ɪt/', 'meaning' => 'áo khoác', 'audio' => '/audio/keywords/jacket.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'try on', 'ipa' => '/traɪ ɒn/', 'meaning' => 'mặc thử đồ', 'audio' => '/audio/keywords/try_on.mp3'],
                            ['word' => 'fitting room', 'ipa' => '/ˈfɪt.ɪŋ ruːm/', 'meaning' => 'phòng thử đồ', 'audio' => '/audio/keywords/fitting_room.mp3'],
                            ['word' => 'jacket', 'ipa' => '/ˈdʒæk.ɪt/', 'meaning' => 'áo khoác', 'audio' => '/audio/keywords/jacket.mp3'],
                        ],
                        'sort_order' => 2,
                        'step_order' => 2,
                    ],
                    [
                        'level' => 'B1',
                        'intent_label' => 'Hỏi xem còn màu sắc khác hoặc còn hàng trong kho không',
                        'vietnamese_intent' => 'Hỏi xem còn màu sắc khác hoặc còn hàng trong kho không',
                        'sentence_frame' => 'Is this color out of stock, or could you check if there is another one in the back storage?',
                        'keywords_data' => [
                            ['word' => 'out of stock', 'ipa' => '/aʊt əv stɒk/', 'meaning' => 'hết hàng trên kệ', 'audio' => '/audio/keywords/out_of_stock.mp3'],
                            ['word' => 'back storage', 'ipa' => '/bæk ˈstɔːr.ɪdʒ/', 'meaning' => 'kho phía sau', 'audio' => '/audio/keywords/back_storage.mp3'],
                            ['word' => 'in stock', 'ipa' => '/ɪn stɒk/', 'meaning' => 'còn hàng', 'audio' => '/audio/keywords/in_stock.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'out of stock', 'ipa' => '/aʊt əv stɒk/', 'meaning' => 'hết hàng trên kệ', 'audio' => '/audio/keywords/out_of_stock.mp3'],
                            ['word' => 'back storage', 'ipa' => '/bæk ˈstɔːr.ɪdʒ/', 'meaning' => 'kho phía sau', 'audio' => '/audio/keywords/back_storage.mp3'],
                            ['word' => 'in stock', 'ipa' => '/ɪn stɒk/', 'meaning' => 'còn hàng', 'audio' => '/audio/keywords/in_stock.mp3'],
                        ],
                        'sort_order' => 3,
                        'step_order' => 3,
                    ],
                    [
                        'level' => 'B2',
                        'intent_label' => 'Xử lý tình huống hết size: chấp nhận chờ kiểm tra kho hoặc xem mẫu tương tự có giảm giá 15%',
                        'vietnamese_intent' => 'Xử lý tình huống hết size: chấp nhận chờ kiểm tra kho hoặc xem mẫu tương tự có giảm giá 15%',
                        'sentence_frame' => "If checking the back storage doesn't take too long, please do! Otherwise, I'd love to see the similar jacket with the member discount.",
                        'keywords_data' => [
                            ['word' => 'member discount', 'ipa' => '/ˈmɛm.bər ˈdɪs.kaʊnt/', 'meaning' => 'chiết khấu hội viên', 'audio' => '/audio/keywords/discount.mp3'],
                            ['word' => 'similar item', 'ipa' => '/ˈsɪm.ə.lər ˈaɪ.təm/', 'meaning' => 'mẫu tương tự', 'audio' => '/audio/keywords/similar.mp3'],
                            ['word' => 'check storage', 'ipa' => '/tʃɛk ˈstɔːr.ɪdʒ/', 'meaning' => 'kiểm tra kho', 'audio' => '/audio/keywords/check_storage.mp3'],
                        ],
                        'keyword_bank' => [
                            ['word' => 'member discount', 'ipa' => '/ˈmɛm.bər ˈdɪs.kaʊnt/', 'meaning' => 'chiết khấu hội viên', 'audio' => '/audio/keywords/discount.mp3'],
                            ['word' => 'similar item', 'ipa' => '/ˈsɪm.ə.lər ˈaɪ.təm/', 'meaning' => 'mẫu tương tự', 'audio' => '/audio/keywords/similar.mp3'],
                            ['word' => 'check storage', 'ipa' => '/tʃɛk ˈstɔːr.ɪdʒ/', 'meaning' => 'kiểm tra kho', 'audio' => '/audio/keywords/check_storage.mp3'],
                        ],
                        'sort_order' => 4,
                        'step_order' => 4,
                    ],
                ],
            ],
        ];

        foreach ($scenariosData as $item) {
            $scenario = Scenario::updateOrCreate(
                ['slug' => $item['scenario']['slug']],
                $item['scenario']
            );

            // Seed characters (Personas: 2-3 per scenario)
            foreach ($item['characters'] as $charData) {
                Character::updateOrCreate(
                    [
                        'scenario_id' => $scenario->id,
                        'name' => $charData['name'],
                    ],
                    array_merge($charData, ['scenario_id' => $scenario->id])
                );
            }

            // Clean previous hints for this scenario to ensure fresh order
            ScenarioHint::where('scenario_id', $scenario->id)->delete();

            // Seed hints
            foreach ($item['hints'] as $hintData) {
                ScenarioHint::create(
                    array_merge($hintData, ['scenario_id' => $scenario->id])
                );
            }
        }
    }
}
