import { createRouter, createWebHistory } from 'vue-router';
import StateReport from './components/StateReport.vue';
import FactoryReport from './components/FactoryReport.vue';
import FactoryApplicationsReport from './components/FactoryApplicationsReport.vue';

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
    },
    {
        name: 'FactoryApplicationsReport',
        path: '/vue/factory-applications/:id',
        component: FactoryApplicationsReport
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
