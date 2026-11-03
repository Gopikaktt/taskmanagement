import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
    title: (title) => `${title} - TaskFlow`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Helper functions accessible in all Vue components:
        app.config.globalProperties.hasRole = (role) => {
            const roles = props.initialPage.props.auth?.user?.roles || [];
            return roles.includes(role);
        };

        app.config.globalProperties.can = (permission) => {
            const permissions = props.initialPage.props.auth?.user?.permissions || [];
            return permissions.includes(permission);
        };

        app.use(plugin).mount(el);
    },
});
