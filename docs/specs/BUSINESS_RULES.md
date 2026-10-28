# TẬP QUY TẮC NGHIỆP VỤ HỆ THỐNG
## (SYSTEM BUSINESS RULES SPECIFICATION - BRD)

* **Dự án:** AI-Powered Real-World English Communication Simulator
* **Mã tài liệu:** BR-SIM-v1.2 (User-Centric & Gentle Experience)
* **Tài liệu tham chiếu:** [BUSINESS_ANALYSIS.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/BUSINESS_ANALYSIS.md) | [SYSTEM_LOGIC_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/SYSTEM_LOGIC_SPECIFICATION.md)
* **Trạng thái:** Active / Baseline

---

## 1. NGUYÊN TẮC THIẾT KẾ TRẢI NGHIỆM (DESIGN PHILOSOPHY)
* **Mềm mại & Không áp lực (Gentle & Safe Space):** Tránh các thuật ngữ tạo cảm giác thi cử như "bắt buộc", "mục tiêu tối thượng", "hình phạt". Ứng dụng là một không gian an toàn để người học thử nghiệm phản xạ nói.
* **Gợi ý tự nhiên thay vì Gò ép (Subtle Nudges over Hard Gates):** Mục tiêu trong phiên đóng vai trò là "kim chỉ nam gợi ý", không áp đặt trình tự cứng nhắc.

---

## 2. NHÓM BR-SCA: GIÀN GIÁO GỢI Ý (SCAFFOLDING RULES)

### `BR-SCA-01` (Cấm sinh kịch bản hoàn chỉnh - Anti-Parrot Rule)
* Hệ thống không cung cấp nguyên văn câu tiếng Anh hoàn chỉnh trong toàn bộ phần gợi ý.
* Mục đích: Kích thích não bộ tự vận động lắp ráp câu, tạo phản xạ ngôn ngữ thực chất.

### `BR-SCA-02` (Cấu trúc gợi ý 3 thành phần & Hỗ trợ âm thanh)
Mọi phương án gợi ý hiển thị theo định dạng 3 thành phần:
1. **Ý định giao tiếp (Intent):** Diễn đạt ý tứ bằng tiếng Việt một cách tự nhiên.
2. **Khung câu linh hoạt (Sentence Frame):** Mẫu ngữ pháp chứa vị trí trống `[...]`.
3. **Từ khóa có âm thanh mẫu (Keyword Bank with Audio):** Tối thiểu 2 từ/cụm từ liên quan kèm phiên âm IPA và **nút nghe phát âm mẫu [🔊]**.

### `BR-SCA-03` (Đa dạng hóa hướng phản hồi)
* Cung cấp từ 2 đến 3 hướng phản hồi mang sắc thái khác nhau (vd: Đồng thuận, Từ chối nhẹ nhàng, hoặc Hỏi thêm thông tin) để người học lựa chọn theo cảm xúc tự nhiên.

---

## 3. NHÓM BR-MIS: MỤC TIÊU GỢI Ý & ĐÁNH GIÁ (OUTCOME & CSI RULES)

### `BR-MIS-01` (Hội thoại phi tuyến tính & Linh hoạt)
* Người học hoàn toàn tự do dẫn dắt câu chuyện: hỏi ngoài lề, từ chối, đổi ý, nói chuyện phiếm nhẹ. AI nương theo dòng cảm xúc để đối đáp chân thực như đời sống.

### `BR-MIS-02` (Hoàn thành tự nhiên - Natural Resolution)
* Phiên kết thúc tự nhiên khi cuộc trao đổi đạt được kết quả mà hai bên cảm thấy thoải mái (vd: chốt được món uống hoặc nói lời chào tạm biệt), không ép buộc phải đi hết toàn bộ nội dung kịch bản.

### `BR-MIS-03` (Công thức Chỉ số Thành công Giao tiếp - CSI)
$$\text{CSI Score} = (\text{Resolution} \times 0.4) + (\text{Adaptability} \times 0.35) + (\text{Flow & Fluency} \times 0.25)$$

* **Resolution (40% - Kết quả trao đổi):**
  - Đạt điểm cao khi hai bên hiểu nhau và hoàn tất được mục đích trao đổi chính một cách tự nhiên.
* **Adaptability (35% - Độ thích ứng & Liên quan ngữ cảnh):**
  - **Ràng buộc liên quan ngữ cảnh (Contextual Relevance):** Dù người học nói lưu loát, nhưng nếu liên tục nói về các chủ đề hoàn toàn không liên quan đến bối cảnh chung $\rightarrow$ Điểm Adaptability sẽ không được đánh giá cao.
  - Đánh giá khả năng lắng nghe và phản ứng linh hoạt trước câu hỏi/biến số từ AI.
* **Flow & Fluency (25% - Nhịp điệu & Độ dài phù hợp):**
  - *Tính thích ứng về dung lượng:* Độ dài ngắn không đồng nghĩa với kém cỏi.
    - Một người gọi món dứt khoát trong 3 câu đạt điểm tốt vì sự gãy gọn.
    - Một người giao lưu, hỏi han thêm về các loại bánh trong 12–15 lượt đối đáp vẫn đạt điểm tối đa nếu duy trì được nhịp điệu tự nhiên và đúng bối cảnh.
  - Điểm chỉ giảm khi có sự ngắt quãng kéo dài liên tục hoặc rơi vào vòng lặp lúng túng.

