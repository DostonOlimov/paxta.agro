import { createRouter, createWebHistory } from 'vue-router';
import StateReport from './components/StateReport.vue';
import FactoryReport from './components/FactoryReport.vue';

const routes = [
    {
        name: 'StateReport',
        path: '/vue/state-report',
        component: StateReport
    },
    {
        name: 'FactoryReport',
        path: '/vue/factory-report/:id',
        component: FactoryReport
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
