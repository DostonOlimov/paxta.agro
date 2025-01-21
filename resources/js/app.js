import { createApp } from 'vue';
import App from './App.vue'; // Main Vue component
import router from './router.js'; // Vue Router setup

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const appElement = document.querySelector('#app');
if (appElement) {
    const app = createApp(App); // Create Vue app

    app.component('VueDatePicker', VueDatePicker);

    app.use(router);           // Use Vue Router
    app.mount('#app');         // Mount to DOM element
}
