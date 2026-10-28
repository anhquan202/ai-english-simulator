/**
 * Device Tester Component (Screen 2: Pre-Flight Lobby)
 * Evaluates Microphone Readiness (SILENT, WEAK, READY), Camera Mirror,
 * and manages Challenge Level & Persona Selection state
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('deviceTester', (config = {}) => ({
        // 1. Learning Options, Language & Persona Selection
        selectedLanguage: config.selectedLanguage || 'en',
        selectedLevel: config.selectedLevel || 'A2',
        selectedCharId: config.selectedCharId || null,
        characters: Array.isArray(config.characters) ? config.characters : [],
        isAuthenticated: config.isAuthenticated ?? true,
        showAuthModal: false,

        getLangFlag(code) {
            const flags = {
                en: '🇬🇧',
                /* [MULTI-LANG DISABLED]
                ja: '🇯🇵',
                zh: '🇨🇳',
                ko: '🇰🇷',
                es: '🇪🇸'
                */
            };
            return flags[code] || '🌐';
        },
        getLangName(code) {
            const names = {
                en: 'Tiếng Anh',
                /* [MULTI-LANG DISABLED]
                ja: 'Tiếng Nhật',
                zh: 'Tiếng Trung',
                ko: 'Tiếng Hàn',
                es: 'Tây Ban Nha'
                */
            };
            return names[code] || code;
        },

        get selectedCharacter() {
            return this.characters.find(c => c.id == this.selectedCharId) || this.characters[0] || {};
        },

        // Practice & Auth Transition Handlers
        handleStartPractice(e) {
            if (!this.isAuthenticated) {
                if (e && e.preventDefault) e.preventDefault();
                this.showAuthModal = true;
                return;
            }
            if (this.$refs.practiceForm) {
                this.$refs.practiceForm.submit();
            }
        },

        onAuthSuccess(detail) {
            this.isAuthenticated = true;
            this.showAuthModal = false;
            this.$nextTick(() => {
                if (this.$refs.practiceForm) {
                    this.$refs.practiceForm.submit();
                }
            });
        },

        // 2. Microphone state: 'SILENT' | 'WEAK' | 'READY'
        micStatus: 'SILENT',
        volumeLevel: 0,
        audioContext: null,
        analyser: null,
        micStream: null,
        animationFrameId: null,
        isListening: false,
        micError: null,

        // 3. Camera Mirror state (Optional, 100% client-side)
        cameraEnabled: false,
        cameraStream: null,
        cameraError: null,

        // 4. AI Greeting Voice Preview
        isPlayingGreeting: false,

        init() {
            // Set initial selected character if not set
            if (!this.selectedCharId && this.characters.length > 0) {
                const def = this.characters.find(c => c.is_default);
                this.selectedCharId = def ? def.id : this.characters[0].id;
            }

            // Automatically prompt mic check on lobby entry
            this.$nextTick(() => {
                this.initMic();
            });
        },

        // --- MICROPHONE TEST PIPELINE ---
        async initMic() {
            this.micError = null;
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                this.micError = 'Trình duyệt của bạn không hỗ trợ getUserMedia.';
                return;
            }

            try {
                this.micStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        autoGainControl: true,
                    }
                });

                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                this.audioContext = new AudioCtx();
                if (this.audioContext.state === 'suspended') {
                    await this.audioContext.resume();
                }

                this.analyser = this.audioContext.createAnalyser();
                this.analyser.fftSize = 512;
                this.analyser.minDecibels = -90;
                this.analyser.maxDecibels = -10;
                this.analyser.smoothingTimeConstant = 0.8;

                const source = this.audioContext.createMediaStreamSource(this.micStream);
                source.connect(this.analyser);

                this.isListening = true;
                this.startAnalysis();
            } catch (err) {
                console.warn('Microphone permission or hardware error:', err);
                this.micStatus = 'SILENT';
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    this.micError = 'Bạn chưa cấp quyền truy cập Microphone. Vui lòng nhấn "Cho phép" trên thanh địa chỉ.';
                } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                    this.micError = 'Không tìm thấy thiết bị Microphone kết nối.';
                } else {
                    this.micError = 'Không thể kết nối mic: ' + (err.message || 'Lỗi không xác định');
                }
            }
        },

        startAnalysis() {
            if (!this.analyser || !this.audioContext) return;

            const dataArray = new Uint8Array(this.analyser.frequencyBinCount);
            const sampleRate = this.audioContext.sampleRate;
            const binSize = sampleRate / this.analyser.fftSize;

            const check = () => {
                if (!this.analyser) return;

                this.analyser.getByteFrequencyData(dataArray);

                let totalEnergy = 0;
                let humanVoiceEnergy = 0;

                for (let i = 0; i < dataArray.length; i++) {
                    const value = dataArray[i];
                    totalEnergy += value;
                    const freq = i * binSize;

                    // Human conversational speech range (300Hz - 3000Hz)
                    if (freq >= 300 && freq <= 3000) {
                        humanVoiceEnergy += value;
                    }
                }

                const avgVolume = totalEnergy / dataArray.length;
                this.volumeLevel = Math.min(100, Math.round((avgVolume / 128) * 100));

                // 3 Standard States:
                // 1. SILENT: Very low energy
                // 2. WEAK: Sub-optimal loudness or background noise
                // 3. READY: Clear human speech frequency & volume
                if (avgVolume < 4) {
                    this.micStatus = 'SILENT';
                } else if (avgVolume < 14 || humanVoiceEnergy < (totalEnergy * 0.40)) {
                    this.micStatus = 'WEAK';
                } else {
                    this.micStatus = 'READY';
                }

                this.animationFrameId = requestAnimationFrame(check);
            };

            check();
        },

        // Friendly bypass for sandbox or headless testing
        bypassMicForTesting() {
            this.micStatus = 'READY';
            this.volumeLevel = 82;
            this.micError = null;
        },

        // --- CAMERA MOUTH-MIRROR TOGGLE ---
        async toggleCamera() {
            this.cameraError = null;
            this.cameraEnabled = !this.cameraEnabled;

            if (this.cameraEnabled) {
                try {
                    this.cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            width: { ideal: 640 },
                            height: { ideal: 360 },
                            facingMode: 'user',
                        }
                    });

                    if (this.$refs.videoPreview) {
                        this.$refs.videoPreview.srcObject = this.cameraStream;
                    }
                } catch (err) {
                    console.warn('Camera error:', err);
                    this.cameraEnabled = false;
                    this.cameraError = 'Không thể mở Camera. Vui lòng cấp quyền hoặc kiểm tra thiết bị.';
                }
            } else {
                this.stopCamera();
            }
        },

        stopCamera() {
            if (this.cameraStream) {
                this.cameraStream.getTracks().forEach(track => track.stop());
                this.cameraStream = null;
            }
            if (this.$refs.videoPreview) {
                this.$refs.videoPreview.srcObject = null;
            }
        },

        // --- AI GREETING VOICE PREVIEW (TTS) ---
        playAiGreeting(greetingText, speechRate = 0.92) {
            if (!('speechSynthesis' in window)) {
                alert('Trình duyệt không hỗ trợ Web Speech Synthesis.');
                return;
            }

            if (this.isPlayingGreeting) {
                window.speechSynthesis.cancel();
                this.isPlayingGreeting = false;
                return;
            }

            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(greetingText || 'Hello there! Welcome to our conversation practice.');
            utterance.lang = 'en-US';
            utterance.rate = Number(speechRate) || 0.92;
            utterance.pitch = 1.0;

            this.isPlayingGreeting = true;

            utterance.onend = () => {
                this.isPlayingGreeting = false;
            };

            utterance.onerror = () => {
                this.isPlayingGreeting = false;
            };

            window.speechSynthesis.speak(utterance);
        },

        // --- CLEANUP ---
        destroy() {
            if (this.animationFrameId) {
                cancelAnimationFrame(this.animationFrameId);
            }
            if (this.micStream) {
                this.micStream.getTracks().forEach(track => track.stop());
                this.micStream = null;
            }
            this.stopCamera();
            if (this.audioContext && this.audioContext.state !== 'closed') {
                this.audioContext.close();
            }
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
        }
    }));
});
