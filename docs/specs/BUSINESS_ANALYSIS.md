# BẢN PHÂN TÍCH NGHIỆP VỤ SẢN PHẨM
## (PRODUCT BUSINESS ANALYSIS - BA)

* **Tên dự án:** AI-Powered Real-World English Communication Simulator
* **Phiên bản:** 1.2 (User-Centric & Gentle Experience)
* **Tài liệu quy tắc kèm theo:** [BUSINESS_RULES.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/BUSINESS_RULES.md) | [SYSTEM_LOGIC_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/SYSTEM_LOGIC_SPECIFICATION.md)
* **Người thực hiện:** Senior Business Analyst / Requirements Specialist

---

## 1. BỐI CẢNH & TRIẾT LÝ SẢN PHẨM

### 1.1. Triết lý thiết kế (Product Philosophy)
* **Không gian an toàn, không phán xét (Psychological Safety):** Loại bỏ hoàn toàn áp lực thi cử, gạt bỏ những từ ngữ mang tính đe dọa như "bắt buộc", "mục tiêu tối thượng", "hình phạt". Ứng dụng là một người bạn đồng hành kiên nhẫn, nơi người học được quyền thử nghiệm và nói sai mà không cảm thấy xấu hổ.
* **Gợi ý tự nhiên thay vì gò ép (Gentle Guidance over Rigidity):** Mục tiêu trong phiên chỉ mang tính định hướng bối cảnh; người học hoàn toàn tự do chọn nhịp điệu ngắn hay dài, miễn là đạt được sự thoải mái trong giao tiếp.

### 1.2. Nỗi đau thực tế của người học
* **Sợ bị cướp lời khi đang ngẫm nghĩ:** Người mới học thường cần 5 – 10 giây để sắp xếp từ trong đầu. Các ứng dụng ngắt mic quá sớm khiến họ hoảng loạn và ức chế.
* **Bị ngắt mạch hội thoại vì nhắc nhở vụn vặt:** Khi lỡ chêm một từ tiếng Việt, việc AI liên tục dừng lại để nhắc "Hãy nói tiếng Anh" tạo cảm giác bị giám sát và làm đứt gãy mạch cảm xúc.
* **Học vẹt phụ đề:** Cung cấp sẵn câu mẫu hoàn chỉnh khiến não bộ lười vận động, không tạo ra phản xạ ngôn ngữ thực chất ngoài đời.

---

## 2. CHÂN DUNG NGƯỜI DÙNG & MÔ HÌNH TRẢI NGHIỆM

```
┌───────────────────────────────────────────────────────────────┐
│                      CHÂN DUNG NGƯỜI HỌC                      │
├──────────────────┬────────────────────────────────────────────┤
│ Trình độ nền     │ Biết từ vựng sơ cấp (A1 - A2); có thể chưa │
│                  │ thạo đọc phiên âm ký tự IPA phức tạp.      │
├──────────────────┼────────────────────────────────────────────┤
│ Tâm lý tiếp cận  │ Cần sự nhẹ nhàng, không muốn bị gò bó vào  │
│                  │ các checklist nhiệm vụ nặng nề.            │
├──────────────────┼────────────────────────────────────────────┤
│ Kỳ vọng cốt lõi  │ Được kiên nhẫn lắng nghe, có gợi ý hỗ trợ  │
│                  │ âm thanh [🔊], và được sửa lỗi tinh tế.    │
└──────────────────┴────────────────────────────────────────────┘
```

---

## 3. KHUNG SƯ PHẠM ĐỊNH HÌNH GIẢI PHÁP (5 TRỤ CỘT ÊM DỊU)

1. **Khung câu & Âm thanh mẫu (Frames & Audio over Scripts):** Chỉ gợi ý ý định tiếng Việt + Khung câu rỗng `[...]` + Từ khóa có nút nghe mẫu [🔊]. Kích thích não bộ tự ráp câu mà không cần đọc vẹt.
2. **Cửa sổ Suy nghĩ Thư thái (10–15s):** Tôn trọng nhịp suy nghĩ tự nhiên. Người học chủ động nhấn `[Xong / Gửi]` khi sẵn sàng; bấm `[Nói lại]` có bộ đệm chuẩn bị 1.5s để chống spam và giữ mic mượt mà.
3. **Hoàn thành Tự nhiên (Natural Resolution):** Cuộc trò chuyện kết thúc êm đẹp khi hai bên hiểu nhau, không gò ép theo checklist cứng nhắc.
4. **Ứng xử Ngôn ngữ Tinh tế (Graceful Multilingual Handling):** Lỡ chêm tiếng Việt thì AI vẫn trò chuyện tiếp tự nhiên bằng tiếng Anh; việc dùng tiếng mẹ đẻ được phản hồi khéo léo ở bảng tổng kết sau phiên.
5. **Đánh giá Thích ứng theo Ngữ cảnh (Context-Aware Evaluation):** Chấm điểm cao cho sự liên quan và phản xạ phù hợp với tình huống; linh hoạt dung lượng ngắn (3 câu) hay dài (15 câu) tùy nhịp điệu người học.

