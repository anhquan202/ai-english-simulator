# Project Research & Context

> **Ghi chú tự động:** File này lưu trữ toàn bộ các mục tiêu, quyết định đã chốt, phương án đã bị loại bỏ và các câu hỏi mở. Bất kỳ khi nào một vấn đề mới được thống nhất, file này sẽ được cập nhật tự động.

---

## 1. Mục tiêu & Phạm vi (Core Goals)
* **Tên dự án:** AI-Powered Real-World English Communication Simulator.
* **Mục tiêu cốt lõi:** Xây dựng web app MVP giúp người học tiếng Anh ở trình độ sơ cấp (A1–A2) rèn luyện phản xạ **tự sản sinh ngôn ngữ (Speech Generation)** thông qua trò chuyện giọng nói 2 chiều với nhân vật AI trong các tình huống thực tế đời sống.
* **Triết lý trải nghiệm:** Tạo dựng một "không gian an toàn, ấm áp, không phán xét, không áp lực thi cử". Loại bỏ hoàn toàn các thuật ngữ và cơ chế gò bó như "bắt buộc", "mục tiêu tối thượng", "hình phạt".
* **Phạm vi MVP (Lean Scope):** 
  - 3 Kịch bản Hỗn hợp thực tế: Quán cà phê, Sân bay, Khách sạn.
  - Cửa sổ suy nghĩ thư thả 10–15s + Nút chủ động gửi.
  - Giàn giáo gợi ý Khung câu rỗng + Từ khóa có nút loa nghe mẫu [🔊].
  - Chỉ số CSI thích ứng & Đánh giá ngữ âm phân tầng mang tính xây dựng.
  - Luyện lại theo Lát cắt hội thoại 3 bước; lưu tiến độ vào cơ sở dữ liệu MySQL theo từng `user_id`.

---

## 2. Các quyết định đã chốt (Decisions Made)

### 2.1. Kiến Trúc Xử Lý Ảnh Bất Đồng Bộ (Async Image Processing Pipeline) - [MỚI CHỐT]
* **Mô hình hàng đợi (Queue-driven):** Mọi tác vụ upload ảnh hoặc lệnh import batch hàng loạt của Admin đều được đẩy vào background qua `ProcessScenarioImageJob` (implements `ShouldQueue`). Tuyệt đối không xử lý ảnh đồng bộ trong main HTTP request hay CLI thread để chống tràn RAM và timeout.
* **Thư viện chuẩn hóa:** Sử dụng **Intervention Image v3** (`intervention/image`).
* **Chuẩn hóa tối ưu:**
  - Tự động convert 100% ảnh sang định dạng **`.webp`** (nén giảm 70–80% dung lượng, tải trang siêu nhanh).
  - Tự động resize: `cover_image` theo chuẩn 16:9 (`800x450px`), `avatar_path` theo chuẩn 1:1 (`256x256px`).
* **Queue Driver:** Giai đoạn phát triển dùng `QUEUE_CONNECTION=database` (chạy trên bảng `jobs` của MySQL, không cần cài Redis); khi lên Production chuyển sang `redis`.

### 2.2. Chiến Lược Lưu Trữ File (File & Asset Storage Strategy)
* **Giai đoạn Phát triển (Development):** Sử dụng **Laravel Local Public Disk** (`storage/app/public` qua `php artisan storage:link`). Lưu trực tiếp ảnh bìa kịch bản, avatar nhân vật và âm thanh từ khóa trên máy nội bộ; 0đ chi phí, 0ms cấu hình cloud.
* **Giai đoạn Triển khai (Production):** Sử dụng **Cloudflare R2** (S3-Compatible Object Storage) thông qua driver `s3` có sẵn của Laravel Flysystem:
  - Miễn phí vĩnh viễn 10GB lưu trữ mỗi tháng.
  - **0đ phí băng thông tải ra (Zero Egress Fees):** Không bị tính tiền khi người dùng tải ảnh và nghe file audio liên tục.

### 2.3. Bố Cục UI/UX Màn Hình 1 - Trang Chủ (Screen 1 Dashboard)
* **Header Tinh Gọn:** Dọn sạch Header, chỉ giữ Logo và Avatar / Nút Đăng nhập. Bộ chọn cấp độ được chuyển xuống thanh Toolbar bên dưới.
* **Thanh Công Cụ Lọc & Sắp Xếp (Toolbar):** Đặt ngay trên danh sách Card, gồm: Lọc theo Thể loại, Lọc theo Cấp độ (`Tất cả`, `A2`, `B1`, `B2`) và Sắp xếp (`Ưu tiên bài đã ghim`, `Mới nhất`).
* **Section 2 (Hero Spotlight Thông Minh):**
  - *Nếu chưa ghim bài nào:* Hiển thị bài gợi ý ngẫu nhiên mặc định (Daily Pick) theo level của user.
  - *Nếu đã ghim $\ge 1$ bài:* Section 2 tự động hiển thị **Bài được ghim mới nhất** để người học bấm vào luyện ngay lập tức.
