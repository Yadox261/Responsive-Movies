import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'neutral-primary-soft': '#F9FAFB',
                'neutral-secondary-medium': '#F3F4F6',
                'neutral-tertiary-medium': '#E5E7EB',
                'default': '#D1D5DB',
                'heading': '#111827',
                'body': '#4B5563',
            }
        },
    },

    plugins: [forms, typography, flowbite],
};
