import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                coffee: {
                    50:  '#faf8f5',
                    100: '#f2ece2',
                    200: '#e2d3bc',
                    300: '#c9b08a',
                    400: '#b08d5e',
                    500: '#8b6f47',
                    600: '#6b5538',
                    700: '#4a3a26',
                    800: '#2d2419',
                    900: '#1a140e',
                    950: '#0e0b07',
                },
                sidebar: {
                    bg:      '#0f1117',
                    hover:   '#1a1d27',
                    active:  '#1e2235',
                    border:  '#1e2235',
                    text:    '#94a3b8',
                    heading: '#e2e8f0',
                },
                surface: {
                    DEFAULT: '#ffffff',
                    muted:   '#f8fafc',
                    border:  '#e2e8f0',
                },
                brand: {
                    DEFAULT: '#c8a96e',
                    light:   '#e2c99a',
                    dark:    '#a07840',
                    muted:   '#2a2215',
                },
                cream: '#f5f0e8',
                charcoal: '#2d2419',
                gold: '#c8a96e',
            },
            boxShadow: {
                'sidebar': '4px 0 24px 0 rgba(0,0,0,0.18)',
                'card':    '0 1px 3px 0 rgba(0,0,0,0.06), 0 1px 2px -1px rgba(0,0,0,0.04)',
                'card-md': '0 4px 12px 0 rgba(0,0,0,0.08)',
            },
        },
    },

    plugins: [forms],
};
