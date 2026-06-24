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
            colors: {
                primary: '#2563EB',
                success: '#16A34A',
                warning: '#F59E0B',
                danger: '#DC2626',
                app: {
                    background: '#F3F4F6',
                    card: '#FFFFFF',
                    dark: '#111827',
                    sidebar: '#111827',
                    muted: '#6B7280',
                    border: '#E5E7EB',
                    info: '#EFF6FF',
                },
            },
        },
    },

    plugins: [forms],
};
