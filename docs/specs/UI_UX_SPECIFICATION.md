# ĐẶC TẢ GIAO DIỆN & TRẢI NGHIỆM NGƯỜI DÙNG (UI/UX SPECIFICATION)
## (CORE SCREENS & INTERACTION DESIGN SPECIFICATION)

* **Dự án:** AI-Powered Real-World English Communication Simulator
* **Mã tài liệu:** UIUX-v1.1 (Home Dashboard, Pre-Flight Lobby, Simulator Room, Summary & Turn Inspector)
* **Tài liệu tham chiếu:** [BUSINESS_RULES.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/BUSINESS_RULES.md) | [DATABASE_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/DATABASE_SPECIFICATION.md) | [FRONTEND_ALPINE_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/FRONTEND_ALPINE_SPECIFICATION.md)
* **Trạng thái:** Active / Baseline

---

## 1. NGUYÊN TẮC THIẾT KẾ TRẢI NGHIỆM (CORE UX PHILOSOPHY)

1. **Tâm Lý Học Không Phán Xét (Safe & Non-Judgmental Space):**
   - Không dùng màu sắc cảnh báo giật gân (như đỏ rực cảnh báo nguy hiểm trong lúc nói).
   - Không hiển thị đồng hồ đếm ngược gấp gáp khiến người học hoảng loạn. Cửa sổ suy nghĩ 10–15s được biểu diễn bằng thanh tiến trình mềm mại.
   - Không áp đặt các khái niệm nặng nề ("Mục tiêu tối thượng", "Bắt buộc", "Hình phạt"). Mọi gợi ý đều mang tính đồng hành.
2. **Product-Led Growth (PLG) & Zero Barrier:**
   - Cho phép khách vãng lai (Guest Mode) vào học ngay chỉ với 1 cú click. Lưu trạng thái yêu thích vào `localStorage` và đồng bộ khi đăng ký.
3. **Phòng Chuẩn Bị Thiết Bị Không Gây Hụt Hẫng (Pre-Flight Safety Net):**
   - Kiểm tra kỹ lưỡng tín hiệu microphone trước khi bước vào phòng luyện, ngăn ngừa 100% trường hợp người học nói mà AI không nhận diện được do mic rè hoặc chưa cấp quyền.
4. **Giàn Giáo Đỡ Bước Nhẹ Nhàng (Anti-Parrot Scaffolding):**
   - Chỉ gợi ý: Ý đồ tiếng Việt $\rightarrow$ Khung câu rỗng `[...]` $\rightarrow$ Ngân hàng từ khóa kèm nút phát âm mẫu `[🔊]`. Tuyệt đối không mớm nguyên câu hoàn chỉnh để bảo vệ phản xạ tự tạo câu.
5. **Tối Ưu Phản Hồi Âm Thanh & Thị Giác (Multi-Sensory Feedback):**
   - Avatar tương tác 2D có vòng hào quang sóng âm (Audio visualizer) mấp máy tự nhiên theo biên độ âm lượng giọng nói qua Web Audio API.

### 1.1. Hệ Thống Thẩm Mỹ & Design Tokens Chống "AI Slop" (Hybrid Aesthetic System)

Nhằm triệt tiêu 100% cảm giác "AI Slop" (những dải màu tím-cyan neon sáo rỗng, hiệu ứng bóng lộn rẻ tiền và giao diện vô hồn), toàn bộ ứng dụng tuân thủ mô hình **Hybrid Aesthetic System**:

* **Tầng Ngoài (Màn 1 Trang chủ, Màn 2 Chuẩn bị, Màn 4 Tổng kết) ──► Warm Humanist Style:**
  - **Triết lý:** Ấm áp, chữa lành, mộc mạc như không gian một quán cà phê rang xay bằng gỗ và giấy mộc.
  - **Nền chính (Canvas):** Kem ấm nhạt (`bg-[#FAF8F5]` / `bg-stone-50`), không dùng trắng chói `#FFFFFF`.
  - **Màu chữ (Typography):** Màu than củi mun (`text-stone-900` / `#1C1917`), tương phản mềm êm mắt.
  - **Đường viền & Khối thẻ:** Viền đá tự nhiên mỏng (`border-stone-200`), bóng đổ mềm (`shadow-sm`).
  - **Điểm nhấn (Accents):** Cam đất nung (Terracotta `#EA580C`), Vàng hổ phách ấm (`#D97706`).
  - **Cặp Font:** Tiêu đề dùng Serif thanh lịch (*Fraunces* / *Newsreader*) + Nội dung dùng Sans-serif thân thiện (*Plus Jakarta Sans* / *Inter*).

