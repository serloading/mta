import { existsSync } from 'node:fs';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

// The Filament admin theme @imports a file from vendor/. Only build it when
// Composer dependencies are present (skips cleanly on frontend-only CI builds
// where `composer install` has not run, e.g. Vercel).
const filamentThemeEntry = 'resources/css/filament/admin/theme.css';
const hasFilamentVendor = existsSync('vendor/filament/filament/resources/css/theme.css');

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...(hasFilamentVendor ? [filamentThemeEntry] : []),
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
