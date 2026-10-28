/**
 * Auth Modal Component (Lobby Screen 2 Pre-flight Flow)
 * Handles Seamless Login & Registration via AJAX, inline validation,
 * and auto-submitting the practice session upon successful auth.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('authModal', (config = {}) => ({
        tab: 'login', // 'login' | 'register'
        isLoading: false,

        // Login State
        loginUsername: '',
        loginPassword: '',
        showLoginPassword: false,
        loginError: '',

        // Register State
        regFirstName: '',
        regLastName: '',
        regUsername: '',
        regPassword: '',
        showRegPassword: false,
        regErrors: {
            username: '',
            password: '',
            general: ''
        },

        init() {
            // Reset errors when switching tabs
            this.$watch('tab', () => {
                this.loginError = '';
                this.regErrors = { username: '', password: '', general: '' };
            });
        },

        async submitLogin() {
            this.loginError = '';
            if (!this.loginUsername || !this.loginPassword) {
                this.loginError = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
                return;
            }

            this.isLoading = true;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        username: this.loginUsername,
                        email: this.loginUsername,
                        password: this.loginPassword
                    })
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && (data.success !== false)) {
                    // Auth success: notify parent lobby component
                    this.$dispatch('auth-success', { user: data.user || { name: this.loginUsername } });
                } else {
                    this.loginError = data.message || (data.errors ? Object.values(data.errors).flat()[0] : 'Mật khẩu chưa đúng. Bạn thử nhập lại nhé.');
                }
            } catch (err) {
                console.error('Login request failed:', err);
                this.loginError = 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.';
            } finally {
                this.isLoading = false;
            }
        },

        async submitRegister() {
            this.regErrors = { username: '', password: '', general: '' };

            if (!this.regUsername || !this.regPassword) {
                this.regErrors.general = 'Vui lòng điền đầy đủ các thông tin bắt buộc.';
                return;
            }

            if (this.regPassword.length < 8) {
                this.regErrors.password = 'Mật khẩu cần có ít nhất 8 ký tự.';
                return;
            }

            this.isLoading = true;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const response = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        first_name: this.regFirstName,
                        last_name: this.regLastName,
                        name: `${this.regFirstName} ${this.regLastName}`.trim() || this.regUsername,
                        username: this.regUsername,
                        email: this.regUsername.includes('@') ? this.regUsername : `${this.regUsername}@molo.local`,
                        password: this.regPassword,
                        password_confirmation: this.regPassword
                    })
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && (data.success !== false)) {
                    // Register success: notify parent lobby component
                    this.$dispatch('auth-success', { user: data.user || { name: this.regUsername } });
                } else {
                    if (data.errors) {
                        if (data.errors.username) this.regErrors.username = data.errors.username[0];
                        if (data.errors.email) this.regErrors.username = data.errors.email[0];
                        if (data.errors.password) this.regErrors.password = data.errors.password[0];
                    } else {
                        this.regErrors.general = data.message || 'Đăng ký không thành công. Hãy thử lại.';
                    }
                }
            } catch (err) {
                console.error('Register request failed:', err);
                this.regErrors.general = 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.';
            } finally {
                this.isLoading = false;
            }
        }
    }));
});