* **Tầng Trong (Màn 3 Phòng Luyện Video Call) ──► Modern Studio Dark Style:**
  - **Triết lý:** Tối giản cao cấp kiểu Google Meet / Apple FaceTime, làm nền tĩnh lặng để tôn vinh tối đa khuôn mặt nhân vật AI và cửa sổ camera người học.
  - **Nền chính:** Xám than đá Slate trầm ấm (`bg-slate-900` / `#0F172A` & `bg-slate-950`), không dùng đen tuyền `#000000`.
  - **Thanh Dock điều khiển:** Kính mờ siêu thực (`backdrop-blur-xl bg-slate-900/75 border border-white/10`).
  - **Điểm nhấn:** Xanh ngọc lục bảo (`#10B981` Emerald) cho trạng thái mic chuẩn và Cyan dịu (`#06B6D4`) cho sóng âm AI.

* **3 Nguyên Tắc Bất Di Bất Dịch Chống "AI Slop" (Anti-AI-Slop Rules):**
  1. ❌ **Cấm:** Dải màu gradient tím-cyan vô tội vạ (`linear-gradient(purple, cyan)`).
  2. ❌ **Cấm:** Nền đen kịt `#000000` tạo cảm giác lạnh lẽo, hầm hố.
  3. ❌ **Cấm:** Các icon robot, vi mạch, não bộ 3D trừu tượng; toàn bộ icon phải mang tính đời thường gần gũi.

---

## 2. MÀN HÌNH 1: TRANG CHỦ & DANH MỤC KỊCH BẢN (HOME DASHBOARD)

### 2.1. Kiến Trúc Bố Cục (Layout Architecture)
* **Header tinh gọn:** Logo thương hiệu bên trái, Avatar profile / Nút Đăng nhập bên phải.
* **Section 2 (Hero Spotlight):** Thẻ gợi ý tình huống nổi bật hôm nay (ưu tiên bài được người dùng ghim `[❤️]` mới nhất, hoặc bài Daily Pick của hệ thống). Giúp người học vào luyện ngay trong 3 giây mà không bị tê liệt lựa chọn.
* **Thanh công cụ Toolbar (Lọc & Sắp xếp):** Đặt ngay trên lưới danh sách bài học:
  * Lọc Cấp độ: `[Tất cả]` `[A2 - Cơ bản]` `[B1 - Tự tin]` `[B2 - Nâng cao]`
  * Lọc Danh mục: `[Tất cả]` `[Đời sống]` `[Du lịch]` `[Công sở]`
  * Sắp xếp: Dropdown `[Ưu tiên bài ghim]` | `[Mới nhất]` | `[Thời lượng ngắn]`
* **Section 3 (Card Grid & Hover Peek):**
  * Lưới thẻ kịch bản: Ảnh cover WebP (16:9), Huy hiệu phân loại, Tên tình huống, Thời lượng ước tính (`⏱️ 3-5 phút`), Nút ghim yêu thích `[❤️]`.
  * **Hiệu ứng Hover Peek:** Khi rê chuột vào thẻ, một bảng chi tiết (Flyout Preview) mềm mại xuất hiện bên cạnh hiển thị: Mô tả bối cảnh chi tiết (`detailed_description`), Tên & chân dung nhân vật AI đối thoại, Danh sách kỹ năng rèn luyện, và nút to rõ `[Vào phòng luyện ngay →]`.

### 2.2. Wireframe Màn Hình 1 (Desktop & Mobile)

