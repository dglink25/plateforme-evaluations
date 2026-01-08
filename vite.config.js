// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // Gardez-le si vous voulez garder un peu de Tailwind
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});