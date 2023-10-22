import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import { svelte } from '@sveltejs/vite-plugin-svelte';

/* if you're using React */
// import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        /* react(), // if you're using React */
        svelte(),
        symfonyPlugin(),
    ],
    build: {
        rollupOptions: {
            input: {
                app: "./assets/app.js"
            },
        }
    },
});
