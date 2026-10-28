# ĐẶC TẢ LOGIC VẬN HÀNH HỆ THỐNG
## (SYSTEM LOGIC & INTERACTION SPECIFICATION - SLS)

* **Dự án:** AI-Powered Real-World English Communication Simulator
* **Mã tài liệu:** SLS-v1.2 (User-Centric & Gentle Experience)
* **Tài liệu tham chiếu:** [BUSINESS_ANALYSIS.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/BUSINESS_ANALYSIS.md) | [BUSINESS_RULES.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/BUSINESS_RULES.md)
* **Người thực hiện:** Senior Business Analyst / Requirements Specialist

---

## 1. GIAI ĐOẠN 1: CHỌN CHỦ ĐỀ & KHỞI TẠO PHIÊN

* **Trigger:** Người dùng click vào thẻ kịch bản mong muốn tại Trang chủ.
* **Tiền kiểm thân thiện (Preflight Check):**
  - Kiểm tra trình duyệt (nhân Chromium). Nếu cần mic $\rightarrow$ hiển thị lời nhắc cấp quyền nhẹ nhàng.
* **Khởi tạo phiên:**
  - Sinh mã `sessionId`.
  - Nạp bối cảnh mở: Persona nhân vật, Ý định gợi ý (Suggested Intent), và tình huống phát sinh thực tế nhẹ.
  - Chuyển trạng thái sang `ACTIVE` một cách mượt mà.

---

## 2. GIAI ĐOẠN 2: NẠP BỐI CẢNH & AI MỞ LỜI

### 2.1. Logic giao diện & Trạng thái mở màn (Nhẹ nhàng & Tự nhiên):
* **Bỏ hoàn toàn "Thanh Mục tiêu tối thượng":** Giao diện không dùng các từ ngữ áp lực như "Nhiệm vụ bắt buộc", "Mục tiêu tối thượng".
* **Gợi ý bối cảnh tinh tế (Subtle Context Note):** 
  - Chỉ hiển thị một dòng mô tả bối cảnh ngắn gọn, trang nhã ở góc trên (ví dụ: *"Bối cảnh: Một buổi sáng ghé quán cà phê quen thuộc..."*).
  - Có thể thu gọn hoặc mở ra xem ý định gợi ý bất kỳ lúc nào mà không gây choáng ngợp tầm nhìn.
* **Lời chào tự nhiên từ AI:**
  - AI phát câu chào mở màn bằng giọng đọc truyền cảm.
  - Avatar chuyển trạng thái `SPEAKING`.
  - Khi AI dứt lời $\rightarrow$ Avatar chuyển `LISTENING`, mở mic và bắt đầu cho phép người dùng tương tác.

---

## 3. GIAI ĐOẠN 3: VÒNG LẶP HỘI THOẠI PHẢN XẠ (CORE LOOP)

```
                  ┌── [AI dứt lời: Mở Mic & Kích hoạt Cửa sổ suy nghĩ 10-15s] ──┐
                  │                                                             │
                  ▼                                                             ▼
[Hành vi A: Đang ngẫm nghĩ]                                       [Hành vi B: Người dùng cất giọng nói]
  ├── Không ngắt mic vội, giữ sự kiên nhẫn                          ├── Live transcript hiển thị chữ mượt mà
  ├── Sau 8s: Nháy nhẹ nút [💡 Gợi ý]                               ├── Nút [Xong / Gửi] sáng rõ để bấm chủ động
  └── Sau 15s im lặng: Khẽ mở gợi ý trợ giúp                        └── Nút [Nói lại] sẵn sàng hỗ trợ
                                                                                │
                                           ┌────────────────────────────────────┴────────────────────────────────────┐
                                           ▼                                                                         ▼
                                 [Bấm "Xong / Gửi"]                                                        [Bấm "Nói lại / Redo"]
                        -> Đóng mic, gửi câu đi ngay (Độ trễ AI ≤ 2.5s)                           -> Reset bộ đệm, đếm chuẩn bị 1.5s
                                                                                                     Người dùng tự bấm gửi khi sẵn sàng
```

### 3.1. Cửa sổ suy nghĩ thư thả (10–15s):
* Tôn trọng nhịp suy nghĩ của người mới học. Người học có thể ngập ngừng vài giây để chọn từ mà không sợ bị hệ thống vội vã cắt ngang câu.

### 3.2. Logic "Nói lại" (Redo) An Toàn & Chống Spam Tài Nguyên AI (`BR-EXC-02`):
* **Không làm đơ thiết bị:** Bấm `[Nói lại]` chỉ **xóa bộ đệm chữ (Transcript Buffer)** và mở lại luồng nghe, tuyệt đối không gọi lại hàm khởi tạo phần cứng hay xin lại quyền mic.
* **Bộ đệm chuẩn bị 1.5s (Preparation Cooldown):**
  - Khi bấm `[Nói lại]`, màn hình hiển thị nhịp đếm chuẩn bị nhẹ (1.5 giây) để người học hít thở và định thần.
  - Trong 1.5s này, nút gửi bị vô hiệu hóa tạm thời để tránh người dùng click spam liên tục.
* **Quyền chủ động gửi:** Sau khi nói lại, hệ thống để người dùng **chủ động bấm `[Xong / Gửi]`** khi thực sự hài lòng (tránh việc mic tự gửi các âm thanh rè hoặc tiếng thở dài, gây lãng phí request gọi AI).
* Giới hạn tối đa **2 lần bấm Nói lại/lượt thoại**.