* **Section 3 (Danh mục kịch bản):**
  - Các bài được ghim (`[❤️]`) luôn được ưu tiên tự động sắp xếp lên vị trí đầu tiên.
* **Hiệu Ứng Hover Peek (Flyout Preview Panel):**
  - Khi rê chuột vào Story Card, một bảng Preview mỏng xuất hiện mượt mà bên cạnh hiển thị: Mô tả bối cảnh chi tiết (`detailed_description`), cấp độ, thời lượng, danh sách nhân vật và nút hành động `[🚀 Vào luyện tập ngay]`.
* **Guest Mode:** Người dùng chưa đăng nhập vẫn trải nghiệm bình thường; ghim bài lưu vào `localStorage`, đồng bộ vào DB khi đăng ký.

### 2.4. Kiến Trúc Backend: MVC + Repository + Interface Pattern
* **Mô hình kiến trúc bắt buộc:**
  - `Controller`: Mỏng (Slim), điều phối request/response.
  - `Contracts (Interfaces)`: `ScenarioRepositoryInterface`, `PracticeSessionRepositoryInterface`, `DialogueTurnRepositoryInterface`, `AiSimulatorServiceInterface`.
  - `Eloquent Repositories`: Thực thi truy vấn cơ sở dữ liệu cụ thể.
  - `Services`: Đóng gói logic nghiệp vụ AI (`GeminiSimulatorService`).
  - `RepositoryServiceProvider`: Đăng ký IoC bindings.

### 2.5. Công nghệ & Hạ tầng (Tech Stack Architecture)
* **Backend Framework:** **Laravel (PHP)** - Chi tiết mã nguồn mẫu xem tại: [DATABASE_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/DATABASE_SPECIFICATION.md).
* **Database Engine:** **MySQL 8.0** - Bảng quan hệ kết hợp cột `JSON` tối ưu cho phân tích từ vựng và cấp độ. Bổ sung `detailed_description` và bảng `user_favorites`.
* **Frontend Reactive UI:** **Blade Templates + Alpine.js + Tailwind CSS** - Gọn nhẹ, phản ứng nhanh (reactive), điều khiển trực tiếp Microphone, bộ đếm thời gian thực và Canvas/SVG Audio Visualizer. Chi tiết xem tại: [FRONTEND_ALPINE_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/FRONTEND_ALPINE_SPECIFICATION.md).
* **AI Model Engine:** **Google Gemini API (Gemini 2.5 Flash)** - Gọi qua `AiSimulatorServiceInterface` với chế độ `response_mime_type: 'application/json'`. Chi tiết xem tại: [PROMPT_AND_AI_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/PROMPT_AND_AI_SPECIFICATION.md).
* **Voice In (STT):** **Web Speech API** tích hợp trực tiếp trong Alpine.js. Chạy tại trình duyệt client, độ trễ 0ms, chi phí 0đ, không cần token.
* **Voice Out (TTS):** Giai đoạn ban đầu dùng **Web Speech Synthesis** có sẵn trên trình duyệt; nâng cấp sau đó qua **Edge-TTS** (miễn phí, giọng Microsoft Neural).

### 2.6. Thiết Kế Cơ Sở Dữ Liệu & Cấu Hình
* **Tách riêng Cấu hình Chấm điểm (`config/simulator.php`):** Toàn bộ trọng số CSI (Resolution 40%, Adaptability 35%, Flow 25%) và hệ số phạt lát cắt được tách riêng vào file cấu hình.
* **Mô hình 7 bảng chuẩn hóa:** `users`, `scenarios`, `characters`, `scenario_hints`, `user_favorites`, `practice_sessions`, `dialogue_turns`.

