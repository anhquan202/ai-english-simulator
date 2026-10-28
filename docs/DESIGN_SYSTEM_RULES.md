# DESIGN SYSTEM RULES & SPECIFICATION
**Dự án:** AI-Powered Real-World English Communication Simulator  
**Phong cách:** Nordic Sage / Modern Editorial Calm (Anti-Anxiety UI)  
**Tiêu chuẩn kỹ thuật:** Tailwind CSS v3, Inter (Sans-Serif), Google Material Symbols, 8pt Spacing Grid

---

## 1. Triết lý thiết kế & Định vị thẩm mỹ

* **Tên phong cách:** **Nordic Sage / Modern Editorial Calm**.
* **Mục tiêu cốt lõi:** **Anti-Anxiety UI (Giảm thiểu áp lực giao tiếp)**. Người học tiếng Anh thường gặp áp lực tâm lý và cortisol tăng cao khi phải mở lời. Tone màu xô thơm tự nhiên (`#267052`) trên nền kem pastel dịu mát (`#F7F8F3`) tạo cảm giác an toàn, thư giãn và nâng cao khả năng tiếp thu.
* **Nguyên tắc tương phản & chiều sâu:**
  * **Zero Heavy Shadows:** Không dùng shadow đen đậm (`shadow-2xl`, `rgba(0,0,0,...)`). Toàn bộ chiều sâu được kiến tạo qua đường viền mảnh 1px (`#E2E7DE`) và các lớp nền phân tầng.
  * **No "AI Slop":** Tuyệt đối không dùng gradient neon tím-cyan công nghiệp, không dùng nền đen kịt `#000000`.

---

## 2. Bảng màu Semantic Tokens (Color System)

Lập trình viên **tuyệt đối không hardcode mã hex `#...`** trong views. Mọi màu sắc phải dùng class ngữ nghĩa tương ứng:

| Token Tailwind | Mã Hex | Vai trò & Ứng dụng |
| :--- | :---: | :--- |
| `bg-sage-canvas` | `#F7F8F3` | Nền canvas toàn bộ ứng dụng (thay thế nền trắng tinh chói mắt). |
| `bg-sage-surface` | `#FFFFFF` | Nền các thẻ bài học (Card), Header, modal, dropdown. |
| `bg-sage-subtle` | `#F1F3EC` | Nền các khối phụ, khung ảnh thumbnail, pill tài khoản header. |
| `bg-sage-tint` | `#EAF3EB` | Nền xanh pastel nhẹ cho Hero Spotlight và badge nổi bật. |
| `border-sage-border` | `#E2E7DE` | Viền 1px chuẩn mực cho card, input search, divider. |
| `text-pine` | `#202D27` | Màu chữ chính (Charcoal Pine - Đen thông tự nhiên, dịu hơn pitch black). |
| `text-pine-muted` | `#6C766F` | Màu chữ phụ, mô tả ngắn, placeholder, thời lượng bài. |
| `bg-sage-primary` / `text-sage-primary` | `#267052` | Màu thương hiệu chính: Nút CTA, tab active, tag overline, focus border. |
| `bg-sage-primary-hover` | `#1E5A42` | Trạng thái hover của nút bấm chính. |
| `bg-sage-accent` / `text-sage-accent-dark` | `#DCEBBA` / `#174D38` | Điểm nhấn Avatar và nhãn trạng thái đặc biệt. |

---

## 3. Quy chuẩn Typography (100% Sans-Serif - Inter)

* **Font Family duy nhất:** `Inter`, `system-ui`, `-apple-system`, `sans-serif`.
* **Font Weights hợp lệ:**
  * `400` (Regular - `font-normal`): Body, mô tả, subtitle.
  * `500` (Medium - `font-medium`): Nav links, filter pills, badge meta.
  * `600` (SemiBold - `font-semibold`): Buttons, Card titles, category tags.
  * `700` (Bold - `font-bold`): Tiêu đề chính H1, H2.
* **Loại bỏ hoàn toàn các kích thước lẻ** (`11px`, `13px`, `17px`, `23px`, `29px`). Ánh xạ chuẩn mực:

