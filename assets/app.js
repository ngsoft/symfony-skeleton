/**
 * Uses tailwind display: {.class}; to detect
 */
import ElementFinder from "@/modules/utils/element-finder.js";

if (document.querySelector('.flex, .flex-inline, .block, .inline, .inline-block')) {
    import ('@/tailwind');
}

/**
 * Fonts Tailwind uses Roboto, sans-serif
 * imports material icons + Poppins fonts
 */
import '@/fonts';

/**
 * Disable active route on navbar
 */
document.querySelector('.navbar')?.addEventListener('click', e => {
    if (e.target.closest('.active')) {
        e.preventDefault();
    }
});


/**
 * Load App
 */
if (document.getElementById('app')) {
    import ('@/demo');
}


/**
 * Put it last to override styles
 */
import '@/app.scss';