### 2.7. Sư phạm & Tương tác Hội thoại
* **Giàn giáo chống học vẹt (`BR-SCA`):** Không đưa câu mẫu hoàn chỉnh; cung cấp Ý định + Khung câu rỗng + Từ khóa có nút loa nghe mẫu [🔊].
* **Cửa sổ suy nghĩ 10–15 giây (`BR-EXC-01`):** Hệ thống kiên nhẫn lắng nghe, không tự ý ngắt mic khi người học ngập ngừng. Người học chủ động bấm `[Xong / Gửi]` để AI phản hồi tức thì ($\le 2.5s$).
* **Nói lại (Redo) An toàn & Chống Spam (`BR-EXC-02`):** Bấm `[Nói lại]` chỉ xóa bộ nhớ đệm chữ (Buffer), không re-init thiết bị phần cứng; có nhịp đệm chuẩn bị 1.5s để chống spam request và tiết kiệm tài nguyên AI (tối đa 2 lần/lượt).
### 2.8. Bố Cục UI/UX 4 Màn Hình Cốt Lõi (`UI_UX_SPECIFICATION.md`)
* **Màn hình 1 - Trang Chủ (Home Dashboard):** Hero Spotlight thông minh, Thanh Toolbar lọc cấp độ/thể loại, Thẻ bài học kèm Hover Peek Flyout Preview.
* **Màn hình 2 - Chi Tiết Kịch Bản & Chuẩn Bị Thiết Bị (Pre-Flight Lobby):**
  - Section 1: Breadcrumb phân cấp trực quan.
  - Section 2: Story recap với mô tả bối cảnh chi tiết (`detailed_description`).
  - Section 3 (7 phần bên trái): Chọn Cấp độ (`A2`/`B1`/`B2`) & Nhân vật AI (`Emma`/`Jack`) kèm hộp hiển thị chi tiết tính cách, phong cách giọng nói, nghe thử giọng chào.
  - Section 4 (5 phần bên phải): Kiểm tra thiết bị:
    * **Check Microphone (Bắt buộc):** Đo Web Audio API FFT qua 3 trạng thái: ⚪ *Im lặng*, 🟠 *Yếu/Tạp âm*, 🟢 *Đủ điều kiện giao tiếp* ($\text{RMS} \ge 0.10$ & dải tần $300-3000\text{Hz}$).
    * **Camera soi khẩu hình (Tùy chọn):** Mặc định tắt, chạy 100% nội bộ trên trình duyệt để soi môi, răng, lưỡi, không gửi frame nào lên mạng.
  - Section 5: Badge trạng thái thiết bị & Nút `[🚀 Bắt đầu luyện tập]` (chỉ sáng khi Mic đạt trạng thái 3).
* **Màn hình 3 - Phòng Luyện Thoại Video Call (`UI_UX_SPECIFICATION.md`):**
  - Trải nghiệm Video Call 1-1 kiểu Google Meet/Zoom: Nhân vật AI lớn trung tâm với vòng hào quang sóng âm (Audio Visualizer Pulse qua Web Audio `AnalyserNode`).
  - Góc Camera người học (PIP Box): Gương soi khẩu hình miệng ở góc trên bên phải (100% xử lý tại trình duyệt).
  - Phụ đề Live Subtitle (CC): Mặc định TẮT để bảo vệ phản xạ nghe hiểu, chỉ hiện khi bật nút `[CC]`.
  - Thanh Dock nổi (Floating Meeting Dock): Nút `[💡 Tips]` phát sáng vòng vàng sau 8s im lặng, nút `[💬 Script]` có badge thông báo, nút `[CC]`, nút `[🎤 Mic]` trung tâm, nút `[✓ Send]`, nút `[↺ Undo]` đệm 1.5s.
  - Khay trượt bên phải (Slide-over Drawer): Mở on-demand chứa 2 tab: Giàn giáo gợi ý (`BR-SCA`) và Lịch sử hội thoại đầy đủ ghi ngầm.
* **Màn hình 4 - Tổng Kết & Phân Tích Lỗi Tương Tác (Summary & Turn Inspector):**
  - Thẻ điểm CSI minh bạch 3 trụ cột (40% Resolution, 35% Adaptability, 25% Flow) kèm lời khích lệ tâm lý ấm áp.
  - Phân tích từng từ trong lời nói bằng mã màu: 🟩 Xanh (chuẩn), 🟧 Cam (tạm ổn), 🟥 Đỏ (cần sửa).
  - Micro-Popover sửa lỗi tức thì khi chạm vào từ Đỏ/Cam: Xem phiên âm IPA, nghe phát âm mẫu `[🔊]`, thu âm đọc thử lại `[🎤]` ngay tại chỗ trong 3 giây.
  - Hero CTA: Luyện lại Lát cắt yếu nhất (3-Step Slice Re-practice) giúp người học kết thúc buổi học trong cảm giác thành công.

