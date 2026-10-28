# ĐẶC TẢ KỸ THUẬT FRONTEND ALPINE.JS & AUDIO
## (FRONTEND ALPINE.JS & AUDIO VISUALIZER SPECIFICATION - FAS)

* **Dự án:** AI-Powered Real-World English Communication Simulator
* **Mã tài liệu:** FAS-v1.0 (Alpine.js Voice Controller & Web Audio Pipeline)
* **Tài liệu tham chiếu:** [BUSINESS_RULES.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/BUSINESS_RULES.md) | [SYSTEM_LOGIC_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/SYSTEM_LOGIC_SPECIFICATION.md) | [PROMPT_AND_AI_SPECIFICATION.md](file:///home/tmi-13/.gemini/antigravity/scratch/ai-english-simulator/docs/PROMPT_AND_AI_SPECIFICATION.md)
* **Trạng thái:** Active / Baseline

---

## 1. NGUYÊN TẮC THIẾT KẾ & TỐI ƯU TÀI NGUYÊN

1. **Client-Side Processing (0đ chi phí):** Xử lý nhận diện giọng nói (STT) và đo biên độ âm thanh (Volume) hoàn toàn trên trình duyệt thông qua **Web Speech API** và **Web Audio API** mà không tốn tài nguyên server.
2. **Chống Spam AI & Bảo Vệ Thiết Bị:**
   - Khi bấm `[Nói lại / Redo]`, hệ thống chỉ xóa bộ nhớ đệm chữ (`transcript`), không re-init microphone hay gọi lại API cấp quyền.
   - Kích hoạt nhịp đệm chuẩn bị 1.5 giây để người học hít thở và tạm khóa nút gửi.
   - Người học **chủ động bấm `[Xong / Gửi]`** khi hài lòng với câu nói, ngăn chặn các âm thanh rè vô nghĩa tự động gửi request đến Gemini.
3. **Cắt Lời AI Tự Nhiên (Barge-in):** Cho phép người học ngắt tiếng AI tức thì bằng cách chạm vào mic hoặc cất giọng nói.

---

## 2. MÁY TRẠNG THÁI GIAO DIỆN (REACTIVE STATE MACHINE)

```
[INIT] ──> [AI_SPEAKING] ──> [USER_RECORDING (Cửa sổ 10-15s)] ──> [AI_THINKING]
                 │                           │
                 │ (Barge-in: Cắt lời AI)    │ (User bấm Xong/Gửi)
                 └───────────────────────────┘
```

* `AI_SPEAKING`: AI phát giọng nói qua Web Speech Synthesis. Avatar mấp máy sóng âm. Nút mic sẵn sàng nhận tương tác cướp lời.
* `USER_RECORDING`: Mở mic thu âm. Kích hoạt bộ đếm suy nghĩ (0 $\rightarrow$ 15s). Live transcript chạy chữ. Sau 8s nhấp nháy nút Gợi ý.
* `AI_THINKING`: Gửi AJAX đến Laravel Controller. Hiển thị trạng thái suy nghĩ, tạm khóa các nút bấm.
* `COMPLETED`: Giao dịch hoàn tất, tự động chuyển trang sang Màn hình Tổng kết (`/summary`).

---

## 3. MÃ NGUỒN COMPONENT ALPINE.JS (`voiceSimulator.js`)

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('voiceSimulator', (config) => ({
        // 1. Trạng thái giao diện & Video Call Dock
        sessionCode: config.sessionCode,
        sessionState: 'INIT', // 'AI_SPEAKING', 'USER_RECORDING', 'AI_THINKING', 'COMPLETED'
        transcript: '',
        currentAiText: '',
        thinkingSeconds: 0,
        thinkingInterval: null,
        
        // Video Call Meeting Dock & Drawer states
        showSubtitles: false,       // Mặc định TẮT Live CC để bảo vệ phản xạ nghe
        activeDrawerTab: null,      // null | 'tips' | 'script' (Slide-over Drawer bên phải)
        isTipsPulsing: false,       // Tự động nhấp nháy vàng sau 8s im lặng
        hasNewScriptBadge: false,   // Chấm đỏ thông báo khi có lượt thoại mới ghi ngầm
        dialogueHistory: [],        // Toàn bộ lịch sử script được ghi ngầm
        
        // 2. Kiểm soát Redo & Chống Spam
        redoCount: 0,
        isRedoPreparing: false,
        redoPreparationTimer: 0,
        
        // 3. Audio & Web Speech Engines
        recognition: null,
        audioContext: null,
        analyser: null,
        micStream: null,
        audioVolume: 0, // Biên độ âm thanh từ 0 - 100 để vẽ sóng Avatar
        
        // Khởi tạo component
        init() {
            this.initSpeechRecognition();
            this.initAudioContext();
            
            this.$nextTick(() => {
                this.playAiSpeech(config.firstMessage);
            });
        },

        // --- MẮT XÍCH 1: NHẬN DIỆN GIỌNG NÓI (STT) ---
        initSpeechRecognition() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                alert('Trình duyệt của bạn chưa hỗ trợ nhận diện giọng nói. Vui lòng dùng Chrome hoặc Edge.');
                return;
            }

            this.recognition = new SpeechRecognition();
            this.recognition.continuous = true;
            this.recognition.interimResults = true;
            this.recognition.lang = 'en-US';

            this.recognition.onresult = (event) => {
                let currentText = '';
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    currentText += event.results[i][0].transcript;
                }
                this.transcript = currentText;
            };

            this.recognition.onerror = (event) => {
                console.warn('Speech Recognition Warning:', event.error);
            };
        },

        // --- MẮT XÍCH 2: BẮT SÓNG ÂM THANH CHO AVATAR (AUDIO VISUALIZER) ---
        async initAudioContext() {
            try {
                this.micStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const source = this.audioContext.createMediaStreamSource(this.micStream);
                this.analyser = this.audioContext.createAnalyser();
                this.analyser.fftSize = 64;
                source.connect(this.analyser);

                this.trackVolume();
            } catch (err) {
                console.error('Lỗi cấp quyền Microphone:', err);
            }
        },

        trackVolume() {
            const dataArray = new Uint8Array(this.analyser.frequencyBinCount);
            const check = () => {
                if (this.sessionState === 'USER_RECORDING' || this.sessionState === 'AI_SPEAKING') {
                    this.analyser.getByteFrequencyData(dataArray);
                    let sum = 0;
                    for (let i = 0; i < dataArray.length; i++) {
                        sum += dataArray[i];
                    }
                    this.audioVolume = Math.min(100, Math.round((sum / dataArray.length) * 1.5));
                } else {
                    this.audioVolume = 0;
                }
                requestAnimationFrame(check);
            };
            requestAnimationFrame(check);
        },

        // --- MẮT XÍCH 3: CỬA SỔ SUY NGHĨ 10-15S (BR-EXC-01) ---
        startThinkingWindow() {
            this.sessionState = 'USER_RECORDING';
            this.thinkingSeconds = 0;
            this.showHintReminder = false;
            
            try { this.recognition.start(); } catch (e) {}

            clearInterval(this.thinkingInterval);
            this.thinkingInterval = setInterval(() => {
                this.thinkingSeconds += 1;
                
                // Sau 8 giây im lặng -> Nhấp nháy nhẹ nút gợi ý
                if (this.thinkingSeconds >= 8 && !this.transcript) {
                    this.showHintReminder = true;
                }
                
                // Sau 15 giây không nói gì -> Tự động mở nhẹ gợi ý
                if (this.thinkingSeconds >= 15 && !this.transcript) {
                    this.showHintsModal = true;
                }
            }, 1000);
        },

        // --- MẮT XÍCH 4: CHỐT GỬI CÂU & GỌI LARAVEL BACKEND ---
        async submitTurn() {
            if (!this.transcript.trim()) return;

            clearInterval(this.thinkingInterval);
            try { this.recognition.stop(); } catch (e) {}
            
            this.sessionState = 'AI_THINKING';

            try {
                const response = await fetch(`/api/sessions/${this.sessionCode}/turn`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        transcript: this.transcript,
                        thinking_seconds: this.thinkingSeconds,
                        redo_count: this.redoCount,
                        hints_used: this.showHintsModal
                    })
                });

                const data = await response.json();
                
                // Reset cho lượt tiếp theo
                this.transcript = '';
                this.redoCount = 0;
                this.showHintReminder = false;

                if (data.is_completed) {
                    this.sessionState = 'COMPLETED';
                    window.location.href = `/sessions/${this.sessionCode}/summary`;
                } else {
                    this.playAiSpeech(data.ai_reply.text);
                }

            } catch (err) {
                console.error('Lỗi gửi lượt thoại:', err);
                this.startThinkingWindow();
            }
        },

        // --- MẮT XÍCH 5: NÓI LẠI (REDO) CHỐNG SPAM & KHÔNG ĐƠ MIC (BR-EXC-02) ---
        redoSpeech() {
            if (this.redoCount >= 2 || this.isRedoPreparing) return;

            this.redoCount++;
            this.transcript = '';
            this.isRedoPreparing = true;
            this.redoPreparationTimer = 1.5;

            try { this.recognition.stop(); } catch (e) {}

            const prepInterval = setInterval(() => {
                this.redoPreparationTimer -= 0.5;
                if (this.redoPreparationTimer <= 0) {
                    clearInterval(prepInterval);
                    this.isRedoPreparing = false;
                    try { this.recognition.start(); } catch (e) {}
                }
            }, 500);
        },

        // --- MẮT XÍCH 6: PHÁT GIỌNG ĐỌC AI & CẮT LỜI (TTS & BARGE-IN) ---
        playAiSpeech(text) {
            this.sessionState = 'AI_SPEAKING';
            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';
            utterance.rate = 0.95;

            utterance.onend = () => {
                this.startThinkingWindow();
            };

            window.speechSynthesis.speak(utterance);
        },

        // Cắt lời AI tự nhiên (Barge-in - BR-EXC-04)
        handleBargeIn() {
            if (this.sessionState === 'AI_SPEAKING') {
                window.speechSynthesis.cancel();
                this.startThinkingWindow();
            }
        }
    }));
});
```

---

## 4. COMPONENT KIỂM TRA THIẾT BỊ PHÒNG CHUẨN BỊ (`deviceTester.js`)

Component điều khiển **Section 4 & Section 5** của Màn hình 2 (Pre-Flight Lobby) để đánh giá chất lượng Microphone qua 3 trạng thái và điều khiển camera soi khẩu hình (tùy chọn).

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('deviceTester', () => ({
        // 1. Trạng thái Microphone: 'SILENT' | 'WEAK' | 'READY'
        micStatus: 'SILENT',
        volumeLevel: 0,
        audioContext: null,
        analyser: null,
        micStream: null,
        animationFrameId: null,

        // 2. Camera Soi Khẩu Hình (Tùy chọn)
        cameraEnabled: false,
        cameraStream: null,

        // Khởi động kiểm tra mic
        async initMic() {
            try {
                this.micStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                this.analyser = this.audioContext.createAnalyser();
                this.analyser.fftSize = 512;
                this.analyser.minDecibels = -90;
                this.analyser.maxDecibels = -10;

                const source = this.audioContext.createMediaStreamSource(this.micStream);
                source.connect(this.analyser);

                this.startAnalysis();
            } catch (err) {
                this.micStatus = 'SILENT';
                console.warn('Không thể truy cập Microphone:', err);
            }
        },

        // Vòng lặp phân tích biên độ và dải tần số giọng người (300Hz - 3000Hz)
        startAnalysis() {
            const dataArray = new Uint8Array(this.analyser.frequencyBinCount);
            const sampleRate = this.audioContext.sampleRate;
            const binSize = sampleRate / this.analyser.fftSize;

            const check = () => {
                this.analyser.getByteFrequencyData(dataArray);

                let totalEnergy = 0;
                let humanVoiceEnergy = 0;

                for (let i = 0; i < dataArray.length; i++) {
                    const value = dataArray[i];
                    totalEnergy += value;
                    const freq = i * binSize;

                    // Dải tần số đàm thoại của con người (300Hz - 3000Hz)
                    if (freq >= 300 && freq <= 3000) {
                        humanVoiceEnergy += value;
                    }
                }

                const avgVolume = totalEnergy / dataArray.length;
                this.volumeLevel = Math.min(100, Math.round((avgVolume / 128) * 100));

                // Phân loại 3 trạng thái
                if (avgVolume < 4) {
                    this.micStatus = 'SILENT'; // Không có tín hiệu
                } else if (avgVolume < 16 || humanVoiceEnergy < (totalEnergy * 0.45)) {
                    this.micStatus = 'WEAK';   // Tín hiệu quá nhỏ hoặc chủ yếu là tạp âm
                } else {
                    this.micStatus = 'READY';  // Đạt tiêu chuẩn giọng nói đàm thoại
                }

                this.animationFrameId = requestAnimationFrame(check);
            };

            check();
        },

        // Bật / Tắt Camera soi khẩu hình
        async toggleCamera() {
            this.cameraEnabled = !this.cameraEnabled;
            if (this.cameraEnabled) {
                try {
                    this.cameraStream = await navigator.mediaDevices.getUserMedia({ 
                        video: { width: 640, height: 360 } 
                    });
                    if (this.$refs.videoPreview) {
                        this.$refs.videoPreview.srcObject = this.cameraStream;
                    }
                } catch (err) {
                    alert('Không thể mở camera. Vui lòng cấp quyền.');
                    this.cameraEnabled = false;
                }
            } else if (this.cameraStream) {
                this.cameraStream.getTracks().forEach(track => track.stop());
                this.cameraStream = null;
            }
        },

        // Dọn dẹp tài nguyên khi rời trang
        destroy() {
            if (this.animationFrameId) cancelAnimationFrame(this.animationFrameId);
            if (this.micStream) this.micStream.getTracks().forEach(t => t.stop());
            if (this.cameraStream) this.cameraStream.getTracks().forEach(t => t.stop());
            if (this.audioContext) this.audioContext.close();
        }
    }));
});
```

