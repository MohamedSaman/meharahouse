import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './app/View/Components/**/*.php',
    ],

    safelist: [
        // Fractional spacing values that Tailwind's JIT may miss
        { pattern: /^(p|px|py|pt|pb|pl|pr|gap)-(0\.5|1\.5|2\.5|3\.5)$/ },
    ],

    theme: {
        extend: {},
    },

    plugins: [
        forms({ strategy: 'class' }),
    ],
};