```
======================================================================================
[LOGO] AI English Simulator                                     [🔔] [Avatar / Login]
======================================================================================
--- SECTION 2: HERO SPOTLIGHT (BÀI HỌC NỔI BẬT / BÀI ĐÃ GHIM MỚI NHẤT) ----------------
+------------------------------------------------------------------------------------+
| [ Ảnh Cover 16:9 ]   ❤️ ĐÃ GHIM • CẤP ĐỘ A2 • ⏱️ 3-5 PHÚT                           |
|                      Gọi Cà Phê Tại Quán Local Roastery                            |
|                      "Bạn vừa bước vào quán cà phê thơm nức mùi hạt rang. Barista  |
|                      Emma đang mỉm cười chuẩn bị đón tiếp bạn..."                  |
|                                                                                    |
|                      Nhân vật: 👩 Emma (Barista thân thiện)                        |
|                      [ BẮT ĐẦU LUYỆN NGAY VỚI EMMA → ]     [ Bỏ ghim / Thay đổi ]  |
+------------------------------------------------------------------------------------+

--- THANH CÔNG CỤ TOOLBAR (BỘ LỌC CẤP ĐỘ & DANH MỤC TRÊN CARD GRID) -------------------
[ Cấp độ: (•) Tất cả  ( ) A2  ( ) B1  ( ) B2 ]    [ Thể loại: Tất cả v ]   [ Sort: Ưu tiên ghim v ]
--------------------------------------------------------------------------------------
--- SECTION 3: DANH SÁCH TÌNH HUỐNG (CARDS GRID + HOVER PEEK PREVIEW) ----------------
+-----------------------+ +-----------------------+ +--------------------------------+
| [ Ảnh Cover ]      ❤️ | | [ Ảnh Cover ]      🤍 | | >>> FLYOUT PREVIEW (HOVER PEEK)|
| Du lịch • A2 • ⏱️ 4p   | | Đời sống • A2 • ⏱️ 3p | | ------------------------------ |
| Check-in Khách Sạn    | | Mua Vé Tàu Điện Ngầm  | | 🏨 Check-in Khách Sạn          |
| "Nhận phòng và hỏi..."| | "Hỏi mua vé khứ hồi..."| | "Bạn đến khách sạn vào lúc     |
| [Chi tiết] [Luyện →]  | | [Chi tiết] [Luyện →]  | | trời mưa. Lễ tân Jack thông báo|
+-----------------------+ +-----------------------+ | phòng bạn đặt đang được bảo trì|
+-----------------------+ +-----------------------+ | và đề xuất đổi sang phòng..."  |
| [ Ảnh Cover ]      🤍 | | [ Ảnh Cover ]      🤍 | | 👤 Đối thoại: Jack (Lễ tân)    |
| Công sở • B1 • ⏱️ 5p   | | Đời sống • A2 • ⏱️ 4p | | 🎯 Kỹ năng: Phản xạ đổi phòng |
| Phỏng Vấn Nhận Việc   | | Trả Đổi Món Đồ Đã Mua | | [ LUYỆN TẬP VỚI JACK NGAY → ]  |
+-----------------------+ +-----------------------+ +--------------------------------+
======================================================================================
```

---

## 3. MÀN HÌNH 2: CHI TIẾT KỊCH BẢN & PHÒNG CHUẨN BỊ (PRE-FLIGHT LOBBY)

### 3.1. Kiến Trúc Bố Cục (Layout Architecture)
* **Section 1: Breadcrumb Điều Hướng:**
  * Đường dẫn phân cấp trực quan: `Trang chủ` > `[Thể loại]` > `[Tên kịch bản]`.
* **Section 2: Thông Tin Tình Huống Chi Tiết (Story Recap):**
  * Tên kịch bản, thời lượng ước tính (`⏱️ 3-5 phút`), thẻ thể loại.
  * Mô tả bối cảnh chi tiết (`detailed_description`): Câu chuyện dẫn nhập đầy đủ, mục tiêu trải nghiệm tự nhiên, gợi ý tình huống bất ngờ có thể xảy ra.
* **Section 3: Tùy Chọn Học & Nhân Vật AI (7 Phần Bên Trái - 60% Width):**
  * **Chọn Cấp độ thử thách:** Radio chips `[A2 - Cơ bản]` `[B1 - Tự tin]` `[B2 - Nâng cao]`.
  * **Chọn Nhân vật đối thoại (Persona Cards):** Lựa chọn nhân vật theo phong cách nói (ví dụ: `👩 Emma` kiên nhẫn, nói chậm vs `👨 Jack` bận rộn, phản xạ nhanh).
  * **Hộp Chi tiết Nhân vật đã chọn:** Avatar lớn, phong cách giọng điệu (Tone of voice), tốc độ nói (90% natural), và nút nghe thử giọng chào `[🔊 Nghe thử giọng]`.