| Cấp bậc chuẩn | Kích thước | Tailwind Class | Ứng dụng nghiệp vụ |
| :--- | :---: | :--- | :--- |
| **Extra Small (XS)** | `12px` (`0.75rem`) | `text-xs` | Tag danh mục (uppercase), thời lượng bài học (`8-10 min`), mô tả thẻ bài. |
| **Small (SM)** | `14px` (`0.875rem`) | `text-sm` | Nút CTA, Nav menu, tên user, nội dung body mặc định. |
| **Base / Large** | `16px` / `18px` | `text-base` $\rightarrow$ `lg:text-lg` | Tiêu đề thẻ kịch bản (Scenario Card Title). |
| **X-Large (XL / 2XL)** | `20px` / `24px` | `text-xl` $\rightarrow$ `md:text-2xl` | Tiêu đề phân mục lớn (Section Heading H2). |
| **2X-Large (2XL)** | `24px` (`1.5rem`) | `text-2xl` | Logo thương hiệu *"mở lời."*. |
| **Display (3XL / 4XL)** | `30px` / `36px` | `text-2xl md:text-3xl lg:text-4xl` | Hero Spotlight Title & Lời chào trang chủ (H1). |

---

## 4. Ma trận Responsive Breakpoint Typography & Layout

| Thành phần giao diện | Mobile (`<640px`) | Tablet (`md: 768px`) | Desktop (`lg: 1024px` / `xl: 1280px+`) | Class Tailwind đầy đủ |
| :--- | :---: | :---: | :---: | :--- |
| **H1 (Hero / Lời chào)** | `24px` (bold) | `30px` (bold) | `36px` (bold) | `text-2xl md:text-3xl lg:text-4xl font-bold tracking-tight text-pine` |
| **H2 (Section Heading)** | `18px` (bold) | `20px` (bold) | `24px` (bold) | `text-lg md:text-xl lg:text-2xl font-bold text-pine` |
| **Card Title (Tên kịch bản)** | `16px` (semibold) | `16px` | `18px` (semibold) | `text-base lg:text-lg font-semibold text-pine` |
| **Body / Subtitle** | `14px` | `14px` | `16px` | `text-sm lg:text-base text-pine-muted` |
| **Filter Pills & Nav** | `12px` (medium) | `12px` | `14px` (medium) | `text-xs md:text-sm font-medium` |
| **Overline & Meta Tag** | `12px` (semibold) | `12px` | `12px` (semibold) | `text-xs font-semibold tracking-wider uppercase` |

---

## 5. Hệ thống Nhịp điệu Spacing 4 Tầng (4-Tier Spacing Hierarchy)

Tuân thủ nghiêm ngặt **8pt Grid** cho khoảng cách cấu trúc và **4pt Grid** cho micro-spacing.

| Tầng Spacing | Vị trí áp dụng | Mobile (`<640px`) | Tablet (`md:`) | Desktop (`lg:` / `xl:`) | Tailwind Class chuẩn |
| :--- | :--- | :---: | :---: | :---: | :--- |
| **Tầng 1: Page Shell** | Lề ngang khung trang (`max-w-7xl`) | `16px` (`px-4`) | `24px` (`px-6`) | `32px` / `48px` (`lg:px-8 xl:px-12`) | `px-4 sm:px-6 lg:px-8 xl:px-12` |
| | Lề dọc đỉnh & đáy trang | `24px` (`py-6`) | `32px` (`py-8`) | `40px` (`py-10`) | `py-6 md:py-8 lg:py-10` |
| | Chiều cao thanh Header | `64px` (`h-16`) | `72px` (`h-18`) | `80px` (`h-20`) | `h-16 md:h-18 lg:h-20` |
| **Tầng 2: Section** | Khoảng cách giữa các Section lớn | `32px` (`mb-8`) | `40px` (`mb-10`) | `48px` (`mb-12`) | `mb-8 md:mb-10 lg:mb-12` |
| | Tiêu đề Section (H2) $\rightarrow$ Nội dung | `16px` (`mb-4`) | `20px` (`mb-5`) | `24px` (`mb-6`) | `mb-4 md:mb-5 lg:mb-6` |
| | Lời giới thiệu Subtitle $\rightarrow$ H2 | `4px` (`mb-1`) | `6px` (`mb-1.5`) | `8px` (`mb-2`) | `mb-1 md:mb-1.5 lg:mb-2` |
| **Tầng 3: Component** | Khung Hero Spotlight (Padding ruột) | `20px` (`p-5`) | `24px` (`p-6`) | `32px` (`p-8`) | `p-5 md:p-6 lg:p-8` |
| | Grid khoảng cách các Card | `16px` (`gap-4`) | `20px` (`gap-5`) | `24px` (`gap-6`) | `gap-4 md:gap-5 lg:gap-6` |
| | Padding ruột trong của Card | `16px` (`p-4`) | `16px` (`p-4`) | `20px` (`p-5`) | `p-4 lg:p-5` |
| | Ảnh minh họa Card $\rightarrow$ Chữ bên dưới | `12px` (`mb-3`) | `12px` (`mb-3`) | `16px` (`mb-4`) | `mb-3 lg:mb-4` |
| | Tiêu đề Card $\rightarrow$ Mô tả ngắn | `6px` (`mb-1.5`) | `6px` (`mb-1.5`) | `8px` (`mb-2`) | `mb-1.5 lg:mb-2` |
| | Footer Card (Đường kẻ $\rightarrow$ Nút Bắt đầu) | `12px` (`pt-3 mt-3`) | `12px` (`pt-3 mt-3`) | `16px` (`pt-4 mt-4`) | `pt-3 mt-3 lg:pt-4 lg:mt-4` |
| **Tầng 4: Micro** | Nút bấm CTA (Padding ngang/dọc) | `h-11 px-5` | `h-11 px-5` | `h-12 px-6` | `h-11 md:h-12 px-5 md:px-6` |
| | Filter Pills (Padding ngang/dọc) | `h-8 px-3.5` | `h-9 px-4` | `h-9 px-4` | `h-8 md:h-9 px-3.5 md:px-4` |
| | Khoảng cách Icon với Text | `6px` (`gap-1.5`) | `8px` (`gap-2`) | `8px` (`gap-2`) | `gap-1.5 md:gap-2` |
| | Thanh tìm kiếm Search (Padding) | `h-10 px-3.5` | `h-11 px-4` | `h-11 px-4` | `h-10 md:h-11 px-3.5 md:px-4` |

