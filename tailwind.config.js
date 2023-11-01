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
    darkMode: "class",
}