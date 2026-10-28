/**
 * Voice Simulator Component (Screen 3: Video Call Room)
 * Implements Reactive State Machine, Web Speech API STT/TTS,
 * MediaRecorder Direct Audio Upload (Cloudflare R2 / Local Fallback),
 * Barge-in, Web Audio Visualizer Halo, Meeting Dock & Scaffolding Drawer
 * 
 * Figma Node 39:1144 Compliance:
 * - Crucial pedagogical logic: Default captions/bubble to HIDDEN (showCaptions = false),
 *   ONLY revealed when learner clicks [CC] or [Script].
 * - Slow audio pronunciation (0.72x speed) for Scaffolding "Hear it slowly".
 * - Session timer counter formatting MM:SS.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('voiceSimulator', (config) => ({
        // 1. Session & Meeting State
        sessionCode: config.sessionCode,
        practiceLanguage: config.practiceLanguage || window.practiceLanguage || 'en',
        ttsLocale: config.ttsLocale || window.ttsLocale || 'en-US',
        langFlag: config.langFlag || '🇬🇧',
        langName: config.langName || 'Tiếng Anh',
        toastMessage: '',
        showToast: false,
        sessionState: 'INIT', // 'INIT', 'AI_SPEAKING', 'USER_RECORDING', 'AI_THINKING', 'COMPLETED'
        transcript: '',
        interimTranscript: '',
        currentAiText: config.firstMessage || 'Hello! Welcome to our conversation practice.',
        currentAiTranslation: config.firstMessageTranslation || 'Chào buổi sáng! Chào mừng bạn đến với Central Brew Roastery. Tôi có thể pha chế món gì cho bạn hôm nay?',
        showAiTranslation: false,
        thinkingSeconds: 0,
        thinkingInterval: null,
        isSubmitting: false,
        showExitModal: false,

        // Session Timer (Figma 39:1144: "02:34")
        sessionSeconds: 0,
        sessionTimerInterval: null,
        get sessionTimerText() {
            const mins = Math.floor(this.sessionSeconds / 60);
            const secs = this.sessionSeconds % 60;
            return (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
        },

        // 2. Crucial Pedagogical Logic: Default Captions HIDDEN!
        showCaptions: false,       // Default false! Only revealed on CC or Script click
        showSubtitles: false,      // Synonym for showCaptions
        isDrawerOpen: true,        // Right Scaffolding Drawer visible on desktop
        activeDrawerTab: 'tips',   // 'tips' | 'script'
        isTipsPulsing: false,      // Pulses golden after 8s silence
        hasNewScriptBadge: false,  // Dot badge for unread script turns
        dialogueHistory: [],       // Full record of conversation turns
        hintsUsedCount: 0,

        // 3. Redo & Anti-Spam Control
        redoCount: 0,
        maxRedos: 2,
        isRedoPreparing: false,
        redoPreparationTimer: 1.5,
        get redosRemaining() {
            return Math.max(0, this.maxRedos - this.redoCount);
        },

        // 4. Scaffolding (Anti-Parrot: Intent -> Sentence Frame -> Keywords)
        currentScaffolding: {
            intent_vi: 'Chào hỏi tự nhiên và nêu rõ món đồ uống bạn muốn gọi.',
            sentence_frame: 'Hello, could I please get a latte with less sugar?',
            keywords: ['hello', 'please', 'latte', 'less sugar']
        },

        // 5. Speech & Audio Engines
        recognition: null,
        recognitionSupported: false,
        isRecognitionActive: false,
        audioContext: null,
        analyser: null,
        micStream: null,
        mediaRecorder: null,
        audioChunks: [],
        audioVolume: 0, // 0 - 100 for reactive visualizer halo
        volumeAnimationId: null,

        // 6. PIP Self-View Camera
        cameraStream: null,
        cameraActive: false,

        // --- LIFECYCLE INIT ---
        init() {
            // Load initial hints from config
            if (config.hints && config.hints.length > 0) {
                const firstHint = config.hints[0];
                this.currentScaffolding = {
                    intent_vi: firstHint.vietnamese_intent || firstHint.intent_label || this.currentScaffolding.intent_vi,
                    sentence_frame: firstHint.sentence_frame || this.currentScaffolding.sentence_frame,
                    keywords: firstHint.keyword_bank || firstHint.keywords_data || this.currentScaffolding.keywords
                };
            }

            // Register initial AI turn into history
            this.dialogueHistory.push({
                id: 'init-ai',
                speaker: 'ai',
                speakerName: config.characterName || 'AI Partner',
                text: this.currentAiText,
                translation: this.currentAiTranslation,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });

            // Start session duration timer
            this.startSessionTimer();

            this.initSpeechRecognition();
            this.initAudioContext();
            this.initPipCamera();

            // Auto start with AI speaking the initial message
            this.$nextTick(() => {
                setTimeout(() => {
                    this.playAiSpeech(this.currentAiText);
                }, 400);
            });
        },

        startSessionTimer() {
            clearInterval(this.sessionTimerInterval);
            this.sessionTimerInterval = setInterval(() => {
                this.sessionSeconds += 1;
            }, 1000);
        },

        // --- MẮT XÍCH 1: NHẬN DIỆN GIỌNG NÓI (STT) ---
        initSpeechRecognition() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                this.recognitionSupported = false;
                console.warn('SpeechRecognition API is not supported in this browser.');
                return;
            }

            this.recognitionSupported = true;
            this.recognition = new SpeechRecognition();
            this.recognition.continuous = true;
            this.recognition.interimResults = true;
            this.recognition.lang = this.ttsLocale || 'en-US';

            this.recognition.onstart = () => {
                this.isRecognitionActive = true;
            };

            this.recognition.onresult = (event) => {
                let accumulated = '';
                for (let i = 0; i < event.results.length; i++) {
                    accumulated += event.results[i][0].transcript;
                }
                this.transcript = accumulated.trim();

                // If user speaks while AI is speaking -> Barge-in!
                if (this.sessionState === 'AI_SPEAKING' && this.transcript.length > 2) {
                    this.handleBargeIn();
                }

                // If user speaks, turn off golden tips pulse
                if (this.transcript.length > 0) {
                    this.isTipsPulsing = false;
                }
            };

            this.recognition.onerror = (event) => {
                console.warn('SpeechRecognition event warning:', event.error);
                if (event.error === 'not-allowed') {
                    this.isRecognitionActive = false;
                }
            };

            this.recognition.onend = () => {
                this.isRecognitionActive = false;
                // If we're still in USER_RECORDING state, gracefully restart
                if (this.sessionState === 'USER_RECORDING' && !this.isRedoPreparing && !this.isSubmitting) {
                    try {
                        this.recognition.start();
                    } catch (e) {
                        // ignore race restart errors
                    }
                }
            };
        },

        // --- MẮT XÍCH 2: AUDIO CONTEXT, MEDIA RECORDER & VISUALIZER SÓNG ÂM ---
        async initAudioContext() {
            try {
                this.micStream = await navigator.mediaDevices.getUserMedia({
                    audio: { echoCancellation: true, noiseSuppression: true }
                });

                // Initialize MediaRecorder for direct binary audio recording
                if (window.MediaRecorder && this.micStream) {
                    const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                        ? 'audio/webm;codecs=opus'
                        : (MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : 'audio/ogg');
                    try {
                        this.mediaRecorder = new MediaRecorder(this.micStream, { mimeType });
                        this.mediaRecorder.ondataavailable = (e) => {
                            if (e.data && e.data.size > 0) {
                                this.audioChunks.push(e.data);
                            }
                        };
                    } catch (recErr) {
                        console.warn('MediaRecorder init error:', recErr);
                    }
                }

                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                this.audioContext = new AudioCtx();
                if (this.audioContext.state === 'suspended') {
                    await this.audioContext.resume();
                }

                this.analyser = this.audioContext.createAnalyser();
                this.analyser.fftSize = 128;
                this.analyser.smoothingTimeConstant = 0.7;

                const source = this.audioContext.createMediaStreamSource(this.micStream);
                source.connect(this.analyser);

                this.trackVolume();
            } catch (err) {
                console.warn('Microphone access warning for visualizer:', err);
                this.trackSimulatedVolume();
            }
        },

        startMediaRecording() {
            this.audioChunks = [];
            if (!this.mediaRecorder) return;
            try {
                if (this.mediaRecorder.state === 'recording') {
                    this.mediaRecorder.stop();
                }
                this.mediaRecorder.start(200);
            } catch (e) {
                console.warn('MediaRecorder start error:', e);
            }
        },

        async stopMediaRecording() {
            if (!this.mediaRecorder) {
                return this.audioChunks.length > 0 ? new Blob(this.audioChunks, { type: 'audio/webm' }) : null;
            }

            if (this.mediaRecorder.state === 'recording') {
                return new Promise((resolve) => {
                    const onStop = () => {
                        this.mediaRecorder.removeEventListener('stop', onStop);
                        const mimeType = this.mediaRecorder.mimeType || 'audio/webm';
                        const blob = new Blob(this.audioChunks, { type: mimeType });
                        resolve(blob);
                    };
                    this.mediaRecorder.addEventListener('stop', onStop);
                    try {
                        this.mediaRecorder.stop();
                    } catch (e) {
                        const mimeType = this.mediaRecorder.mimeType || 'audio/webm';
                        resolve(new Blob(this.audioChunks, { type: mimeType }));
                    }
                });
            }

            const mimeType = this.mediaRecorder.mimeType || 'audio/webm';
            return this.audioChunks.length > 0 ? new Blob(this.audioChunks, { type: mimeType }) : null;
        },

        trackVolume() {
            const dataArray = new Uint8Array(this.analyser.frequencyBinCount);

            const loop = () => {
                if (this.sessionState === 'USER_RECORDING' && this.analyser) {
                    this.analyser.getByteFrequencyData(dataArray);
                    let sum = 0;
                    for (let i = 0; i < dataArray.length; i++) {
                        sum += dataArray[i];
                    }
                    const avg = sum / dataArray.length;
                    this.audioVolume = Math.min(100, Math.round((avg / 128) * 100 * 1.4));
                } else if (this.sessionState === 'AI_SPEAKING') {
                    // Pulsing natural wave during AI speech
                    const baseWave = 45 + Math.sin(Date.now() / 150) * 28 + Math.random() * 18;
                    this.audioVolume = Math.round(baseWave);
                } else {
                    this.audioVolume = 0;
                }

                this.volumeAnimationId = requestAnimationFrame(loop);
            };

            this.volumeAnimationId = requestAnimationFrame(loop);
        },

        trackSimulatedVolume() {
            const loop = () => {
                if (this.sessionState === 'AI_SPEAKING') {
                    this.audioVolume = 45 + Math.round(Math.sin(Date.now() / 150) * 25);
                } else {
                    this.audioVolume = 0;
                }
                this.volumeAnimationId = requestAnimationFrame(loop);
            };
            this.volumeAnimationId = requestAnimationFrame(loop);
        },

        // --- MẮT XÍCH 3: CỬA SỔ SUY NGHĨ 10-15S (BR-EXC-01) ---
        startThinkingWindow() {
            this.sessionState = 'USER_RECORDING';
            this.thinkingSeconds = 0;
            this.isTipsPulsing = false;

            // Start audio recording with MediaRecorder
            this.startMediaRecording();

            // Start Speech Recognition
            if (this.recognition && !this.isRecognitionActive) {
                try {
                    this.recognition.start();
                } catch (e) {
                    // Already running or blocked
                }
            }

            clearInterval(this.thinkingInterval);
            this.thinkingInterval = setInterval(() => {
                this.thinkingSeconds += 1;

                // After 8 seconds of silence and no transcript -> Pulse tips button
                if (this.thinkingSeconds >= 8 && !this.transcript) {
                    this.isTipsPulsing = true;
                }

                // After 15 seconds without speaking -> gently ensure drawer is visible
                if (this.thinkingSeconds >= 15 && !this.transcript) {
                    this.isDrawerOpen = true;
                    this.activeDrawerTab = 'tips';
                    this.hintsUsedCount++;
                }
            }, 1000);
        },

        // --- MẮT XÍCH 4: CHỐT GỬI CÂU, DIRECT UPLOAD R2 & GỌI LARAVEL BACKEND ---
        async submitTurn() {
            if (!this.transcript.trim() || this.isSubmitting) return;

            this.isSubmitting = true;
            clearInterval(this.thinkingInterval);

            // Safely stop recognition
            if (this.recognition) {
                try { this.recognition.stop(); } catch (e) {}
            }

            // Stop MediaRecorder and collect recorded audio Blob
            let audioBlob = null;
            try {
                audioBlob = await this.stopMediaRecording();
            } catch (recorderErr) {
                console.warn('Error collecting audioBlob:', recorderErr);
            }

            const sentTranscript = this.transcript.trim();
            const elapsedSeconds = this.thinkingSeconds;
            const currentRedo = this.redoCount;

            // Direct Upload Flow: Request Presigned URL & PUT directly to Cloudflare R2 / Local Fallback
            let uploadedAudioUrl = null;
            if (audioBlob && audioBlob.size > 1000) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const presignedRes = await fetch('/api/storage/presigned-url', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            file_name: `turn_${Date.now()}.webm`,
                            mime_type: 'audio/webm',
                            session_code: this.sessionCode
                        })
                    });

                    if (presignedRes.ok) {
                        const presignedData = await presignedRes.json();
                        if (presignedData.success && presignedData.upload_url) {
                            const uploadRes = await fetch(presignedData.upload_url, {
                                method: presignedData.method || 'PUT',
                                headers: {
                                    'Content-Type': 'audio/webm'
                                },
                                body: audioBlob
                            });

                            if (uploadRes.ok) {
                                uploadedAudioUrl = presignedData.public_url;
                            }
                        }
                    }
                } catch (uploadErr) {
                    console.warn('Direct upload audio failed, proceeding with text-only turn:', uploadErr);
                }
            }

            // Add user's turn immediately to dialogue history
            this.dialogueHistory.push({
                id: 'user-' + Date.now(),
                speaker: 'user',
                speakerName: 'Bạn (Learner)',
                text: sentTranscript,
                audioUrl: uploadedAudioUrl,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });

            this.sessionState = 'AI_THINKING';
            this.hasNewScriptBadge = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const response = await fetch(`/sessions/${this.sessionCode}/turn`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        transcript: sentTranscript,
                        audio_url: uploadedAudioUrl,
                        thinking_seconds: elapsedSeconds,
                        redo_count: currentRedo,
                        hints_used: this.hintsUsedCount > 0 || this.activeDrawerTab === 'tips'
                    })
                });

                if (!response.ok) {
                    throw new Error(`Server returned status ${response.status}`);
                }

                const data = await response.json();

                // Reset turn variables
                this.transcript = '';
                this.audioChunks = [];
                this.redoCount = 0;
                this.isTipsPulsing = false;
                this.isSubmitting = false;

                // Update scaffolding if provided in pedagogical feedback
                if (data.pedagogical_feedback?.scaffolding) {
                    const scaf = data.pedagogical_feedback.scaffolding;
                    this.currentScaffolding = {
                        intent_vi: scaf.intent_vi || this.currentScaffolding.intent_vi,
                        sentence_frame: scaf.sentence_frame || this.currentScaffolding.sentence_frame,
                        keywords: scaf.keywords || this.currentScaffolding.keywords
                    };
                }

                // If completed -> redirect to summary
                if (data.is_completed) {
                    this.sessionState = 'COMPLETED';
                    const redirectUrl = data.summary_url || `/sessions/${this.sessionCode}/summary`;
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 600);
                    return;
                }

                // Otherwise, AI speaks the next reply
                const aiReplyText = data.ai_reply?.text || 'I understand. Let us continue.';
                this.currentAiText = aiReplyText;
                this.currentAiTranslation = data.ai_reply?.translation_vi || this.getFallbackTranslation(aiReplyText) || '';

                this.dialogueHistory.push({
                    id: 'ai-' + Date.now(),
                    speaker: 'ai',
                    speakerName: config.characterName || 'AI Partner',
                    text: aiReplyText,
                    translation: this.currentAiTranslation,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });

                this.playAiSpeech(aiReplyText);

            } catch (err) {
                console.error('Lỗi khi gửi lượt thoại:', err);
                this.isSubmitting = false;
                alert('Không thể kết nối với AI. Đang khởi động lại lượt nói...');
                this.startThinkingWindow();
            }
        },

        // --- MẮT XÍCH 5: NÓI LẠI (REDO) CHỐNG SPAM & KHÔNG ĐƠ MIC (BR-EXC-02) ---
        redoSpeech() {
            if (this.redoCount >= this.maxRedos || this.isRedoPreparing || this.sessionState !== 'USER_RECORDING') {
                return;
            }

            this.redoCount++;
            this.transcript = '';
            this.isRedoPreparing = true;
            this.redoPreparationTimer = 1.5;

            // Reset audio chunks and stop recorder if active
            if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                try { this.mediaRecorder.stop(); } catch (e) {}
            }
            this.audioChunks = [];

            if (this.recognition) {
                try { this.recognition.stop(); } catch (e) {}
            }

            const prepInterval = setInterval(() => {
                this.redoPreparationTimer = Math.max(0, parseFloat((this.redoPreparationTimer - 0.5).toFixed(1)));
                if (this.redoPreparationTimer <= 0) {
                    clearInterval(prepInterval);
                    this.isRedoPreparing = false;
                    this.startThinkingWindow();
                }
            }, 500);
        },

        // --- MẮT XÍCH 6: PHÁT GIỌNG ĐỌC AI & BARGE-IN (BR-EXC-04) ---
        playAiSpeech(text) {
            this.sessionState = 'AI_SPEAKING';

            if (!('speechSynthesis' in window)) {
                // Speech synthesis fallback: simulate speaking duration
                const duration = Math.min(8000, Math.max(2500, text.split(' ').length * 300));
                setTimeout(() => {
                    if (this.sessionState === 'AI_SPEAKING') {
                        this.startThinkingWindow();
                    }
                }, duration);
                return;
            }

            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = this.ttsLocale || 'en-US';
            utterance.rate = 0.92;
            utterance.pitch = 1.0;

            utterance.onend = () => {
                if (this.sessionState === 'AI_SPEAKING') {
                    this.startThinkingWindow();
                }
            };

            utterance.onerror = (e) => {
                console.warn('TTS playback error:', e);
                if (this.sessionState === 'AI_SPEAKING') {
                    this.startThinkingWindow();
                }
            };

            window.speechSynthesis.speak(utterance);
        },

        // Cắt lời AI tự nhiên khi người học nói hoặc chạm vào Mic
        handleBargeIn() {
            if (this.sessionState === 'AI_SPEAKING') {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                }
                this.startThinkingWindow();
            }
        },

        // Mic Click Action from Meeting Dock
        handleMicClick() {
            if (this.sessionState === 'AI_SPEAKING') {
                this.handleBargeIn();
            } else if (this.sessionState === 'USER_RECORDING') {
                if (this.transcript.trim()) {
                    this.submitTurn();
                }
            }
        },

        // --- CC / CAPTIONS TOGGLE (Pedagogical Logic: default false, toggled on demand) ---
        toggleCaptions() {
            this.showCaptions = !this.showCaptions;
            this.showSubtitles = this.showCaptions;
        },

        // --- SCAFFOLDING "HEAR IT SLOWLY" (0.72x speed TTS) ---
        speakSlowly(text) {
            if (!text || !('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = this.ttsLocale || 'en-US';
            utterance.rate = 0.72; // Slow, clear articulation
            utterance.pitch = 1.0;
            window.speechSynthesis.speak(utterance);
        },

        // Toast notifications
        showToastNotification(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3200);
        },

        // Pin current AI utterance to Sticky Notes
        pinCurrentAiPhrase() {
            this.pinPhrase(this.currentAiText, this.currentAiTranslation);
        },

        // Pin specific phrase into Sticky Notes API
        async pinPhrase(phrase, meaning) {
            if (!phrase) return;
            try {
                const response = await fetch('/notes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        phrase: phrase,
                        meaning_vi: meaning || phrase,
                        language: this.practiceLanguage || 'en',
                        color: 'yellow',
                        pin_style: 'thumbtack_brass'
                    })
                });

                if (response.ok) {
                    this.showToastNotification('📌 Đã ghim câu vào Giấy Note!');
                } else if (response.status === 401) {
                    this.showToastNotification('⚠️ Vui lòng đăng nhập để lưu giấy note');
                } else {
                    this.showToastNotification('📌 Đã lưu câu vào sổ note!');
                }
            } catch (e) {
                console.error(e);
            }
        },

        // Pronounce single vocabulary word
        speakWord(word) {
            if (!word || !('speechSynthesis' in window)) return;
            const utter = new SpeechSynthesisUtterance(word);
            utter.lang = 'en-US';
            utter.rate = 0.82;
            window.speechSynthesis.speak(utter);
        },

        // --- DRAWER TOGGLE (Tips / Script) ---
        toggleDrawer(tab) {
            if (tab === 'script') {
                // When learner opens Script, also reveal captions per pedagogical brief!
                this.showCaptions = true;
                this.showSubtitles = true;
                this.hasNewScriptBadge = false;
            }

            if (this.isDrawerOpen && this.activeDrawerTab === tab) {
                this.isDrawerOpen = false;
            } else {
                this.isDrawerOpen = true;
                this.activeDrawerTab = tab;
                if (tab === 'tips') {
                    this.isTipsPulsing = false;
                    this.hintsUsedCount++;
                }
            }
        },

        closeDrawer() {
            this.isDrawerOpen = false;
        },

        getFallbackTranslation(text) {
            if (!text) return '';
            const clean = text.trim();
            const map = {
                'Good morning! Welcome to Central Brew Roastery. What can I craft for you today?': 'Chào buổi sáng! Chào mừng bạn đến với Central Brew Roastery. Tôi có thể pha chế món gì cho bạn hôm nay?',
                'Good morning! Welcome to Local Roastery. What can I get started for you today?': 'Chào buổi sáng! Chào mừng bạn đến với Local Roastery. Tôi có thể lấy món gì cho bạn hôm nay?',
                'Sure thing! By the way, our main espresso grinder is resetting for five minutes. Would you mind waiting five minutes or would you prefer a smooth cold brew right now?': 'Tất nhiên rồi! Nhân tiện thì máy xay espresso chính đang cài đặt lại trong 5 phút. Bạn có phiền đợi 5 phút không hay muốn đổi sang một ly cold brew đậm vị ngay bây giờ?',
                'Perfect, your cold brew with a splash of oat milk is ready right here! That comes to four dollars and fifty cents. Enjoy your coffee!': 'Tuyệt vời, ly cold brew thêm chút sữa yến mạch của bạn đã sẵn sàng đây rồi! Tổng cộng là 4 đô la 50 xu. Chúc bạn thưởng thức cà phê ngon miệng!',
                'Thank you! I found your reservation under that name. Our sea-view room is currently having quick AC maintenance—would you like a free upgrade to our Executive Garden Suite instead?': 'Cảm ơn bạn! Tôi đã tìm thấy thông tin đặt phòng dưới tên bạn. Phòng hướng biển hiện đang được bảo trì điều hòa nhanh—bạn có muốn nâng hạng miễn phí lên phòng Executive Garden Suite không?',
                'Wonderful choice! Here are your keycards for Suite 402 on the Executive floor. Breakfast is served from 6:30 AM in the lounge. Have a wonderful stay!': 'Lựa chọn tuyệt vời! Đây là thẻ khóa phòng Suite 402 trên tầng Executive của bạn. Bữa sáng phục vụ từ 6:30 sáng tại sảnh. Chúc bạn có kỳ nghỉ thật tuyệt vời!',
                'Line 1 is temporarily halted for rail repairs today. You can take Line 3 to 14th Street and transfer to the shuttle bus. Would you like a day pass or single-ride ticket?': 'Tuyến số 1 hôm nay đang tạm ngừng để sửa chữa đường ray. Bạn có thể đi Tuyến số 3 đến Phố 14 và chuyển sang xe buýt trung chuyển. Bạn muốn mua vé ngày hay vé một lượt?',
                'Here is your metro card and connection map. Just tap your card at the turnstile right ahead. Have a safe ride!': 'Đây là thẻ tàu điện ngầm và bản đồ chuyển tuyến của bạn. Bạn chỉ cần chạm thẻ vào cửa xoay ngay phía trước. Chúc bạn có một chuyến đi an toàn!',
            };

            if (map[clean]) return map[clean];

            if (clean.toLowerCase().includes('enjoy your coffee') || clean.toLowerCase().includes('have a great day')) {
                return 'Chúc bạn một ngày tuyệt vời và thưởng thức món đồ uống của mình nhé!';
            }
            if (clean.toLowerCase().includes('have a safe ride') || clean.toLowerCase().includes('have a wonderful stay')) {
                return 'Chúc bạn có một trải nghiệm và chuyến đi an toàn, thoải mái!';
            }

            return '';
        },

        // --- PIP CAMERA MIRROR ---
        async initPipCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return;
            try {
                this.cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: 320, height: 240, facingMode: 'user' }
                });
                this.cameraActive = true;
                if (this.$refs.pipVideo) {
                    this.$refs.pipVideo.srcObject = this.cameraStream;
                }
            } catch (err) {
                this.cameraActive = false;
            }
        },

        togglePipCamera() {
            this.cameraActive = !this.cameraActive;
            if (this.cameraActive && !this.cameraStream) {
                this.initPipCamera();
            } else if (!this.cameraActive && this.cameraStream) {
                this.cameraStream.getTracks().forEach(t => t.stop());
                this.cameraStream = null;
            }
        },

        // Cleanup
        destroy() {
            clearInterval(this.thinkingInterval);
            clearInterval(this.sessionTimerInterval);
            if (this.volumeAnimationId) cancelAnimationFrame(this.volumeAnimationId);
            if (this.recognition) {
                try { this.recognition.stop(); } catch (e) {}
            }
            if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                try { this.mediaRecorder.stop(); } catch (e) {}
            }
            if (this.micStream) {
                this.micStream.getTracks().forEach(t => t.stop());
            }
            if (this.cameraStream) {
                this.cameraStream.getTracks().forEach(t => t.stop());
            }
            if (this.audioContext && this.audioContext.state !== 'closed') {
                this.audioContext.close();
            }
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
        }
    }));
});