### 3.3. Cắt lời AI tự nhiên (Barge-in):
* Bất cứ lúc nào AI đang nói, nếu người học cất giọng hoặc chạm mic $\rightarrow$ AI lập tức ngừng nói để lắng nghe người học.

---

## 4. GIAI ĐOẠN 4: BỘ NÃO ĐIỀU PHỐI NGẦM (STATE ENGINE)

### 4.1. Ứng xử với Tiếng Việt / Ngoại ngữ khác (Chống Vòng Lặp Nhắc Nhở - `BR-EXC-03`):
* **Vấn đề cần tránh:** Không để AI liên tục ngắt lời và nhắc đi nhắc lại câu cứng nhắc *"Hãy nói tiếng Anh"*, gây ức chế và làm hỏng dòng cảm xúc giao tiếp.
* **Logic xử lý tự nhiên:**
  - AI bỏ qua từ tiếng Việt chêm vào, bắt lấy các từ khóa tiếng Anh có nghĩa để tiếp tục đối đáp theo bối cảnh.
  - **Chuyển về đánh giá sau phiên:** Việc chêm tiếng mẹ đẻ sẽ được hệ thống âm thầm ghi nhận và đưa vào điểm số **Độ trôi chảy & Tự nhiên (Flow & Fluency)** ở Màn hình Tổng kết cuối cùng, kèm theo gợi ý nhẹ nhàng cách thay thế từ tiếng Việt đó bằng tiếng Anh.

### 4.2. Logic Thích ứng Ngữ cảnh Thực tế (Contextual Adaptation):
* AI đưa ra 1 tình huống phát sinh nhẹ ở giữa phiên (ví dụ: quán hết loại sữa quen thuộc).
* **Kiểm tra độ liên quan ngữ cảnh (Contextual Relevance):**
  - Hệ thống chỉ đánh giá cao điểm `Adaptability` khi câu trả lời của người học **thực sự gắn liền với bối cảnh giao dịch**.
  - Nếu người học nói rất lưu loát nhưng lại nói về thời tiết, chính trị hay chủ đề không ăn nhập $\rightarrow$ Điểm Adaptability sẽ được điều chỉnh giảm phù hợp, đồng thời AI dùng kỹ thuật *Bridge & Pivot* kéo câu chuyện về lại quán cà phê.

### 4.3. Kết thúc phiên tự nhiên:
* Khi giao dịch đã hoàn tất một cách thoải mái cho cả hai bên, AI gửi lời chúc tốt lành và hệ thống chuyển nhẹ nhàng sang màn hình tổng kết.

---

## 5. GIAI ĐOẠN 5: TỔNG KẾT & CHẤM ĐIỂM CSI (EVALUATION)

### 5.1. Công thức CSI Thích Ứng & Linh Hoạt:
$$\text{CSI} = (\text{Resolution} \times 0.4) + (\text{Adaptability} \times 0.35) + (\text{Flow & Fluency} \times 0.25)$$

* **Resolution (40%):** Hoàn thành được mục đích trao đổi chính một cách tự nhiên.
* **Adaptability (35% - Độ thích ứng theo ngữ cảnh):**
  - Đánh giá khả năng hiểu và phản hồi hợp lý với tình huống phát sinh.
  - **Khóa tiêu chí liên quan:** Không cộng điểm cho những câu nói trôi chảy nhưng lạc đề hoàn toàn.
* **Flow & Fluency (25% - Nhịp điệu & Dung lượng linh hoạt):**
  - **Tính mềm mại về độ dài:** Không áp đặt quy tắc cứng rằng "nói ngắn là giỏi" hay "nói dài là kém".
    - Người mua cà phê dứt khoát trong 3 câu: Đạt điểm cao vì sự mạch lạc, gãy gọn.
    - Người trao đổi hỏi han kỹ lưỡng qua 12–15 câu: Vẫn đạt điểm tối đa nếu duy trì được nhịp điệu tự nhiên, từ vựng phong phú và đúng bối cảnh.
  - Chỉ trừ điểm khi có hiện tượng ngắt quãng quá lâu hoặc chêm nhiều tiếng mẹ đẻ.

### 5.2. Đánh giá Ngữ âm Phân tầng & Trích xuất Lát cắt:
* Tô màu nhẹ nhàng: 🟩 Xanh (Tự nhiên), 🟧 Cam (Gợi ý cải thiện), 🟥 Đỏ (Lưu ý phát âm rõ hơn).
* Tìm ra **Lát cắt người học có thể làm tốt hơn** (nơi có nhiều từ cần chú ý nhất) để đưa vào phần thực hành lại.

---

## 6. GIAI ĐOẠN 6: LUYỆN LẠI LÁT CẮT & GHI NHẬN TIẾN BỘ

### 6.1. Chu trình Luyện lại 3 bước nhẹ nhàng:
1. AI gợi mở lại câu thoại trước đó.
2. Người học thử nghiệm cách diễn đạt mới (có thể xem gợi ý câu tự nhiên hơn của người bản ngữ).
3. AI phản hồi một câu thực tế để khích lệ.
* Hiển thị chỉ số tiến bộ tinh thần: *"+15% tự tin hơn sau lượt này!"*.

### 6.2. Lưu trữ tiến độ cá nhân:
* Lưu thông số phiên vào `localStorage` trên thiết bị để người học tự theo dõi hành trình tiến bộ của mình qua từng ngày.

---
*Bản đặc tả logic này đảm bảo toàn bộ hệ sinh thái tính năng vận hành trơn tru, bảo vệ tài nguyên AI, không gây lỗi thiết bị và mang lại trải nghiệm học tập ấm áp, không áp lực.*