---

## 6. Hệ thống Icon chuẩn hóa: Google Material Symbols Outlined

* **CDN Nhúng layout:**
  ```html
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  ```
* **Kích thước Icon quy chuẩn:**
  * Mini (`16px` / `[font-size:16px]`): Dành cho tag overline, badge thời lượng, icon inline với `text-xs`.
  * Small (`20px` / `[font-size:20px]`): Dành cho Buttons CTA, Input search, filter pill với `text-sm`.
  * Base (`24px` / `[font-size:24px]`): Dành cho Nav item, Card action icon với `text-base`.
  * Large (`32px` - `40px`): Dành cho empty state hoặc visual illustration.
* **Bảng ánh xạ Icon chính:**
  * Logo / Voice: `forum` hoặc `chat_bubble`
  * Tìm kiếm: `search`
  * Tiếp tục / Bắt đầu: `arrow_forward`
  * Thời gian: `schedule`
  * Mua sắm: `shopping_bag`
  * Ăn uống / Cafe: `restaurant` / `local_cafe`
  * Công việc: `work`
  * Yêu thích: `favorite` / `bookmark`
  * Dropdown: `expand_more`
  * Check hoàn thành: `check_circle`

---

## 7. Bộ CSS Component Presets (`resources/css/app.css`)

Sử dụng trực tiếp các class sau để tránh viết lặp lại chuỗi breakpoint:

```css
@layer components {
    /* Khung trang chuẩn */
    .layout-container {
        @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 xl:px-12;
    }

    /* Nhịp điệu giữa các section */
    .section-spacing {
        @apply mb-8 md:mb-10 lg:mb-12;
    }

    /* Tiêu đề section xuống content */
    .section-header-spacing {
        @apply mb-4 md:mb-5 lg:mb-6;
    }

    /* Grid danh sách bài học */
    .scenario-grid {
        @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5 lg:gap-6;
    }

    /* Thẻ kịch bản */
    .scenario-card {
        @apply bg-sage-surface border border-sage-border hover:border-sage-primary rounded-card p-4 lg:p-5 transition-all duration-200 flex flex-col justify-between;
    }

    /* Hero Spotlight Container */
    .hero-container {
        @apply rounded-2xl lg:rounded-hero p-5 md:p-6 lg:p-8 bg-sage-tint border border-sage-border/80;
    }

    /* Nút bấm hành động chính */
    .btn-primary {
        @apply h-11 md:h-12 px-5 md:px-6 bg-sage-primary hover:bg-sage-primary-hover text-white text-sm font-semibold rounded-xl transition duration-150 inline-flex items-center justify-center gap-1.5 md:gap-2;
    }

    /* Thẻ lọc danh mục */
    .filter-pill {
        @apply h-8 md:h-9 px-3.5 md:px-4 rounded-full text-xs md:text-sm font-medium transition duration-150 inline-flex items-center gap-1.5;
    }
}
```

