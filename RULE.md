# DỰ ÁN: AI REAL-WORLD ENGLISH SIMULATOR
# QUY CHUẨN PHÁT TRIỂN & RÀNG BUỘC KIẾN TRÚC (RULE.md)
*Tài liệu chuẩn mực bắt buộc tuân thủ 100% đối với mọi Lập trình viên và AI Agent khi làm việc trên codebase này.*

---

## 1. MỤC ĐÍCH & NGUYÊN TẮC BẤT KHẢ XÂM PHẠM (CORE PRINCIPLES)

1. **Chống hồi quy lỗi (Zero Regression Policy)**: Một lỗi đã được phát hiện và sửa dứt điểm thì **tuyệt đối không được phép tái diễn** ở các lần cập nhật tiếp theo (đặc biệt là lỗi khựng cuộn, lỗi zoom iOS, và lỗi padding).
2. **Thực dụng & Rõ ràng (Pragmatic Architecture)**: Không áp dụng quy tắc một cách giáo điều, máy móc. Mọi quyết định CSS, Component và Layout phải dựa trên cấu trúc thực tế của giao diện.
3. **Single Source of Truth**: Mọi giá trị token, animation loop, và component tái sử dụng phải có duy nhất một nơi quản lý gốc.

---

## 2. QUY TẮC HIỆU NĂNG CUỘN & ANIMATION (CHỐNG KHỰNG / GIẬT LAG)

> [!CAUTION]
> **Đây là lỗi nghiêm trọng từng tái diễn nhiều lần do xung đột thư viện:**

1. **CẤM TUYỆT ĐỐI dùng `scroll-smooth` khi có thư viện Lenis**:
   - Khi trang sử dụng JavaScript Smooth Scroll (Lenis), **KHÔNG BAO GIỜ** được đặt class `scroll-smooth` vào thẻ `<html>` hoặc `<body>`.
   - *Lý do kỹ thuật*: CSS `scroll-smooth` sẽ đè lên thuật toán nội suy toạ độ của JavaScript Lenis, gây ra độ trễ (delay) từ 200–500ms, làm chuột bị "lì", cảm giác cuộn bị khựng và giật nấc.
2. **CẤM TUYỆT ĐỐI vòng lặp Animation Frame kép (Double RAF Loop)**:
   - Khi đã đồng bộ Lenis vào GSAP Ticker:
     ```javascript
     gsap.ticker.add((time) => {
         lenisInstance.raf(time * 1000);
     });
     gsap.ticker.lagSmoothing(0);
     ```
   - **NGHIÊM CẤM** chạy thêm bất kỳ vòng lặp `requestAnimationFrame(raf)` độc lập nào khác cho Lenis.
   - *Lý do kỹ thuật*: Gọi `lenis.raf()` hai lần trong một frame với hai mốc delta time khác nhau sẽ khiến thuật toán gia tốc tính toán sai lệch, gây ra hiện tượng rung lắc micro-stuttering.
3. **CẤM Layout Thrashing trong sự kiện Scroll**:
   - Không được dùng `window.addEventListener('scroll', ...)` thô để liên tục đọc các thuộc tính `scrollTop`, `scrollHeight`, `clientHeight` của DOM.
   - Bắt buộc đồng bộ trực tiếp từ sự kiện của Lenis:
     ```javascript
     lenisInstance.on('scroll', (e) => {
         ScrollTrigger.update();
         if (progressBar && typeof e.progress === 'number') {
             progressBar.style.width = (e.progress * 100) + '%';
         }
     });
     ```
4. **Cấu hình chuẩn của Lenis**:
   - `duration: 0.85` (giữ tốc độ phản xạ tức thì, không để $> 1.0$ gây cảm giác chậm chạp).
   - `smoothWheel: true`, `wheelMultiplier: 1.0`, `touchMultiplier: 1.0`.

---

## 3. QUY TẮC TYPOGRAPHY TOKENS THỰC DỤNG (PRAGMATIC TYPOGRAPHY)

1. **Phân biệt theo cấu trúc (Structure-based Usage)**:
   - **Text đơn lẻ, micro-copy, nhãn phụ (One-off snippets)**: Hoàn toàn chấp nhận dùng trực tiếp class utility chuẩn của Tailwind (ví dụ `text-sm font-medium text-pine-subtle`, `text-xs text-stone-400`). Không giáo điều ép buộc gán custom class cho các đoạn chữ đặc thù xuất hiện 1 lần.
   - **Section lớn, tiêu đề chính, hoặc các khối có tính lặp lại**: **ƯU TIÊN BẮT BUỘC** dùng các semantic tokens định nghĩa tại `resources/css/app.css` (`.text-display`, `.text-heading`, `.text-subheading`, `.text-lead`, `.text-sub`, `.text-body`, `.text-caption`).
