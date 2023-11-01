import {fileURLToPath, URL} from 'node:url';
import {defineConfig} from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import {svelte} from '@sveltejs/vite-plugin-svelte';
import Unfonts from 'unplugin-fonts/vite'

/* if you're using React */
// import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        /* react(), // if you're using React */
        svelte(),
        symfonyPlugin(),
        Unfonts({
            display: 'auto',
            fontsource: {
                families: [
                    {
                        name: 'Poppins',
                        weights: [300, 400, 500, 600, 700, 800, 900],

                    },
                    {
                        name: 'Poppins',
                        weights: [300, 400, 500, 600, 700, 800, 900],
                        styles: ['italic'],

                    },

                    'Material Icons',
                    'Material Icons Round',
                    'Material Icons Outlined',
                    'Material Icons Sharp',
                    'Material Icons Two Tone',
                ],
            },

        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./assets', import.meta.url))
        }
    },
    build: {
        target: "esnext",
        chunkSizeWarningLimit: 1024,
        rollupOptions: {
            input: {
                app: "./assets/app.js"
            },

        }
    },
});
