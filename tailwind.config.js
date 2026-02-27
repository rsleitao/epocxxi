import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        { pattern: /^bg-(green|blue)-(700|800)$/ },
        { pattern: /^text-white$/ },
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                epoc: {
                    primary: '#2D647A',
                    'primary-hover': '#255a6d',
                    light: '#8FB5CB',
                    lighter: '#AECAD9',
                },
            },
        },
    },

    plugins: [forms],
};
