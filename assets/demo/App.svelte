<script>
    import "./demo.scss";
    import svelteLogo from "../pictures/svelte.svg";
    import viteLogo from "../pictures/vite.svg";
    import Counter from "./lib/Counter.svelte";
    import Micon from "../svelte/components/Micon.svelte";
    import {MATERIAL_OUTLINED} from "../fonts/index.js";


</script>

<main>
    <div class="flex justify-center">
        <a href="https://vitejs.dev" target="_blank" rel="noreferrer">
            <img src={viteLogo} class="logo" alt="Vite Logo"/>
        </a>
        <a href="https://svelte.dev" target="_blank" rel="noreferrer">
            <img src={svelteLogo} class="logo svelte" alt="Svelte Logo"/>
        </a>
    </div>
    <h1>Vite + Svelte</h1>

    <div class="_card">
        <Counter/>
    </div>

    <p>
        <Micon name="info" variant={MATERIAL_OUTLINED}></Micon>
        Check out <a
            href="https://github.com/sveltejs/kit#readme"
            target="_blank"
            rel="noreferrer">SvelteKit</a
    >, the official Svelte app framework powered by Vite!
    </p>

    <p class="read-the-docs">Click on the Vite and Svelte logos to learn more</p>
</main>

<style lang="scss">

  .logo {
    box-sizing: content-box;
    height: 6em;
    padding: 1.5em;
    will-change: filter;
    transition: filter 300ms;
  }

  .logo:hover {
    filter: drop-shadow(0 0 2em #646cffaa);
  }

  .logo.svelte:hover {
    filter: drop-shadow(0 0 2em #ff3e00aa);
  }

  .read-the-docs {
    color: #888;
  }
</style>