---

## 8. Nguyên tắc cấm kỵ (Do's & Don'ts Checklist)

* ❌ **CẤM:** Dùng kích thước lẻ `text-[11px]`, `text-[13px]`, `text-[17px]`, `text-[23px]`, `text-[29px]`.
* ❌ **CẤM:** Tự chế spacing tùy tiện `p-[18px]`, `py-7`, `my-11`, `gap-7`.
* ❌ **CẤM:** Hardcode mã màu hex trực tiếp trong Blade views (`#F7F8F3`, `#267052`).
* ❌ **CẤM:** Sử dụng font có chân (Serif) cho văn bản và tiêu đề. Toàn bộ dự án quy về họ `Inter`.
* ❌ **CẤM:** Dùng icon SVG tự do không kiểm soát kích thước stroke/viewBox; 100% icon chuyển về `material-symbols-outlined`.
* ✅ **BẮT BUỘC:** Mọi khoảng cách cấu trúc $\ge 16px$ phải chia hết cho 8; khoảng cách nhỏ $< 16px$ theo bước 4px hoặc 6px.
* ✅ **BẮT BUỘC:** Kiểm tra hiển thị responsive tối thiểu trên 3 mốc: Mobile (375px - 414px), Tablet (768px), Desktop (1280px - 1440px).

---

## 9. Quy Tắc Sử Dụng Typography & Tách Component (Pragmatic Architecture)

### 9.1. Quy tắc áp dụng Typography linh hoạt theo cấu trúc (Structure-based Typography)
* **KHÔNG ép buộc tuyệt đối**: Lập trình viên không bị ép buộc phải gán các class custom như `.text-heading`, `.text-title`, hay `.text-sub` cho mọi phần tử chữ trên trang.
* **Tiêu chí lựa chọn dựa vào cấu trúc**:
  1. **Text đơn lẻ, micro-copy, các phần tử cục bộ (One-off / Isolated elements)**:
     - Hoàn toàn chấp nhận sử dụng trực tiếp các class utility tiêu chuẩn của Tailwind CSS (ví dụ: `text-sm font-medium text-pine-subtle`, `text-xs text-stone-400`, `text-xs font-semibold`).
     - Tránh gò ép tạo hoặc dùng class custom cho những đoạn text đặc thù chỉ xuất hiện 1 lần.
  2. **Section lớn, tiêu đề chính của trang, hoặc các khối có tính lặp lại ở nhiều vị trí / nhiều màn hình**:
     - **ƯU TIÊN CAO** sử dụng các custom classes / semantic tokens đã được định nghĩa (`.text-display`, `.text-heading`, `.text-subheading`, `.text-lead`, `.text-sub`, `.text-body`, `.text-caption`, `.section-py`).
     - Đảm bảo tính nhất quán về kích thước (size scale), nhịp điệu thị giác (visual rhythm), và độ co giãn responsive đồng bộ theo breakpoint trên toàn dự án.

### 9.2. Nguyên tắc ưu tiên tách Component khi có tính lặp lại (Component Extraction Priority)
* **ƯU TIÊN TÁCH COMPONENT** (Blade Component / Partial) khi một section, khối giao diện hoặc thẻ card có tính chất lặp lại (reusable) ở nhiều vị trí hoặc trên nhiều màn hình.
  - **Ví dụ điển hình**:
    - Thẻ câu chuyện / kịch bản (`<x-story-card>` / `<x-scenario-card>`).
    - Khối thẻ bước đối thoại / Step Card.
    - Hộp kết quả / Trụ cột đánh giá CSI (`<x-csi-pillar>`).
    - Form Input chuẩn hóa (`<x-form-input>`).
* **Lợi ích kiến trúc**:
  - Tránh lặp lại hàng chục dòng HTML / Tailwind phức tạp ở nhiều file khác nhau.
  - Đảm bảo khi cần tinh chỉnh giao diện, logic hoặc sửa bug chỉ cần can thiệp tại một điểm duy nhất (Single Source of Truth).
  - Tách bạch rõ ràng giữa tầng cấu trúc component dùng chung và tầng view lắp ghép.

---

## 10. Quy Chuẩn Button, CTA & Touch Target trên Mobile (Accessibility & Ergonomics)

