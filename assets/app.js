import './fonts';
import "./app.scss";
import "./_svelte-app.scss";
import 'flowbite';

import App from './App.svelte';

const target = document.getElementById('app') ?? document.createElement('div');


const app = new App({
    target,
});

export default app;