* **Section 4: Kiểm Tra Thiết Bị (5 Phần Bên Phải - 40% Width):**
  * **Kiểm tra Microphone (Bắt buộc):**
    * Đồng hồ đo sóng âm thời gian thực (Level Meter) phân tích Web Audio API qua 3 trạng thái:
      1. ⚪ *Không ghi nhận âm thanh* (Silent / Muted).
      2. 🟠 *Chưa đủ tần số phát âm / Tạp âm* (Weak / Background Noise).
      3. 🟢 *Đủ điều kiện giao tiếp* (Optimal Voice - RMS $\ge 0.10$ & tần số $300 - 3000\text{Hz}$).
    * Dòng nhắc nhở tương tác: *"Nói thử câu: 'Hello Emma, testing mic' để kiểm tra..."*
  * **Camera soi khẩu hình (Tùy chọn - Không bắt buộc):**
    * Công tắc Toggle bật/tắt (Mặc định: TẮT).
    * Khi bật: Khung video gương soi khẩu hình cục bộ 100% trên trình duyệt để người học tự tin chỉnh môi, răng, lưỡi; tuyệt đối không gửi frame nào lên mạng.
* **Section 5: Badge Trạng Thái & Nút Bắt Đầu (Bottom Action Bar):**
  * Thẻ tóm tắt trạng thái: `[✓] Mic sẵn sàng  |  [✓] Cấp độ: A2  |  [✓] Đối thoại: Emma`.
  * Nút CTA `[ 🚀 BẮT ĐẦU VÀO PHÒNG LUYỆN TẬP NGAY → ]`:
    * **Khóa (Disabled):** Khi Microphone chưa đạt Trạng thái 3.
    * **Mở sáng (Active):** Ngay khi tín hiệu mic đạt chuẩn đàm thoại.

### 3.2. Wireframe Màn Hình 2 (Desktop & Mobile)

```
==================================================================================================
[LOGO] AI English Simulator                                                 [🔔] [Avatar / Profile]
==================================================================================================

SECTION 1: BREADCRUMB
Trang chủ  >  ☕ Đời sống hàng ngày  >  Gọi Cà Phê Tại Quán Local Roastery
--------------------------------------------------------------------------------------------------

SECTION 2: THÔNG TIN TÌNH HUỐNG (STORY RECAP & CONTEXT)
+------------------------------------------------------------------------------------------------+
| ☕ GỌI CÀ PHÊ TẠI QUÁN LOCAL ROASTERY                   [⏱️ 3-5 Phút]  [🏷️ Thể loại: Đời sống]  |
|                                                                                                |
| Mô tả bối cảnh chi tiết:                                                                       |
| "Bạn vừa đáp máy bay và ghé vào một quán cà phê rang xay ấm cúng tại trung tâm Melbourne.      |
| Quán đang khá đông khách vào buổi sáng. Barista phụ trách order là Emma - người luôn tươi cười |
| nhưng quán hôm nay đang tạm hết một số loại hạt cà phê đặc biệt. Hãy tự tin gọi món mình thích |
| và sẵn sàng ứng biến nếu barista đề xuất thay đổi loại hạt nhé!"                               |
+------------------------------------------------------------------------------------------------+

--- SECTION 3 & SECTION 4: BỐ CỤC CHIA CỘT (7 PHẦN TRÁI : 5 PHẦN PHẢI) ---------------------------
+---------------------------------------------+ +------------------------------------------------+
| SECTION 3: TÙY CHỌN HỌC & AI (7 PHẦN TRÁI)  | | SECTION 4: KIỂM TRA THIẾT BỊ (5 PHẦN PHẢI)     |
|                                             | |                                                |
| 1. CHỌN CẤP ĐỘ THỬ THÁCH:                   | | 🎙️ KIỂM TRA MICROPHONE (BẮT BUỘC):             |
| [•] A2 (Cơ bản)   [ ] B1 (Tự tin)  [ ] B2   | | Trạng thái: [ 🟢 ĐỦ ĐIỀU KIỆN GIAO TIẾP ]      |
| (Từ vựng quen thuộc, nói chậm, có giàn giáo)| | Sóng âm: [████████████████████░░░░] 75%        |
|                                             | | 💡 Hướng dẫn: Bạn hãy nói thử:                 |
| 2. CHỌN NHÂN VẬT AI ĐỐI THOẠI (PERSONA):    | |    "Hello Emma, testing my microphone"         |
| +---------------------+ +-----------------+ | |                                                |
| | (•) 👩 Emma (Đã chọn)| | ( ) 👨 Jack     | | | ---------------------------------------------- |
| | Barista vui vẻ,     | | Barista bận rộn,| | | 📷 CAMERA SOI KHẨU HÌNH (TÙY CHỌN):           |
| | kiên nhẫn, nói chậm | | phản xạ nhanh   | | | [ Công tắc: (•) BẬT  /  ( ) TẮT ]             |
| +---------------------+ +-----------------+ | | +--------------------------------------------+ |
|                                             | | | [ Khung Video Gương Soi Khẩu Hình 16:9 ]   | |
| 3. THÔNG TIN CHI TIẾT VỀ AI ĐÃ CHỌN:        | | |                                            | |
| +-----------------------------------------+ | | |         (Khẩu hình mặt bạn ở đây)          | |
| | 👩 EMMA - BARISTA NỘI TRỢ ĐỊA           | | | |                                            | |
| | • Tính cách: Cực kỳ ấm áp, sẵn sàng     | | | +--------------------------------------------+ |
| |   chờ bạn suy nghĩ, không bao giờ phán  | | | 🔒 Camera chỉ chạy trên máy bạn để tự soi     |
| |   xét khi bạn nói vấp.                  | | |    khẩu hình miệng, hoàn toàn bảo mật.         |
| | • Giọng nói: Nữ chuẩn Mỹ (US Neutral).  | | +------------------------------------------------+
| | • Tốc độ: 90% tốc độ tự nhiên.          | |
| | [ 🔊 Nghe thử giọng chào của Emma ]      | |
| +-----------------------------------------+ | |
+---------------------------------------------+ +------------------------------------------------+

--- SECTION 5: BADGE TRẠNG THÁI & NÚT BẮT ĐẦU (BOTTOM ACTION BAR) --------------------------------
+------------------------------------------------------------------------------------------------+
| [✓] Mic: Sẵn sàng (To, rõ)   |  [✓] Cấp độ: A2   |  [✓] Đối thoại: Emma                        |
|                                                                                                |
| [ ← Quay lại Trang chủ ]                       [ 🚀 BẮT ĐẦU VÀO PHÒNG LUYỆN TẬP NGAY → ]       |
|                                                (Nút chỉ sáng khi Mic đạt Trạng thái 3)         |
+------------------------------------------------------------------------------------------------+
==================================================================================================
```

