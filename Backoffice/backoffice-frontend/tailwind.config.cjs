/** @type {import('tailwindcss').Config} */
module.exports = {
    safelist: [
        'badge-outline-primary',
        'badge-outline-secondary',
        'badge-outline-success'
    ],
    content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
    darkMode: 'class',
    theme: {
        container: {
            center: true,
        },
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#648de1',
                    light: '#e8eeff',
                    dark: '#4a6fc9',
                    'dark-light': 'rgba(100,141,225,.15)',
                },
                secondary: {
                    DEFAULT: '#8b5cf6',
                    light: '#ede9fe',
                    dark: '#7c3aed',
                    'dark-light': 'rgba(139,92,246,.15)',
                },
                accent: {
                    DEFAULT: '#06b6d4',
                    light: '#e0f7fa',
                    dark: '#0891b2',
                },
                success: {
                    DEFAULT: '#10b981',
                    light: '#d1fae5',
                    'dark-light': 'rgba(16,185,129,.15)',
                },
                danger: {
                    DEFAULT: '#ef4444',
                    light: '#fee2e2',
                    'dark-light': 'rgba(239,68,68,.15)',
                },
                warning: {
                    DEFAULT: '#f59e0b',
                    light: '#fef3c7',
                    'dark-light': 'rgba(245,158,11,.15)',
                },
                info: {
                    DEFAULT: '#3b82f6',
                    light: '#dbeafe',
                    'dark-light': 'rgba(59,130,246,.15)',
                },
                dark: {
                    DEFAULT: '#1e293b',
                    light: '#f1f5f9',
                    'dark-light': 'rgba(30,41,59,.15)',
                },
                black: {
                    DEFAULT: '#0f172a',
                    light: '#e2e8f0',
                    'dark-light': 'rgba(15,23,42,.15)',
                },
                white: {
                    DEFAULT: '#ffffff',
                    light: '#f8fafc',
                    dark: '#94a3b8',
                },
            },

            fontFamily: {
                roboto: ['Roboto', 'sans-serif'],
                robotoCondence: ['Roboto Condensed'],
            },
            spacing: {
                4.5: '18px',
            },
            boxShadow: {
                '3xl': '0 2px 2px rgb(224 230 237 / 46%), 1px 6px 7px rgb(224 230 237 / 46%)',
            },
            typography: ({ theme }) => ({
                DEFAULT: {
                    css: {
                        '--tw-prose-invert-headings': theme('colors.white.dark'),
                        '--tw-prose-invert-links': theme('colors.white.dark'),
                        h1: { fontSize: '40px', marginBottom: '0.5rem', marginTop: 0 },
                        h2: { fontSize: '32px', marginBottom: '0.5rem', marginTop: 0 },
                        h3: { fontSize: '28px', marginBottom: '0.5rem', marginTop: 0 },
                        h4: { fontSize: '24px', marginBottom: '0.5rem', marginTop: 0 },
                        h5: { fontSize: '20px', marginBottom: '0.5rem', marginTop: 0 },
                        h6: { fontSize: '16px', marginBottom: '0.5rem', marginTop: 0 },
                        p: { marginBottom: '0.5rem' },
                        li: { margin: 0 },
                        img: { margin: 0 },
                    },
                },
            }),
        },
    },
    plugins: [
        require('@tailwindcss/forms')({
            strategy: 'class',
        }),
        require('@tailwindcss/typography'),
    ],
};
