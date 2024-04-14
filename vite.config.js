import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/scss/auth/style.scss',
                'resources/js/auth.js',
            ],
            refresh: true,
        }),
    ],
});
