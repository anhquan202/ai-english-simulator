# HƯỚNG DẪN & ĐẶC TẢ SETUP HẠ TẦNG (INFRASTRUCTURE SETUP SPECIFICATION)
## Dự Án: AI-Powered Real-World English Communication Simulator
**Mục tiêu tài liệu:** Ghi nhận toàn bộ các quyết định kỹ thuật đã chốt, chi tiết hạ tầng cơ sở dữ liệu **MySQL 8.0**, lưu trữ File/Media (Cloudflare R2), và lộ trình môi trường từ Local đến Public Production.

---

## 1. TỔNG HỢP CÁC QUYẾT ĐỊNH ĐÃ CHỐT 100% (LOCKED ARCHITECTURE DECISIONS)

| Thành phần | Lựa chọn đã chốt | Lý do chốt & Trách nhiệm chính |
| :--- | :--- | :--- |
| **Cơ sở dữ liệu (DB)** | **MySQL 8.0 (InnoDB utf8mb4)** | **Lựa chọn chính thức:** Chuẩn mực mặc định số 1 của Laravel. Động cơ InnoDB hỗ trợ khóa cấp dòng (Row-level locking) giải quyết triệt để vấn đề lock ghi khi nhiều người học cùng luyện nói; hỗ trợ kiểu dữ liệu `JSON` native và tương thích 100% các nhà cung cấp Cloud/VPS. |
| **Lưu trữ File & Audio** | **Cloudflare R2 (S3-compatible)** | R2 có chính sách **0đ phí băng thông tải ra (Zero Egress Fees)**, cực kỳ tiết kiệm cho ứng dụng nghe lại file âm thanh người học. Local dùng tạm `storage/public`. |
| **Tầng STT (Nghe)** | **Google Cloud STT v2 (Chirp)** | Mô hình 2 tỷ tham số của Google, nhận diện chuẩn xác tiếng Anh của người Việt (chống nuốt âm cuối, chống ngọng ngữ điệu). |
| **Tầng LLM (Bộ não)** | **`gemini-3.8-flash`**<br>`thinkingLevel: "medium"` | Đóng vai nhân vật (Barista/Lễ tân), sinh bản dịch tiếng Việt (`translation_vi`), đánh giá sư phạm và tính điểm CSI. Phản hồi mượt mà sau 0.8s – 1.2s. |
| **Tầng TTS (AI phát âm)** | **Google Cloud TTS (Neural2 / Journey)** | Giọng đọc truyền cảm, ấm áp chuẩn người bản xứ. Tự động chỉnh tốc độ theo cấp độ (A1: 0.85x, A2: 0.92x, B1-B2: 1.0x). |
| **Giao tiếp Realtime** | **WebRTC Dual-Channel** | • Kênh Audio: Độ trễ <150ms, chống vọng tiếng (Hardware AEC).<br>• Kênh DataChannel: Bắn phụ đề và bản dịch tiếng Việt tức thì. Triển khai qua LiveKit Cloud (Free 10k phút). |

---

## 2. CHI TIẾT HẠ TẦNG CƠ SỞ DỮ LIỆU: MYSQL 8.0

### 2.1. Tại sao MySQL 8.0 là lựa chọn hoàn hảo cho dự án?
1. **Khóa cấp dòng (Row-level Locking) với InnoDB:**
   * Khắc phục hoàn toàn nhược điểm lớn nhất của SQLite (khóa toàn bộ file DB khi có 1 tiến trình ghi). Với MySQL InnoDB, hàng trăm người học có thể cùng bấm kết thúc lượt thoại, lưu điểm số CSI và chạy background queue đồng thời mà không bao giờ gặp lỗi nghẽn.
2. **Hỗ trợ kiểu dữ liệu JSON Native mạnh mẽ:**
   * Trong dự án, các bảng `practice_sessions` và `dialogue_turns` lưu trữ các trường JSON quan trọng: `word_analysis` (tô màu từ 🟩 🟧 🟥), `keywords_data`, `supported_levels`.
   * MySQL 8.0 hỗ trợ native JSON documents với các hàm tối ưu: `JSON_EXTRACT()`, `JSON_CONTAINS()`, giúp truy vấn nhanh và tiết kiệm dung lượng lưu trữ.
3. **Bộ mã UTF8MB4 toàn diện:**
   * Lưu trữ hoàn hảo tiếng Việt có dấu, ký tự phiên âm IPA quốc tế (`/ˈlɑː.teɪ/`), và emoji giao diện (`☕`, `👤`, `🇻🇳`).
4. **Hệ sinh thái lưu trữ khổng lồ:**
   * 100% các nền tảng Cloud hiện nay (AWS RDS for MySQL, Google Cloud SQL for MySQL, DigitalOcean Managed MySQL, PlanetScale, hoặc tự cài MySQL trên VPS) đều hỗ trợ sẵn, dễ dàng sao lưu tự động và phục hồi sau sự cố.

