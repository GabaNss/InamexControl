import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Identidade visual do INAMEX: vermelho do coracao (acao
                // primaria) e laranja da flor (destaque/acento).
                brand: {
                    50: '#fdf3f1',
                    100: '#fbe3de',
                    200: '#f6c7bd',
                    300: '#eea190',
                    400: '#e2745f',
                    500: '#cf5440',
                    600: '#bd3a26',
                    700: '#982e1e',
                    800: '#7a2619',
                    900: '#5c1c13',
                },
                accent: {
                    50: '#fff7ed',
                    100: '#ffeacc',
                    200: '#ffd199',
                    300: '#ffb763',
                    400: '#f39c3a',
                    500: '#e8923c',
                    600: '#d97a1f',
                    700: '#b35f16',
                    800: '#8c4912',
                    900: '#6b370e',
                },
            },
        },
    },

    plugins: [forms],
};
