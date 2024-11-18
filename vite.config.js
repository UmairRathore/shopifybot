import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/app.css'], // Add CSS if needed
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null, // Keeps assets relative
                    includeAbsolute: false, // Avoids transforming absolute URLs
                },
            },
        }),
    ],
});
