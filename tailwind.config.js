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
                kantin: {
                    50:  '#fdf8f0',
                    100: '#faefd9',
                    200: '#f3d9a8',
                    300: '#eabd6e',
                    400: '#e0a03c',
                    500: '#c8862a',
                    600: '#a86820',
                    700: '#85501c',
                    800: '#6b3f1d',
                    900: '#57341b',
                },
            },
        },
    },

    plugins: [forms],
};
