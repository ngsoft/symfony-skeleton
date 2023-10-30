import './fonts';
import './app.scss';


let target;

if ((target = document.getElementById('app'))) {

    // dynamic import
    const App = (await import('./demo/App.svelte')).default;
    new App({target});
}




