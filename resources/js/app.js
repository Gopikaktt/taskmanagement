import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp, usePage } from '@inertiajs/vue3';

createInertiaApp({
    title: (title) => `${title} - TaskFlow`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Helper functions accessible reactively across all Vue components:
        app.config.globalProperties.hasRole = (role) => {
            try {
                const roles = usePage().props.auth?.user?.roles || props.initialPage.props.auth?.user?.roles || [];
                return Array.isArray(roles) && roles.includes(role);
            } catch (e) {
                return false;
            }
        };

        app.config.globalProperties.can = (permission) => {
            try {
                const permissions = usePage().props.auth?.user?.permissions || props.initialPage.props.auth?.user?.permissions || [];
                return Array.isArray(permissions) && permissions.includes(permission);
            } catch (e) {
                return false;
            }
        };

        app.use(plugin).mount(el);
    },
});
