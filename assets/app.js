/**
 * Fonts
 * imports material icons + Poppins fonts
 */
import '@/fonts';
/**
 * Uses tailwind display: {.class}; to detect
 */
if (document.querySelector('.flex, .flex-inline, .block, .inline, .inline-block')) {
    import ('@/tailwind');
}


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
