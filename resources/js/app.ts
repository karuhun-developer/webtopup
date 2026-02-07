import '../css/app.css';

import { createInertiaApp, usePage } from '@inertiajs/vue3';
import { putConfig, renderApp } from '@inertiaui/modal-vue';
import { VueQueryPlugin } from '@tanstack/vue-query';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp } from 'vue';
import { initializeTheme } from './composables/useAppearance';

const app = usePage();

createInertiaApp({
    title: (title) => (title ? `${title} - ${app.props.name}` : app.props.name),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: renderApp(App, props) })
            .use(plugin)
            .use(VueQueryPlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

putConfig({
    type: 'modal',
    navigate: false,
    modal: {
        closeButton: true,
        closeExplicitly: false,
        maxWidth: '2xl',
        paddingClasses: 'p-4 sm:p-6',
        panelClasses: 'bg-(--background) rounded dark:bg-gray-800',
        position: 'center',
    },
    slideover: {
        closeButton: true,
        closeExplicitly: false,
        maxWidth: '2xl',
        paddingClasses: 'p-4 sm:p-6',
        panelClasses: 'bg-(--background) min-h-screen border-l',
        position: 'right',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