### 2.9. Hệ Thống Thẩm Mỹ & Design Tokens Chống "AI Slop" (Hybrid Aesthetic System)
* **Tầng Ngoài (Màn 1, 2, 4) - Warm Humanist:** Nền giấy kem ấm (`#FAF8F5`), chữ than củi (`#1C1917`), viền đá tự nhiên mỏng (`stone-200`), điểm nhấn cam đất nung (`#EA580C`) / vàng hổ phách (`#D97706`). Tiêu đề font Serif (*Fraunces* / *Newsreader*), nội dung Sans-serif (*Plus Jakarta Sans* / *Inter*).
* **Tầng Trong (Màn 3) - Modern Studio Dark:** Xám than đá Slate trầm ấm (`bg-slate-900` / `#0F172A`), thanh Dock kính mờ (`backdrop-blur-xl bg-slate-900/75 border border-white/10`), điểm nhấn Emerald (`#10B981`) và Cyan (`#06B6D4`).
* **Triệt tiêu hoàn toàn cảm giác rẻ tiền, mang lại sự chữa lành, ấm áp và tập trung cao độ.**

---

## 3. Các phương án ĐÃ BỊ LOẠI BỎ & Lý do (Rejected Alternatives)
*(Mục này cực kỳ quan trọng để không bao giờ đề xuất lại các phương án đã bị từ chối)*

