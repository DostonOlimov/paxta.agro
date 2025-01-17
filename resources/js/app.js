import { createApp } from 'vue';
import App from './App.vue'; // Main Vue component
import router from './router.js'; // Vue Router setup

const appElement = document.querySelector('#app');
if (appElement) {
    const app = createApp(App); // Create Vue app
    app.use(router);           // Use Vue Router
    app.mount('#app');         // Mount to DOM element
}
