/**
 * Uses tailwind display: {.class}; to detect
 */
if (document.querySelector('.flex, .flex-inline, .block, .inline, .inline-block')) {
    import ('@/tailwind');
}

/**
 * Fonts Tailwind uses Roboto, sans-serif
 * imports material icons + Poppins fonts
 */
import '@/fonts';


/**
 * Dynamic Import Symfony App
 */
import ('@/app/index.js');


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