---

## 4. NHÓM BR-EVA: ĐÁNH GIÁ NGỮ ÂM PHÂN TẦNG (PRONUNCIATION RULES)

### `BR-EVA-01` (Phân định Từ nội dung vs Từ chức năng)
* **Từ nội dung (Content Words):** Soi xét các âm tiết then chốt và trọng âm chính của danh từ, động từ mang nghĩa cốt lõi.
* **Từ chức năng (Function Words):** Bỏ qua lỗi âm lẻ; khuyến khích hiện tượng nuốt âm/nối âm mềm mại (`gonna`, `wanna`, `could ya`).

### `BR-EVA-02` (Cơ chế góp ý phát âm mang tính xây dựng)
* 🟥 **Đỏ (Cần lưu ý):** Từ nội dung phát âm sai làm biến đổi ý nghĩa hoặc mất âm đuôi quan trọng (`/s/`, `/z/`, `/ed/`).
* 🟧 **Cam (Gợi ý cải thiện):** Từ nhận diện được nhưng độ rõ ràng chưa cao.
* 🟩 **Xanh (Tự nhiên):** Đạt độ chuẩn xác và tự nhiên.

---

## 5. NHÓM BR-REP: LUYỆN LẠI THEO LÁT CẮT (RE-PRACTICE RULES)

### `BR-REP-01` (Lát cắt hội thoại 3 bước)
* Trích xuất lượt thoại mà người học có thể làm tốt hơn (nhiều từ cần chú ý nhất hoặc ngẫm nghĩ lâu nhất).
* Luyện lại nhẹ nhàng trong dòng chảy: *AI nhắc lại $\rightarrow$ Người học thử cách diễn đạt mới $\rightarrow$ AI phản hồi xác nhận*.

### `BR-REP-02` (Đo lường tiến bộ tinh thần - Delta Progression)
* Ghi nhận sự tự tin tăng thêm ($\Delta$) sau khi luyện lại, tạo động lực tích cực thay vì phán xét điểm số cũ.

---

## 6. NHÓM BR-EXC: QUẢN LÝ TƯƠNG TÁC, CHỐNG SPAM & TRẢI NGHIỆM ÊM DỊU

### `BR-EXC-01` (Cửa sổ Suy nghĩ Thư thả 10–15s & Nút Gửi Chủ Động)
* Hệ thống giữ trạng thái lắng nghe trong vòng **10 – 15 giây** để người học thoải mái định hình câu nói mà không bị cướp lời.
* Khi nói xong, người học chủ động nhấn **`[Xong / Gửi]`** để AI phản hồi tức thì ($\le 2.5s$).

### `BR-EXC-02` (Logic Nói Lại Không Đơ Thiết Bị & Chống Spam AI)
* **Xử lý thiết bị:** Khi bấm `[Nói lại / Redo]`, hệ thống chỉ **xóa bộ nhớ đệm chữ (Transcript Buffer)** và mở lại luồng thu âm, **tuyệt đối không khởi tạo lại phần cứng/xin lại quyền mic** để tránh làm đơ giật giao diện.
* **Bộ đệm chuẩn bị & Chống Spam:**
  - Sau khi bấm `[Nói lại]`, giao diện hiển thị hiệu ứng chuẩn bị nhẹ (1.5 – 2.0s) để người học hít thở và sẵn sàng.
  - Người học phải chủ động bấm nút gửi khi nói xong (không tự động bắn request liên tục), bảo vệ tài nguyên AI và tránh người học bấm loạn xạ.
  - Giới hạn tối đa **2 lần bấm Nói lại** cho mỗi lượt thoại.

### `BR-EXC-03` (Ứng xử khi chêm tiếng khác / Tiếng Việt - Không Nhắc Nhở Vụn Vặt)
* **Quy tắc chống lặp (Anti-Nagging):** Khi người học lỡ chêm từ tiếng Việt hoặc ngoại ngữ khác, **AI tuyệt đối không ngắt lời hay liên tục nhắc "Hãy nói tiếng Anh"**.
* **Cách xử lý tự nhiên:** AI vẫn nương theo những từ tiếng Anh bắt được để phản hồi tiếp một cách lịch sự, giữ cho cuộc trò chuyện không bị sượng.
* **Ghi nhận đánh giá:** Sự xuất hiện của tiếng mẹ đẻ sẽ được **tính nhẹ nhàng vào chỉ số Độ trôi chảy & Tự nhiên (Flow & Fluency)** ở bảng tổng kết sau cùng, kèm lời khuyên thân thiện.

### `BR-EXC-04` (Cắt lời AI tự nhiên - Barge-in)
* Khi AI đang nói, người học có thể bắt đầu cất giọng hoặc bấm mic $\rightarrow$ AI lập tức dừng phát âm thanh để nhường quyền nói cho người học.