### 2.2. Cấu hình Container MySQL 8.0 trong Docker Compose
Hạ tầng Docker của dự án đã được định nghĩa sẵn service `db` chạy MySQL 8.0:
* **Container Name:** `ai_simulator_db`
* **Image:** `mysql:8.0`
* **Port Mapping:** Host `3309` $\rightarrow$ Container `3306` (Ánh xạ cổng 3309 để tránh xung đột nếu máy bạn đã có sẵn MySQL 3306).
* **Network Alias:** Container `app` gọi nội bộ qua hostname `db:3306`.
* **Persistent Volume:** `ai_simulator_mysql_data` đảm bảo dữ liệu không bao giờ bị mất khi tắt mở container.
* **Command tối ưu:**
  ```yaml
  command: --default-authentication-plugin=mysql_native_password --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci
  ```

### 2.3. Chuyển đổi giữa SQLite và MySQL trong 1 bước:
Do toàn bộ backend viết bằng chuẩn Repository Pattern + Eloquent ORM:
```env
# Cấu hình kết nối MySQL trong .env:
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
FORWARD_DB_PORT=3309
DB_DATABASE=ai_simulator
DB_USERNAME=simulator_user
DB_PASSWORD=simulator_password
DB_ROOT_PASSWORD=root_password
```
Chạy lệnh migrate: `php artisan migrate:fresh --seed` là xong 100%!

---

## 3. CHIẾN LƯỢC HẠ TẦNG LƯU TRỮ FILE & AUDIO (OBJECT STORAGE)

Trong ứng dụng luyện nói, người học cần **nghe lại giọng của chính mình** ở Màn hình 4 (Summary Inspector):
* **Vấn đề của AWS S3:** Thu phí băng thông tải ra (Egress Fees) rất đắt mỗi khi người dùng bấm nghe lại file ghi âm.
* **Giải pháp chuẩn hóa: Cloudflare R2:**
  * **0 ĐỒNG CHI PHÍ BĂNG THÔNG TẢI RA (Zero Egress Fees):** Nghe lại bao nhiêu lần cũng không tốn tiền băng thông.
  * **10GB lưu trữ miễn phí/tháng** (đủ lưu trữ hàng trăm nghìn file ghi âm giọng nói).
  * **Tương thích 100% chuẩn S3:** Laravel dùng driver `s3` trỏ tới endpoint của Cloudflare R2.
* **Giai đoạn Local:** Lưu trực tiếp tại ổ cứng máy tính (`storage/app/public`). Khi Public chỉ cần bật cấu hình R2 vào `.env`.

---

## 4. BẢN ĐỒ CONTAINER DOCKER & CỔNG DỊCH VỤ CỤ THỂ

Toàn bộ cụm hạ tầng Docker Compose hoàn chỉnh của dự án:

```
[Máy chủ của bạn / Local Host]
  │
  ├── 1. Container: ai_simulator_nginx (Cổng Host 8002 -> Container 80)
  │      └── Phục vụ giao diện Web, CSS, JS, hình ảnh, reverse proxy.
  │
  ├── 2. Container: ai_simulator_app (PHP 8.2-FPM)
  │      └── Chạy Laravel 11, kết nối MySQL (db:3306).
  │      └── Gọi Google AI Library (Gemini 3.8 Flash).
  │
  ├── 3. Container: ai_simulator_db (MySQL 8.0 - Cổng Host 3309 -> Container 3306)
  │      └── Cơ sở dữ liệu chính: ai_simulator (InnoDB utf8mb4).
  │      └── Volume lưu trữ bền vững: ai_simulator_mysql_data.
  │
  ├── 4. Container: ai_simulator_queue (PHP CLI Worker)
  │      └── Chạy hàng đợi ngầm xử lý nén ảnh WebP, tính toán điểm số nặng.
  │
  └── 5. [WebRTC Voice Gateway]: LiveKit Cloud (Free 10k phút/tháng)
         └── Điều phối luồng âm thanh thời gian thực mà bạn không cần quản lý máy chủ.
```

---

## 5. BẢNG BIẾN MÔI TRƯỜNG (.ENV) HOÀN CHỈNH (MYSQL CHUẨN)

