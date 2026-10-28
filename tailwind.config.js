import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                sage: {
                    canvas: '#F7F8F3',
                    surface: '#FFFFFF',
                    subtle: '#F1F3EC',
                    tint: '#EAF3EB',
                    border: '#E2E7DE',
                    'border-light': '#D5E3D7',
                    primary: '#267052',
                    'primary-hover': '#1E5A42',
                    accent: '#DCEBBA',
                    'accent-dark': '#174D38',
                },
                pine: {
                    DEFAULT: '#202D27',
                    subtle: '#435149',
                    muted: '#6C766F',
                },
                warm: {
                    border: '#E7E1D8',
                    50: '#FAF8F5',
                    100: '#F5F0E8',
                    200: '#EAE1D3',
                    300: '#D8CCA8',
                    700: '#443A33',
                    800: '#2C2521',
                    900: '#1C1917',
                },
                terracotta: {
                    DEFAULT: '#BA583C',
                    hover: '#A3482F',
                    subtle: '#FFF4F0',
                    50: '#FFF7ED',
                    100: '#FFEDD5',
                    200: '#FED7AA',
                    400: '#FB923C',
                    500: '#EA580C',
                    600: '#C2410C',
                    700: '#9A3412',
                },
                amber: {
                    50: '#FFFBEB',
                    100: '#FEF3C7',
                    500: '#F59E0B',
                    600: '#D97706',
                    700: '#B45309',
                },
            },
            spacing: {
                '4.5': '1.125rem', // 18px
                'touch-gap-sm': '0.5rem', // 8px (Minimum click gap)
                'touch-gap': '0.75rem',   // 12px (Recommended click gap)
                'touch-gap-lg': '1rem',   // 16px (Comfortable click gap)
            },
            minHeight: {
                'touch-sm': '2.25rem', // 36px (Filter Pill mobile)
                'touch-md': '2.5rem',  // 40px (Header/Compact button mobile)
                'touch': '2.75rem',    // 44px (Apple HIG standard)
                'touch-lg': '3rem',    // 48px (Google Material standard)
                'cta': '3.5rem',       // 56px (Hero CTA mobile)
                'cta-lg': '4rem',      // 64px (Hero CTA desktop)
            },
            minWidth: {
                'touch': '2.75rem',    // 44px (Apple HIG icon/square target)
                'touch-lg': '3rem',    // 48px (Google Material icon target)
            },
            height: {
                input: '3.25rem', // 52px
                header: '4.75rem', // 76px
            },
            width: {
                drawer: '21rem', // 336px
            },
            borderRadius: {
                card: '1.25rem', // 20px
                hero: '1.75rem', // 28px
            },
            boxShadow: {
                'soft-sm': '0 2px 8px -2px rgba(32, 45, 39, 0.05)',
                'soft': '0 4px 20px -4px rgba(32, 45, 39, 0.07)',
                'soft-lg': '0 12px 32px -8px rgba(32, 45, 39, 0.10)',
                'glow-emerald': '0 0 24px rgba(38, 112, 82, 0.35)',
                'glow-cyan': '0 0 32px rgba(6, 182, 212, 0.4)',
                'glow-amber': '0 0 24px rgba(245, 158, 11, 0.45)',
            },
            keyframes: {
                goldenPulse: {
                    '0%, 100%': { transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(245, 158, 11, 0.7)' },
                    '50%': { transform: 'scale(1.06)', boxShadow: '0 0 22px 6px rgba(245, 158, 11, 0.6)' },
                },
                haloWave: {
                    '0%': { transform: 'scale(0.95)', opacity: '0.8' },
                    '50%': { transform: 'scale(1.15)', opacity: '0.3' },
                    '100%': { transform: 'scale(1.35)', opacity: '0' },
                },
                subtleFloat: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-4px)' },
                },
            },
            animation: {
                'golden-pulse': 'goldenPulse 1.8s ease-in-out infinite',
                'halo-wave': 'haloWave 2.2s cubic-bezier(0, 0, 0.2, 1) infinite',
                'subtle-float': 'subtleFloat 4s ease-in-out infinite',
            },
        },
    },
    plugins: [],
};
