/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./assets/**/*.{svelte,js,ts}",
        "./vendor/symfony/twig-bridge/Resources/views/Form/*.html.twig",
        "./templates/**/*.html.twig"
    ],
    theme: {
        extend: {},
    },
    plugins: [],
    darkMode: "class",
}

