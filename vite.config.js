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
                        subset: 'latin-ext'
                    },
                    {
                        name: 'Poppins',
                        weights: [300, 400, 500, 600, 700, 800, 900],
                        styles: ['italic'],
                        subset: 'latin-ext'
                    }
                ],
            },

        }),
    ],
    build: {
        target: "esnext",
        rollupOptions: {
            input: {
                app: "./assets/app.js"
            },
        }
    },
});
