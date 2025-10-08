import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import collectModuleAssetsPaths from "./vite-module-loader";

let paths = [
    'resources/css/app.css',
    'resources/js/app.js',
];

// Precompute all paths synchronously.
let allPaths = [];
(async () => {
    allPaths = await collectModuleAssetsPaths(paths, 'Modules');
})();

if (allPaths.length === 0) {
    allPaths = paths;
}

export default defineConfig({
    plugins: [
        laravel({
            input: allPaths,
            refresh: [
                'routes/**',
                'resources/views/**',
                'app/Http/Controllers/**',
                'Modules/**/Resources/views/**',
                '!storage/**',
            ],
        }),
        react(),
        tailwindcss(),
    ],
    esbuild: {
        jsx: 'automatic',
        // drop: ['console', 'debugger'],
    },
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            input: 'resources/js/app.js',
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: false,
            ignored: [
                '**/storage/logs/**',
                '**/storage/framework/**',
                '**/storage/app/**',
                '**/node_modules/**',
                '**/vendor/**',
                '**/.git/**',
                '**/public/build/**',
                '**/.env',
                '**/*.log',
            ],
        },
    },
});