2. **100% Tách Biệt Màu Sắc (Decoupled Color)**:
   - Các class Typography Token **CHỈ ĐƯỢC PHÉP** quản lý: `font-size`, `line-height`, `letter-spacing`, và responsive breakpoints.
   - **TUYỆT ĐỐI KHÔNG** gán cứng thuộc tính `color` bên trong các class Typography Token. Màu sắc phải được phối hợp tự do qua class utility của Tailwind (`text-pine`, `text-pine-subtle`, `text-white`, `text-terracotta`, `text-sage-primary`).
3. **Quy chuẩn tỷ lệ chữ (Tránh lỗi Subtitle quá to lấn át Heading)**:
   - `.text-display` / `.text-main`: `32px` mobile $\to$ `40px` sm $\to$ `48px` desktop (Tiêu đề Hero).
   - `.text-heading` / `.text-title`: `24px` mobile $\to$ `32px` sm $\to$ `36px` desktop (Tiêu đề Section).
   - `.text-subheading` / `.text-title-sm`: `18px` mobile $\to$ `20px` desktop (Tiêu đề thẻ/Card).
   - `.text-lead` / `.text-sub`: **`14px` mobile $\to$ `16px` desktop** (Chuẩn mực vừa mắt, tuyệt đối **KHÔNG dùng 18px** làm subtitle để đảm bảo khoảng cách 4px với heading 20px).
   - `.text-body`: `14px` mobile $\to$ `16px` desktop (Nội dung paragraph, `<textarea>`, `<input>`).
   - `.text-caption` / `.text-hint`: **`13px`** (Ngoại lệ số lẻ duy nhất được phê duyệt, chuẩn Apple HIG & Google Material).

---

## 4. QUY TẮC KHUNG TRỤC THỊ GIÁC (CONTAINER & SPACING)

1. **Khóa cứng trục dọc (Unified Visual Container)**:
   - Mọi section trên trang chủ và các trang công khai (Navbar, Chương 1, 2, 3, 4, Footer) **bắt buộc sử dụng chung một khung container ngoài**:
     ```html
     <div class="max-w-6xl mx-auto px-4 sm:px-6">
     ```
   - **CẤM TUYỆT ĐỐI** việc co bóp container ngoài thành `max-w-4xl` hay `max-w-3xl` ở cấp section, vì sẽ khiến lề trái/phải nhảy thụt thò liên tục khi cuộn (Jumping Margins), làm lệch tầm mắt của người dùng.
   - Nếu cần khối nội dung hẹp căn giữa, hãy bọc bên trong bằng `max-w-2xl mx-auto` hoặc `max-w-3xl mx-auto`.
2. **Quy chuẩn khoảng cách phân đoạn (Section Spacing)**:
   - Toàn bộ các section chính phải sử dụng class `.section-py`:
     - Mobile: `padding-top: 56px; padding-bottom: 56px;`
     - Desktop ($\ge 1024px$): `padding-top: 72px; padding-bottom: 72px;`
   - Tuyệt đối không dùng padding quá khổ (`py-20 sm:py-32` = 128px) gây cảm giác mỏi tay khi cuộn (Scroll Fatigue).

---

## 5. NGUYÊN TẮC ƯU TIÊN TÁCH COMPONENT (COMPONENT-FIRST)

1. **Tiêu chí tách Component**:
   - Bất kỳ khối giao diện nào có tính chất **lặp lại từ 2 lần trở lên** trên cùng một màn hình hoặc qua nhiều màn hình khác nhau đều phải được tách thành Blade Component (`<x-...>` trong `resources/views/components/`) hoặc Blade Partial (`resources/views/partials/`).
   - *Ví dụ bắt buộc tách*:
     - Story Card / Scenario Card
     - Step Cards / Khối các bước hướng dẫn
     - CSI Metric Pillars (Trụ cột đánh giá)
     - Form Input chuẩn hóa
2. **Mục tiêu kiến trúc**:
   - Đảm bảo **Single Source of Truth**: Sửa bug hoặc đổi style chỉ cần sửa tại 1 file component duy nhất.
   - Tránh duplicate hàng chục dòng HTML, giảm thiểu dung lượng file view chính.

---

## 6. QUY TẮC NỀN TẢNG & VIEWPORT (IOS / TAILWIND VALIDATION)

1. **Chống lỗi Zoom iOS Safari**:
   - Mọi layout (`resources/views/layouts/*.blade.php`, `landing/index.blade.php`) bắt buộc phải có:
     ```html
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
     ```
2. **Kiểm tra tính hợp lệ của class Tailwind**:
   - Trước khi sử dụng class Tailwind, phải kiểm tra xem class đó có tồn tại trong bộ chuẩn của Tailwind CSS hay không.
   - *Cấm tuyệt đối*: Không dùng class không tồn tại như `h-13`, `h-15`, `w-13`, `py-13`.

