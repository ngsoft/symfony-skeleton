/**
 * Disable active route on navbar
 */
import ElementFinder from "@/modules/utils/element-finder.js";
import dataset from "@/modules/utils/dataset-reader.js";
import {isInt} from "@/modules/utils/index.js";

document.querySelector('.navbar')?.addEventListener('click', e => {
    if (e.target.closest('.active')) {
        e.preventDefault();
    }
});

/**
 * Alert auto-hide
 */

const reducedMotion = matchMedia('(prefers-reduced-motion)')

ElementFinder('[data-autohide].alert', alert => {

    const hide = () => {
        alert.remove();
    };

    let delay = dataset(alert, 'delay');

    if (!isInt(delay)) {
        delay = 2000;
    }
    // delay in seconds
    if (delay < 100) {
        delay *= 1000;
    }

    if (!reducedMotion.matches) {
        alert.addEventListener('animationend', () => {
            alert.classList.remove('opacity-0', 'fade-in');
            setTimeout(() => {
                alert.addEventListener('animationend', hide, {once: true});
                alert.classList.add('fade-out');
            }, delay)

        }, {once: true});
    } else {
        setTimeout(hide, delay);
    }

});
