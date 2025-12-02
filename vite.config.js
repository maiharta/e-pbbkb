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
                'resources/scss/app.scss',
                'resources/scss/themes/dark/app-dark.scss',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': normalizePath(__dirname, 'resources/js'),
            '~bootstrap': resolve(__dirname, 'node_modules/bootstrap'),
            '~bootstrap-icons': resolve(__dirname, 'node_modules/bootstrap-icons'),
            '~select2': resolve(__dirname, 'node_modules/select2'),
            '~perfect-scrollbar': resolve(__dirname, 'node_modules/perfect-scrollbar'),
            '~flatpickr': resolve(__dirname, 'node_modules/flatpickr'),
        },
    },
});
