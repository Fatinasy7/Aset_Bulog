import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/login.css',
                'resources/css/scan-qr.css',
                'resources/css/dashboard.css',
                'resources/css/assets-form.css',
                'resources/css/assets-index.css',
                'resources/css/assets-show.css',
                'resources/css/laptops.css',
                'resources/css/printers.css',
                'resources/css/audit-index.css',
                'resources/css/management.css',
                'resources/css/frontend-preview.css',
                'resources/css/pics-form.css',
                'resources/css/pics-index.css',
                'resources/css/reports-index.css',
                'resources/css/settings-index.css',
                'resources/css/design-system.css',
                'resources/js/app.js',
                'resources/js/scan-qr.js',
            ],
            refresh: true,
        }),
    ],
});
