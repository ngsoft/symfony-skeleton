import './fonts';


let target;

if ((target = document.getElementById('app'))) {

    // dynamic import
    const App = (await import('./App.svelte')).default;
    new App({target});
}