3. **Quy chuẩn tối thiểu cho Button, CTA & Touch Target trên Mobile**:
   > [!IMPORTANT]
   > Tuân thủ chuẩn Apple Human Interface Guidelines (44x44pt) và Google Material Design (48x48dp) để chống bấm trượt (fat finger) trên mobile.

   - **Hệ thống Design Tokens chuẩn cho Button & Touch Target (CẤM set lẻ trong Blade/Views)**:
     Toàn bộ các nút bấm và vùng chạm phải sử dụng các class Design Token tập trung, không viết inline các chuỗi class dài trong view:
     | Loại Button | Class Token Bắt Buộc | Chiều cao tối thiểu (`min-h`) | Padding-y (`py`) | Ghi chú & Nơi định nghĩa |
     | :--- | :--- | :--- | :--- | :--- |
     | **Hero / Primary CTA** | `.btn-cta` | `min-h-cta sm:min-h-cta-lg` (56px/64px) | `py-4.5 sm:py-5 px-8 sm:px-10` | Nút kích hoạt chính trang chủ / chuyển đổi |
     | **Standard Action** | `.btn-primary` | `min-h-touch md:min-h-touch-lg` (44px/48px) | `py-2.5 md:py-3 px-5 md:px-6` | Nút hành động form, modal, xác nhận |
     | **Header / Compact** | `.btn-compact` | `min-h-touch-md sm:min-h-touch` (40px/44px) | `py-2 px-4` | Thanh navbar, thanh công cụ toolbar |
     | **Icon Button** | `.btn-icon` | `min-w-touch min-h-touch` (44px x 44px) | Căn giữa flex | Nút micro, nút loa âm thanh, nút đóng modal |
     | **Filter Pill** | `.filter-pill` | `min-h-touch-sm sm:min-h-touch-md` (36px/40px) | `py-1.5 sm:py-2 px-3.5 sm:px-4` | Bộ lọc danh mục, thẻ chọn chủ đề |

   - **Tokens kích thước & khoảng cách mở rộng trong `tailwind.config.js`**:
     - `minHeight`: `'touch-sm': '2.25rem'` (36px), `'touch-md': '2.5rem'` (40px), `'touch': '2.75rem'` (44px), `'touch-lg': '3rem'` (48px), `'cta': '3.5rem'` (56px), `'cta-lg': '4rem'` (64px).
     - `minWidth`: `'touch': '2.75rem'` (44px), `'touch-lg': '3rem'` (48px).
     - `spacing`: `'4.5': '1.125rem'` (18px), `'touch-gap-sm': '0.5rem'` (8px), `'touch-gap': '0.75rem'` (12px), `'touch-gap-lg': '1rem'` (16px).

   - **Nguyên tắc Padding-y bắt buộc cho Button**:
     - **CẤM gán cứng `height` đơn thuần mà thiếu `padding-y`** (ví dụ chỉ set `h-10` mà không có padding nội dung). Khi chữ bị xuống dòng hoặc co kéo font, nút sẽ bị ép bẹp, cụt viền.
     - Luôn kết hợp `py-*` cùng với `min-h-[...]` (hoặc dùng các token `.btn-cta`, `.btn-primary`, `.btn-compact`) để button vừa có không gian thở (breathing room), vừa đảm bảo kích thước tối thiểu khi click.
   - **Khoảng cách an toàn giữa 2 phần tử click trên Mobile (Click Margin / Spacing)**:
     - Khoảng cách giữa 2 nút bấm hoặc thẻ liên kết cạnh nhau trên mobile **tối thiểu phải là 8px** (`gap-2` hoặc `gap-touch-gap-sm`), khuyến nghị **12px – 16px** (`gap-3` / `gap-touch-gap` đến `gap-4` / `gap-touch-gap-lg`).
     - Với danh sách nút bấm xếp dọc (stack): `space-y-3` hoặc `gap-3` (12px).
     - Với nhóm nút bấm xếp ngang (row): `gap-3 sm:gap-4`.
     - Tuyệt đối không để 2 nút bấm tương tác dính sát nhau (`gap-0` hoặc `margin < 8px`), tránh việc người dùng chạm nhầm nút hủy/xác nhận.

4. **Quy tắc chạy lệnh**:
   - Lệnh PHP & Artisan: Bắt buộc chạy qua Docker: `docker exec ai_simulator_app php artisan ...`
   - Lệnh biên dịch Frontend: Chạy trên host: `npm run build`

---

## 7. QUY TRÌNH DELEGATION & CHỐNG HỒI QUY (ANTI-REGRESSION PROTOCOL)

1. Khi Agent Tech Lead giao việc cho Sub-agent (`feature_dev`, `frontend-dev`):
   - Phải nêu rõ các **Ràng buộc bất khả xâm phạm (Inviolable Rules)** trong brief nhiệm vụ.
   - Yêu cầu sub-agent tuyệt đối không được ghi đè (overwrite) toàn bộ file nếu chưa đối chiếu các bản vá lỗi trước đó.
2. Trước khi hoàn thành nghiệm thu bất kỳ task UI nào:
   - Phải chạy `npm run build` kiểm tra 0 warning/error.
   - Phải chạy `php artisan view:clear && php artisan view:cache` đảm bảo không lỗi cú pháp Blade.
   - Phải chạy `php artisan test tests/Feature/LandingPageTest.php` đạt 100% PASS.
   - Kiểm tra trực quan chuyển động cuộn chuột trên trình duyệt đảm bảo 60–120fps không giật khựng.
