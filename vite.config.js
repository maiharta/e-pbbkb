import {
    defineConfig,
    normalizePath
} from 'vite';
import laravel, {
    refreshPaths
} from 'laravel-vite-plugin';
import path, {
    dirname,
    resolve
} from 'path';

const __dirname = dirname(__filename);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/scss/auth/style.scss',
                'resources/js/auth.js',
                'resources/js/auth/register.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': normalizePath(__dirname, 'resources/js'),
        },
    },
});