---

## 4. MÀN HÌNH 3: PHÒNG LUYỆN THOẠI VIDEO CALL (VIDEO CALL SIMULATOR ROOM)

### 4.1. Kiến Trúc Bố Cục (Layout Architecture)
* **Trải nghiệm Video Call 1-1 Chân Thực (Google Meet / FaceTime Style):**
  * Tối đa hóa cảm giác đàm thoại đời thực, gỡ bỏ giao diện bài tập trắc nghiệm truyền thống.
* **Khung hình chính (Main Stage - AI Actor Viewport):**
  * Chân dung nhân vật AI đối thoại (Barista Emma) chiếm trọn khung nhìn chính trong không gian quán cà phê ấm cúng.
  * **Vòng hào quang sóng âm (Voice-Reactive Audio Visualizer):** Vòng hào quang màu xanh Cyan quanh nhân vật, phập phồng co giãn theo biên độ âm lượng giọng nói AI thời gian thực thông qua Web Audio API `AnalyserNode`.
* **Góc Camera người học (Picture-in-Picture - PIP Self-View):**
  * Cửa sổ camera nhỏ bo góc ở góc trên bên phải, hiển thị gương soi khẩu hình người học (100% xử lý cục bộ trên trình duyệt, không gửi frame nào lên mạng).
* **Thanh Phụ Đề Nổi (Live Subtitle / CC Overlay - Mặc định TẮT):**
  * Nằm nổi phía dưới nhân vật AI với nền kính mờ (glassmorphism).
  * **Nguyên tắc Sư phạm:** Mặc định **TẮT** để người học tập trung luyện nghe (Listening). Người học chỉ bấm bật nút `[CC]` khi nghe không kịp và cần nhìn chữ để nắm bắt nội dung.
* **Thanh Điều Khiển Nổi Chân Trang (Floating Meeting Dock):**
  * Thiết kế bo tròn dạng dock nổi ở đáy màn hình với 6 icon điều khiển đa năng:
    1. **💡 Tips (Gợi ý giàn giáo):** Tự động phát hiệu ứng vòng sáng vàng nhấp nháy (Golden Pulse) sau 8 giây người học im lặng. Bấm vào sẽ trượt mở khay giàn giáo bên phải.
    2. **💬 Script (Lịch sử hội thoại):** Ghi ngầm toàn bộ lượt thoại. Có chấm badge thông báo khi có câu mới. Bấm vào sẽ mở tab xem toàn bộ lịch sử thoại.
    3. **CC (Phụ đề Live):** Toggle bật/tắt hiển thị dòng phụ đề thời gian thực (Xám: Tắt, Xanh Cyan: Bật).
    4. **🎤 Mic (Thu âm chính):** Nút to nhất ở giữa với vòng sáng tím nổi bật, nhận diện giọng nói hoặc cướp lời AI (Barge-in).
    5. **✓ Send (Gửi câu):** Màu xanh lá sáng, người học bấm chủ động khi hài lòng với câu nói để gửi sang Gemini.
    6. **↺ Undo/Redo (Nói lại):** Huy hiệu đếm số lượt còn lại (Badge "2"), kích hoạt nhịp đệm chuẩn bị 1.5s chống spam.
