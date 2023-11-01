import elements from 'tw-elements/dist/plugin.cjs';


/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./assets/**/*.{svelte,js}",
        "./templates/**/*.twig",
        "./src/Form/**/*.php",
        "./node_modules/tw-elements/dist/js/**/*.js",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        elements
    ],
    // added compatibility with other frameworks
    darkMode: ['class', '[data-mode="dark"]', '[data-bs-theme="dark"]'],
}