---

## 4. PHẠM VI DỰ ÁN TINH GỌN (LEAN MVP SCOPE)

| Trong phạm vi MVP (In-Scope) | Ngoài phạm vi MVP (Lộ trình sau) |
| :--- | :--- |
| - Hội thoại giọng nói 2 chiều (Voice In - Voice Out). | - Quản lý tài khoản nhiều người dùng & Cloud DB. |
| - 3 Kịch bản Hỗn hợp cốt lõi (Cà phê, Sân bay, Khách sạn) có tình huống phát sinh thực tế nhẹ. | - Ma trận 3 biến thể độc lập cho mỗi chủ đề ($3 \times 3 = 9$ scenarios). |
| - Cửa sổ suy nghĩ 10 - 15s + Nút chủ động [Xong/Gửi]. | - Trình biên tập kịch bản tùy biến. |
| - Logic Nói lại chống spam (Đệm chuẩn bị 1.5s). | - Mô hình 3D tương tác hoặc Video Deepfake lip-sync. |
| - Gợi ý giàn giáo kèm âm thanh từ khóa [🔊]. | - Phòng luyện phát âm âm vị học chuyên sâu. |
| - Đánh giá CSI thích ứng & Gợi ý cách nói bản xứ. | - Đồng bộ tiến độ đa thiết bị. |
| - Luyện lại lát cắt hội thoại 3 bước nhẹ nhàng. | |

---

## 5. BẢNG TỔNG HỢP YÊU CẦU NGHIỆP VỤ CỐT LÕI

Mỗi yêu cầu nghiệp vụ dưới đây được liên kết trực tiếp với [BUSINESS_RULES.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/BUSINESS_RULES.md).

```
┌─────────────┬──────────────────────────────────────────┬───────────────────┐
│ Mã yêu cầu  │ Tên yêu cầu nghiệp vụ                    │ Quy tắc ràng buộc │
├─────────────┼──────────────────────────────────────────┼───────────────────┤
│ REQ-SCA-01  │ Giàn giáo Khung câu & Audio Từ khóa      │ BR-SCA-01, 02, 03 │
│ REQ-VOI-01  │ Cửa sổ Suy nghĩ Thư thái 10–15s          │ BR-EXC-01         │
│ REQ-VOI-02  │ Logic Nói lại Chống Spam & Giữ Ổn định   │ BR-EXC-02         │
│ REQ-MIS-01  │ Hội thoại Mở & Chỉ số CSI Thích ứng      │ BR-MIS-01, 02, 03 │
│ REQ-EVA-01  │ Đánh giá Ngữ âm Phân tầng Xây dựng       │ BR-EVA-01, 02     │
│ REQ-REP-01  │ Luyện lại theo Lát cắt Hội thoại 3 bước  │ BR-REP-01, 02     │
│ REQ-EXC-01  │ Ứng xử Tinh tế khi Chêm tiếng mẹ đẻ      │ BR-EXC-03, 04, 05 │
└─────────────┴──────────────────────────────────────────┴───────────────────┘
```

---

## 6. TIÊU CHUẨN ĐO LƯỜNG NGHIỆP VỤ THÀNH CÔNG (BUSINESS METRICS)

1. **Chỉ số Tự tin (Confidence Gain):** Người học cảm thấy thoải mái, không bỏ dở giữa chừng vì áp lực thời gian (Tỷ lệ hoàn thành phiên $\ge 80\%$).
2. **Tỷ lệ Tự sinh câu (Speech Generation Rate):** $\ge 70\%$ số lượt thoại được người học tự nói mà không cần mở gợi ý Khung câu.
3. **Tiến bộ Lát cắt (Delta Progression):** Cải thiện rõ rệt độ tự tin sau khi thực hành lại lát cắt hội thoại tại màn hình tổng kết.
