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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                coffee: {
                    50: '#faf8f5',
                    100: '#f5f0e8',
                    200: '#e8dcc8',
                    300: '#d4c0a0',
                    400: '#b89968',
                    500: '#8b6f47',
                    600: '#6b5538',
                    700: '#4a3a26',
                    800: '#2d2419',
                    900: '#1a140e',
                },
                cream: '#f5f0e8',
                charcoal: '#2d2419',
                gold: '#d4af37',
            },
        },
    },

    plugins: [forms],
};