```env
# ==============================================================================
# 1. APPLICATION CORE
# ==============================================================================
APP_NAME="AI Real-World English Simulator"
APP_ENV=local                       # local | staging | production
APP_KEY=base64:ovHYVSthMjLNzQmcC3jGIr/R199wh7wa9/GoJZ5iskM=
APP_DEBUG=true                      # Đặt false khi public
APP_URL=http://localhost:8002       # Đổi thành https://your-domain.com khi public
APP_PORT=8002

# ==============================================================================
# 2. DATABASE CONFIGURATION (MYSQL 8.0 CHÍNH THỨC)
# ==============================================================================
DB_CONNECTION=mysql
DB_HOST=db                          # Tên service trong docker network
DB_PORT=3306                        # Cổng nội bộ container
FORWARD_DB_PORT=3309                # Cổng ánh xạ ra máy host (tránh trùng 3306)
DB_DATABASE=ai_simulator
DB_USERNAME=simulator_user
DB_PASSWORD=simulator_password
DB_ROOT_PASSWORD=root_password

# ==============================================================================
# 3. FILE STORAGE (LOCAL DISK -> PUBLIC CLOUDFLARE R2)
# ==============================================================================
# Mặc định Local:
FILESYSTEM_DISK=public

# Khi Public lên Internet (Cloudflare R2 S3-Compatible):
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=your_r2_access_key
# AWS_SECRET_ACCESS_KEY=your_r2_secret_key
# AWS_DEFAULT_REGION=auto
# AWS_BUCKET=ai-english-simulator-audio
# AWS_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com

# ==============================================================================
# 4. AI & SPEECH APIS (GOOGLE CLOUD & GEMINI)
# ==============================================================================
GEMINI_API_KEY=your_google_ai_studio_api_key_here
GEMINI_MODEL=gemini-3.8-flash
GEMINI_THINKING_LEVEL=medium

GOOGLE_CLOUD_PROJECT_ID=your-gcp-project-id
GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/storage/credentials/google-service-account.json
STT_DRIVER=google_chirp             # google_chirp | web_speech
TTS_DRIVER=google_neural2           # google_neural2 | google_journey

# ==============================================================================
# 5. WEBRTC REALTIME ENGINE (LIVEKIT)
# ==============================================================================
LIVEKIT_URL=wss://your-project.livekit.cloud
LIVEKIT_API_KEY=your_livekit_api_key
LIVEKIT_API_SECRET=your_livekit_api_secret

# ==============================================================================
# 6. SƯ PHẠM & CSI SCORING CONFIGURATION
# ==============================================================================
SIMULATOR_CSI_WEIGHT_RESOLUTION=0.40
SIMULATOR_CSI_WEIGHT_ADAPTABILITY=0.35
SIMULATOR_CSI_WEIGHT_FLOW=0.25
SIMULATOR_WORD_SCORE_GREEN=85
SIMULATOR_WORD_SCORE_ORANGE=60
SIMULATOR_REDO_MAX_ATTEMPTS=2
SIMULATOR_REDO_COOLDOWN_SECONDS=1.5
SIMULATOR_THINKING_DURATION_SECONDS=15
SIMULATOR_THINKING_HINT_REMINDER_SECONDS=8
```

---

## 6. LỘ TRÌNH MÔI TRƯỜNG TỪ LOCAL ĐẾN PUBLIC

```
┌─────────────────────────────────┐      ┌─────────────────────────────────┐      ┌─────────────────────────────────┐
│   NẤC 1: LOCAL DEV (Hiện tại)   │      │   NẤC 2: PUBLIC STAGING / MVP   │      │   NẤC 3: PRODUCTION SCALE       │
├─────────────────────────────────┤      ├────────────────────────────────┤      ├─────────────────────────────────┤
│ • Docker Compose cổng 8002      │      │ • 1 VPS giá rẻ ($5 - $6/tháng)  │      │ • Serverless Auto-scale         │
│ • Database: MySQL 8.0 (3309)    │ ──►  │ • Triển khai tự động (Coolify)  │ ──►  │   (Google Cloud Run)            │
│ • Storage: Local Disk           │      │ • Database: MySQL 8.0           │      │ • DB: Cloud SQL for MySQL       │
│ • WebRTC: LiveKit Cloud Free    │      │ • Storage: Cloudflare R2        │      │ • Storage: Cloudflare R2 + CDN  │
│ • Chi phí: 0 VNĐ                │      │ • Chi phí: ~150.000 VNĐ / tháng │      │ • Chi phí: Theo lưu lượng thực  │
└─────────────────────────────────┘      └─────────────────────────────────┘      └─────────────────────────────────┘
```

* **Tại Nấc 2 (Public MVP):** 
  * Bạn chỉ cần 1 chiếc VPS nhỏ (Hetzner / DigitalOcean) cài Coolify.
  * Cơ sở dữ liệu MySQL chạy trực tiếp trên VPS.
  * Toàn bộ mã nguồn, migrations và dữ liệu chuyển thẳng từ máy của bạn lên VPS 100% nguyên vẹn mà không có bất kỳ xung đột môi trường nào.

---
*Tài liệu này xác lập MySQL 8.0 là cơ sở dữ liệu chính thức, chuẩn hóa toàn diện hạ tầng của dự án.*