* **Khay Trượt Bên Phải (Slide-over Drawer Panel - On-Demand):**
  * Chỉ trượt ra từ cạnh phải khi người học click vào nút `[💡 Tips]` hoặc `[💬 Script]` (chiếm 25-30% độ rộng màn hình, không che video):
    * **Tab 1 - Giàn giáo trợ giúp (`BR-SCA`):** Ý đồ tiếng Việt $\rightarrow$ Khung câu rỗng điền khuyết $\rightarrow$ Ngân hàng từ khóa kèm icon loa `[🔊]` nghe mẫu phát âm.
    * **Tab 2 - Lịch sử hội thoại (Script History):** Toàn bộ các lượt thoại từ đầu đến hiện tại được ghi ngầm để xem lại.

### 4.2. Wireframe Màn Hình 3 (Desktop & Mobile)

```
====================================================================================================
[← Rời phòng]   ☕ Gọi Cà Phê Với Emma [Cấp độ A2]                     [⏱️ 00:08/00:15] [⚙️ Cài đặt]
----------------------------------------------------------------------------------------------------
+--------------------------------------------------------------------------------------------------+
|                                  KHUNG HÌNH VIDEO CALL CHÍNH                                     |
|                                                                                                  |
|                             +--------------------------------------+                             |
|                             |               (( 👩 ))               |   +-----------------------+ |
|                             |                 EMMA                 |   | [ 📷 GÓC CAMERA BẠN ] | |
|                             |        (Barista Local Roastery)      |   | (PIP bo tròn góc phải)| |
|                             |                                      |   |                       | |
|                             |         ~ Emma đang nói... ~         |   |      [Bạn ở đây]      | |
|                             |    (Vòng sóng âm tỏa sáng quanh)     |   | [Mic: 🟢 Đang bật]    | |
|                             +--------------------------------------+   +-----------------------+ |
|                                                                                                  |
|                                                                                                  |
|                                                                                                  |
|          [CC PHỤ ĐỀ MỜ - CHỈ HIỆN KHI BẬT]:                                                      |
|          "Good morning! Welcome to Local Roastery. What can I get started for you today?"        |
|                                                                                                  |
+--------------------------------------------------------------------------------------------------+

--- THANH ĐIỀU KHIỂN NỔI (FLOATING DOCK Ở ĐÁY MÀN HÌNH - GIỐNG GOOGLE MEET) ------------------------
+--------------------------------------------------------------------------------------------------+
|      [ 💡 Tips ]    [ 💬 Script ]    [ CC Phụ đề ]    [ 🎤 MIC ]    [ ✓ Gửi ]    [ ↺ (2) ]       |
|      (Gợi ý câu)    (Lịch sử chat)   (Bật/Tắt)        (Chính)       (Xong câu)   (Nói lại 1.5s)  |
+--------------------------------------------------------------------------------------------------+

>>> KHI CLICK [ 💡 TIPS ] HOẶC [ 💬 SCRIPT ] ──► KHAY TRƯỢT MỞ RA TỪ CẠNH PHẢI (SLIDE-OVER PANEL):
+----------------------------------------------------+
| [X] ĐÓNG PANEL             [ (•) Tips  |  ( ) Script ] |
|----------------------------------------------------|
| 💡 GIÀN GIÁO GỢI Ý (TIPS ON-DEMAND):               |
| 1. Ý đồ tiếng Việt (Converse Intent):              |
|    "Nói bạn muốn 1 ly latte ít đường và hỏi wifi." |
|                                                    |
| 2. Khung câu rỗng (Fill-in-the-blank):             |
|    "Could I get a [ đồ uống ], with less [ gia vị ]|
|     and what is the [ ? ] password?"               |
|                                                    |
| 3. Từ vựng & Phát âm mẫu (Keyword Chips):          |
|    [🔊 latte]    [🔊 less sugar]    [🔊 password]  |
+----------------------------------------------------+
```