* ❌ **Loại bỏ: Phong cách giao diện "AI Slop" (Gradient tím-cyan neon, nền đen kịt #000000, icon robot 3D vô hồn)**
  - *Lý do:* Tạo cảm giác sản phẩm công nghệ dựng sẵn rẻ tiền, phá vỡ triết lý "an toàn, ấm áp và không phán xét", gây nhức mắt và mệt mỏi cho người học. Đã chuyển sang chuẩn Hybrid Aesthetic System (Warm Humanist + Modern Studio).
* ❌ **Loại bỏ: Bật mặc định phụ đề (Auto-on Subtitles/CC) trong lúc AI đang nói**
  - *Lý do:* Hiện tượng thị giác lấn át thính giác (Visual Dominance) khiến người học chỉ chăm chăm đọc chữ, triệt tiêu phản xạ nghe hiểu. Đã chuyển thành mặc định TẮT, chỉ mở khi người học chủ động bấm nút `[CC]`.
* ❌ **Loại bỏ: Bày toàn bộ dòng chat lịch sử và khung gợi ý to đùng choán chỗ trên màn hình chính**
  - *Lý do:* Làm rối mắt và biến app thành giao diện bài tập trắc nghiệm khô cứng. Đã chuyển sang ghi ngầm toàn bộ script và giấu vào khay trượt (Slide-over Drawer) mở theo yêu cầu qua Thanh Dock nổi.
* ❌ **Loại bỏ: Đồng hồ đếm ngược báo động giật gân (Aggressive Red Countdown Timer) ở phòng luyện thoại**
  - *Lý do:* Gây hoảng loạn và ức chế tâm lý cho người học A1–A2. Thay bằng thanh tiến trình suy nghĩ mềm mại, ấm áp.
* ❌ **Loại bỏ: Pop-up chen ngang sửa lỗi ngữ pháp/phát âm ngay khi đang nói chuyện**
  - *Lý do:* Phá nát dòng chảy hội thoại tự nhiên (Flow). Toàn bộ phản hồi sửa lỗi được gom về Màn hình 3 (Tổng kết).
* ❌ **Loại bỏ: Bắt buộc người học phải luyện lại toàn bộ bài từ đầu khi chỉ sai 1 câu**
  - *Lý do:* Làm nản lòng người học. Thay bằng tính năng "Luyện lại Lát cắt yếu nhất 3 bước" (Slice Re-practice) chỉ mất 1 phút.
* ❌ **Loại bỏ: Xử lý nén/resize ảnh trực tiếp trong luồng đồng bộ (Synchronous Processing)**
  - *Lý do:* Làm chậm request, dễ gây lỗi timeout 504 và tràn RAM máy chủ khi upload nhiều ảnh hoặc chạy batch. Đã áp dụng chuẩn Background Jobs qua Laravel Queue.
* ❌ **Loại bỏ: Dùng AWS S3 trực tiếp ngay từ giai đoạn phát triển ban đầu**
  - *Lý do:* AWS S3 tính tiền băng thông tải ra (Egress fees) và bắt buộc khai báo thẻ tín dụng. Ở giai đoạn phát triển, dùng Laravel Local Storage; khi lên Production dùng Cloudflare R2 (S3-compatible) có 10GB miễn phí vĩnh viễn và 0đ tiền băng thông.
* ❌ **Loại bỏ: Dùng các dịch vụ gắn chặt SDK như Cloudinary hay Supabase Storage riêng lẻ**
  - *Lý do:* Gây phụ thuộc vendor (Vendor lock-in). Sử dụng chuẩn S3 API / Laravel Flysystem để linh hoạt đổi hạ tầng bất cứ lúc nào qua file `.env`.
* ❌ **Loại bỏ: Đặt bộ lọc Level trên Header chính**
  - *Lý do:* Làm rối Header, tách rời khỏi danh sách bài học. Đã chuyển xuống thanh Toolbar ngay trên danh mục Card.
* ❌ **Loại bỏ: Bắt buộc đăng nhập ngay từ đầu mới cho vào học (Forced Login Wall)**
  - *Lý do:* Tạo rào cản quá lớn cho người dùng mới. Áp dụng chuẩn Guest Mode (PLG) cho phép bấm vào học ngay.
* ❌ **Loại bỏ: Bắt buộc người dùng phải click vào trang mới để đọc mô tả bối cảnh**
  - *Lý do:* Làm gián đoạn trải nghiệm duyệt bài. Thay bằng hiệu ứng Hover Peek hiển thị bản xem trước chi tiết ngay bên cạnh card.
* ❌ **Loại bỏ: Hiển thị danh sách dài vô tận mà không có Hero Pick ở Trang chủ**
  - *Lý do:* Gây ra hội chứng tê liệt lựa chọn (Paradox of Choice). Luôn có 1 Section Hero nổi bật (ưu tiên bài ghim mới nhất).
* ❌ **Loại bỏ: Viết Eloquent queries trực tiếp trong Controllers**
  - *Lý do:* Làm Controller phình to, khó viết unit test. Đã áp dụng chuẩn Repository Pattern + Interface.
* ❌ **Loại bỏ: Dựng Frontend SPA phức tạp bằng Next.js / Nuxt.js / Vue SPA**
  - *Lý do:* Tối ưu tốc độ triển khai cá nhân, giữ toàn bộ logic và bảo mật API key gọn gàng trong hệ sinh thái MVC quen thuộc.
* ❌ **Loại bỏ: Dùng thư viện 3D WebGL (Three.js/Babylon) hoặc Video Deepfake cho Avatar trong MVP**
  - *Lý do:* Quá nặng trình duyệt. Thay thế bằng Avatar 2D kèm sóng âm dao động bằng Web Audio API cực nhẹ và mượt.
* ❌ **Loại bỏ: Mớm nguyên câu tiếng Anh mẫu hoàn chỉnh trong gợi ý (Full scripts / Cheat-sheet)**
  - *Lý do:* Biến ứng dụng thành app đọc vẹt, triệt tiêu tư duy tự lắp ráp câu của não bộ.

---

## 4. Trạng Thái Hiện Tại & Đã Hoàn Thành (Status & Milestones)

* ✅ **Spike 1 (Kiến trúc & Database MVC + Repository):** Đã hoàn tất (`DATABASE_SPECIFICATION.md`).
* ✅ **Spike 2 (Prompt Engineering & Dual-Channel AI Engine):** Đã hoàn tất (`PROMPT_AND_AI_SPECIFICATION.md`).
* ✅ **Spike 3 (Client Audio & Alpine.js State Machine):** Đã hoàn tất (`FRONTEND_ALPINE_SPECIFICATION.md`).
* ✅ **Spike 4 (Bố Cục UI/UX & Wireframes 4 Màn Hình Cốt Lõi):** Đã hoàn tất (`UI_UX_SPECIFICATION.md`).
* ✅ **Giai đoạn 1 (Hạ tầng Docker & Khung Laravel 11):** 4 container running, Nginx port 8002, MySQL port 3309, cấu hình tách biệt `config/simulator.php`.
* ✅ **Giai đoạn 2 (Database Migrations, Models & Seeders):** 7 bảng, 7 models, 3 kịch bản hybrid nạp sẵn 9 giàn giáo gợi ý có IPA.
* ✅ **Giai đoạn 3 (Repository Pattern & Gemini AI Service):** 4 interfaces, Dual-channel JSON mode, Graceful fallback, Background WebP Job qua Queue.
* ✅ **Giai đoạn 4 (Controllers & API Endpoints):** 5 slim controllers, routing RESTful, CSRF whitelist cho API Alpine.
* ✅ **Giai đoạn 5 (Frontend Blade Views & Alpine.js Web Audio):** Chuẩn Hybrid Aesthetic System (Anti-AI-Slop), 4 màn hình hoàn thiện, Vite build production thành công.
* 🚀 **Trạng thái vận hành:** Hệ thống đang chạy trực tiếp tại `http://localhost:8002`. Báo cáo tổng kết xem tại `walkthrough.md`.
