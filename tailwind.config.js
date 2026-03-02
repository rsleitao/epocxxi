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
        // Calendário dashboard: células dinâmicas
        'bg-gray-50',
        'bg-gray-100',
        'bg-amber-200',
        'bg-amber-300',
        'text-gray-800',
        'text-amber-900',
        'hover:bg-amber-300',
        'cursor-pointer',
        'cursor-default',
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
