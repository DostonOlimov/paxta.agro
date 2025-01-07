import { createApp } from 'vue';
import App from './components/App.vue';

const appElement = document.querySelector('#app');
if (appElement) {
    const app = createApp(App);
    app.mount('#app');
}
