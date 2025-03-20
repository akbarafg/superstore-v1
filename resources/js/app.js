import './bootstrap';
import 'flowbite';
import '../css/app.css';
import 'gridjs/dist/theme/mermaid.css';

import PhosphorIcons from "@phosphor-icons/vue";

import { createApp, h } from 'vue';
import { i18nVue } from 'laravel-vue-i18n';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18n } from './i18n';


const appName = import.meta.env.VITE_APP_NAME || 'SUPERSTORE';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18nVue, {
                resolve: lang => {
                    const langs = import.meta.glob('../../lang/*.json', { eager: true });
                    return langs[`../../lang/${lang}.json`].default;
                },
            })
            .use(ZiggyVue)
            .use(PhosphorIcons)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
