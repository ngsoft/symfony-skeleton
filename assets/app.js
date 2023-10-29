import './fonts';

let target;

if ((target = document.getElementById('app'))) {

    // dynamic import
    const App = (await import('./App.svelte')).default;
    new App({target});
} else if (document.querySelector('.tailwind')) { // detects element with tailwind class
    import('./tailwind');
}


