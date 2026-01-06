import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';



export default defineConfig({
    plugins: [
        tailwindcss(),
    ],
    build: {
        // Generiert eine manifest.json, damit PHP die Dateinamen (mit Hashes) findet
        manifest: true,
        outDir: 'dist',
        rollupOptions: {
            // Dein Einstiegspunkt für CSS/JS
            input: 'src/main.js',
        },
    },
    server: {
        host: '0.0.0.0', // Wichtig für Docker/DDEV
        origin: 'http://localhost:5173',
        strictPort: true,
        cors: true,
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true, // Hilft, wenn Änderungen nicht erkannt werden
        },
    },
});