---

## 5. MÀN HÌNH 4: TỔNG KẾT & PHÂN TÍCH LỖI TƯƠNG TÁC (SUMMARY & TURN INSPECTOR)

### 5.1. Kiến Trúc Bố Cục (Layout Architecture)
* **Thẻ Tổng Quan & Điểm Chỉ Số CSI (Conversational Success Index Card):**
  * Vòng tròn điểm số trung tâm (Thang 100): Hiển thị điểm CSI tổng hợp.
  * 3 Thanh đo thành phần minh bạch:
    * 🎯 **Giải quyết tình huống (Resolution - 40%):** Hoàn thành mục tiêu giao tiếp.
    * 🌊 **Khả năng thích ứng (Adaptability - 35%):** Xử lý bất ngờ khi barista đổi tình huống.
    * 🗣️ **Độ trôi chảy & Tự nhiên (Flow & Fluency - 25%):** Tốc độ phản xạ, nhịp thở (chêm tiếng Việt được ghi nhận nhẹ nhàng tại đây).
  * Lời khích lệ tâm lý ấm áp: *"Xuất sắc! Bạn đã gọi đúng món mình thích và xử lý tình huống đổi sữa hạt rất tự tin."*
* **Dòng thời gian tương tác (Interactive Dialogue Timeline & Turn Inspector):**
  * Từng lượt thoại (Turn 1 $\rightarrow$ Turn N) được trình bày dưới dạng cặp đối thoại.
  * Lời nói của người học được phân tích chính xác với **Mã màu từng từ (Word-level Color Coding)**:
    * 🟩 **Xanh lá (Green - Điểm >= 85):** Phát âm chuẩn, rõ âm tiết.
    * 🟧 **Cam (Orange - Điểm 60-84):** Tạm ổn, nhấn sai trọng âm hoặc lướt âm chưa tròn.
    * 🟥 **Đỏ (Red - Điểm < 60):** Sai âm cơ bản hoặc nuốt âm gây hiểu lầm.
* **Thẻ Popover Sửa Lỗi Tức Thì (Micro-Popover on Word Click):**
  * Khi người học nhấp/chạm vào từ màu Đỏ hoặc Cam, một Popover nhỏ lập tức nổi lên ngay trên từ đó:
    * Phiên âm IPA chuẩn quốc tế.
    * Nhận xét lỗi sai ngắn gọn: *"Trọng âm rơi vào âm tiết đầu tiên /ˈlɑː-/, không đọc là /la-tê/."*
    * Nút `[🔊 Nghe người bản xứ đọc mẫu]`.
    * Nút `[🎤 Thu âm đọc thử lại]` kèm cơ chế chấm điểm nhanh 3 giây tại chỗ để giải tỏa tâm lý ngay lập tức.
* **Hộp Đề Xuất Diễn Đạt Tự Nhiên (Native Expression Alternative):**
  * So sánh câu nói nguyên bản của người học với cách nói tự nhiên của người bản xứ:
    * *Câu bạn nói:* `"I want latte no sugar."`
    * *Cách nói tự nhiên hơn:* `"Could I please have a latte with no sugar?"`
* **Thẻ Hành Động Vàng: Luyện Lại Lát Cắt Yếu Nhất (3-Step Slice Re-practice - `BR-REP`):**
  * Nút CTA lớn nổi bật dưới chân trang: `[🎯 Thực hành lại Lát cắt cần cải thiện nhất]`.
  * Khi bấm vào, một modal tập trung 3 bước mở ra:
    * **Bước 1 (AI dẫn nhập):** AI phát lại câu thoại tình huống bất ngờ mà người học từng lúng túng nhất.
    * **Bước 2 (User phản xạ):** Người học bấm mic nói lại 1 lượt duy nhất với giàn giáo gợi ý hỗ trợ sẵn.
    * **Bước 3 (AI xác nhận & chúc mừng):** AI đưa ra phản hồi công nhận sự tiến bộ để người học kết thúc buổi học trong cảm giác chiến thắng trọn vẹn.

### 5.2. Wireframe Màn Hình 4 (Desktop & Mobile)

