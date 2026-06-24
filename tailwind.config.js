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
                    background: '#F8FAFC',
                    card: '#FFFFFF',
                    dark: '#1E293B',
                    muted: '#64748B',
                    border: '#E2E8F0',
                },
            },
        },
    },

    plugins: [forms],
};