### 10.1. Hệ thống Design Tokens cho Button & Touch Target (CẤM set lẻ trong views)
Tuân thủ tiêu chuẩn Apple Human Interface Guidelines ($\ge 44 \times 44\text{pt}$) và Google Material Design 3 ($\ge 48 \times 48\text{dp}$). Mọi nút bấm bắt buộc dùng class Design Token, không tự viết inline các chuỗi class dài:

| Loại Button | Class Token Chuẩn | Chiều cao tối thiểu (`min-h`) | Padding-y (`py`) | Spacing & Touch Target Token |
| :--- | :--- | :--- | :--- | :--- |
| **Hero / Primary CTA** | `.btn-cta` | `min-h-cta sm:min-h-cta-lg` (56px/64px) | `py-4.5 sm:py-5 px-8 sm:px-10` | Nút kêu gọi hành động chính toàn trang |
| **Standard Action** | `.btn-primary` | `min-h-touch md:min-h-touch-lg` (44px/48px) | `py-2.5 md:py-3 px-5 md:px-6` | Nút hành động chuẩn trong form, modal |
| **Header / Compact** | `.btn-compact` | `min-h-touch-md sm:min-h-touch` (40px/44px) | `py-2 px-4` | Thanh navbar, thanh công cụ header |
| **Icon Button** | `.btn-icon` | `min-w-touch min-h-touch` (44px x 44px) | Căn giữa flex | Nút micro, phát âm, đóng modal |
| **Filter Pill** | `.filter-pill` | `min-h-touch-sm sm:min-h-touch-md` (36px/40px) | `py-1.5 sm:py-2 px-3.5 sm:px-4` | Thẻ lọc danh mục bài học |

### 10.2. Các Design Tokens được định nghĩa trong `tailwind.config.js`
* **`minHeight` tokens**:
  - `min-h-touch-sm`: `2.25rem` (36px)
  - `min-h-touch-md`: `2.5rem` (40px)
  - `min-h-touch`: `2.75rem` (44px - Apple HIG standard)
  - `min-h-touch-lg`: `3rem` (48px - Google Material standard)
  - `min-h-cta`: `3.5rem` (56px)
  - `min-h-cta-lg`: `4rem` (64px)
* **`minWidth` tokens**:
  - `min-w-touch`: `2.75rem` (44px)
  - `min-w-touch-lg`: `3rem` (48px)
* **`spacing` tokens**:
  - `py-4.5`: `1.125rem` (18px)
  - `gap-touch-gap-sm`: `0.5rem` (8px - khoảng cách tối thiểu)
  - `gap-touch-gap`: `0.75rem` (12px - khoảng cách khuyến nghị)
  - `gap-touch-gap-lg`: `1rem` (16px)

### 10.3. Nguyên tắc Padding-y bắt buộc cho Button
* **CẤM gán cứng `height` đơn thuần mà thiếu `padding-y`**:
  - Khi set class chỉ có `h-10` hay `h-12` mà không có padding dọc, nếu font-size thay đổi hoặc văn bản trên mobile xuống 2 dòng, khối nút sẽ bị vỡ layout, chữ đè sát viền.
  - Bắt buộc luôn dùng `py-*` kết hợp với `min-h-[...]` (hoặc dùng token `.btn-cta`, `.btn-primary`, `.btn-compact`) để nút có vùng đệm nội dung tự nhiên.

### 10.4. Khoảng cách an toàn giữa các phần tử click trên Mobile (Touch Target Spacing)
* **Khoảng cách tối thiểu giữa 2 nút bấm hoặc link liền kề**: **Tối thiểu 8px (`gap-2` hoặc `gap-touch-gap-sm`)**, chuẩn an toàn là **12px – 16px (`gap-3` / `gap-touch-gap` đến `gap-4` / `gap-touch-gap-lg`)**.
* **Nhóm nút bấm xếp dọc (Button Stack)**: Dùng `space-y-3` hoặc `flex flex-col gap-3` (12px) để ngón tay cái người dùng không chạm nhầm vào nút bên dưới.
* **Nhóm nút bấm xếp ngang (Button Row)**: Dùng `flex items-center gap-3 sm:gap-4`.
* **CẤM TUYỆT ĐỐI**: Để 2 nút bấm tương tác ngược chiều (ví dụ: "Xác nhận" và "Hủy/Xóa") đặt sát rạt nhau (`gap-0` hoặc `margin < 8px`) mà không có khoảng cách an toàn.