```
======================================================================================
                                🎉 HOÀN THÀNH PHIÊN LUYỆN
======================================================================================
+------------------------------------------------------------------------------------+
|                                  [ CSI: 86 / 100 ]                                 |
|                       "Tuyệt vời! Bạn đã hoàn thành tình huống rất mượt mà."       |
|                                                                                    |
| [🎯 Giải quyết tình huống: 90%]   [🌊 Thích ứng: 85%]   [🗣️ Độ trôi chảy: 80%]     |
+------------------------------------------------------------------------------------+

--- CHI TIẾT TỪNG LƯỢT THOẠI & PHÂN TÍCH TỪNG TỪ (CLICK TỪ ĐỂ XEM CHI TIẾT) ----------
+------------------------------------------------------------------------------------+
| LƯỢT 1: Chào hỏi & Gọi món ban đầu                                                |
| 👩 Emma: "Good morning! Welcome to Roastery Coffee. What can I get for you today?" |
|                                                                                    |
| 👤 Bạn: [🟩 Could] [🟩 I] [🟩 have] [🟩 a] [🟥 latte] [🟩 with] [🟧 less] [🟩 sugar]?|
|                                                │                                   |
|                                  +─────────────┴─────────────+                     |
|                                  | 🟥 latte /ˈlɑːteɪ/        |                     |
|                                  | Sai trọng âm âm tiết 1    |                     |
|                                  | [🔊 Nghe mẫu] [🎤 Đọc lại]|                     |
|                                  +───────────────────────────+                     |
| 💡 Cách diễn đạt tự nhiên hơn:                                                     |
|    "Could I please get a latte with less sugar?"                                   |
+------------------------------------------------------------------------------------+
| LƯỢT 2: Tình huống xoay sở bất ngờ (Twist)                                        |
| 👩 Emma: "Oh, our espresso machine is having a quick reset. Would you mind waiting |
|          5 minutes or would you prefer a cold brew instead?"                       |
|                                                                                    |
| 👤 Bạn: [🟩 I] [🟩 can] [🟩 wait] [🟩 five] [🟩 minutes], [🟩 no] [🟩 problem]!    |
| 💡 Nhận xét phản xạ: Bạn phản xạ rất nhanh và không hề ngập ngừng!                |
+------------------------------------------------------------------------------------+

--- HERO ACTION: LUYỆN LẠI LÁT CẮT YẾU NHẤT (3 BƯỚC) --------------------------------
+------------------------------------------------------------------------------------+
| 🚀 THỰC HÀNH LẠI ĐỂ HOÀN THIỆN (KHUYẾN NGHỊ)                                       |
| Lát cắt cần cải thiện: Cách phát âm từ "latte" và phản xạ gọi đồ uống              |
|                                                                                    |
|          [ 🎯 BẮT ĐẦU LUYỆN LẠI LÁT CẮT (CHỈ MẤT 1 PHÚT) → ]                       |
|          [ 🏠 Trở về Trang chủ ]              [ 🔄 Luyện lại bài này từ đầu ]       |
+------------------------------------------------------------------------------------+
======================================================================================
```

---

## 6. THIẾT KẾ RESPONSIVE & MOBILE-FIRST TỐI ƯU

| Màn hình | Giao diện Desktop (> 1024px) | Giao diện Mobile (< 768px) |
| :--- | :--- | :--- |
| **Màn 1: Trang chủ** | Lưới 3 cột card, Hover Peek flyout mở trôi ngang bên cạnh card. | 1 cột card cuộn dọc, chạm vào thẻ hiện Bottom Sheet xem chi tiết kèm nút bắt đầu. |
| **Màn 2: Chuẩn bị (Lobby)** | Chia 2 cột tỷ lệ 7:5 (Trái: Option & Persona; Phải: Check Mic & Camera). | Xếp chồng dọc 1 cột: Recap $\rightarrow$ Chọn Persona $\rightarrow$ Check Mic & Cam $\rightarrow$ Nút Bắt đầu dính đáy (Sticky Footer). |
| **Màn 3: Phòng Simulator** | Bố cục 2 cột (Cột trái: Avatar to & sóng âm; Cột phải: Khung chat cuộn & Action Bar). | Cột đơn dọc: Avatar thu gọn ở đỉnh (120px), Bong bóng chat ở giữa, Bộ nút mic cố định chân trang (Sticky Footer), Giàn giáo trượt từ dưới lên (Bottom Sheet). |
| **Màn 4: Tổng kết** | Điểm CSI & 3 trụ cột dàn ngang 3 cột. Timeline mở rộng có popover nổi sang bên. | Điểm CSI xếp chồng thẻ dọc. Chạm vào từ màu Đỏ/Cam mở Micro-Modal nhỏ cố định đáy màn hình để dễ bấm ngón tay cái. |
