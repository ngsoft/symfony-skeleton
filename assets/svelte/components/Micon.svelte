<script>
    import {MATERIAL_FILLED, MATERIAL_VARIANTS} from "@/fonts/index.js";

    export let
        variant = MATERIAL_FILLED,
        name = null,
        size = 24;

    if (! MATERIAL_VARIANTS.includes(variant)) {
        variant = null;
    }
</script>

{#if variant !== null && name !== null }
    <span class={variant} style="--micon-font-size: {size}px">{name}</span>
{/if}

