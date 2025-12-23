import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ThemeProvider } from '@/Components/theme-provider';

const appName = import.meta.env.VITE_APP_NAME || 'Baitul Jannah';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.tsx`, import.meta.glob('./Pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <ThemeProvider defaultTheme="system" storageKey="baitul-jannah-theme">
                <App {...props} />
            </ThemeProvider>
        );
    },
    progress: {
        color: '#6366f1',
    },
});
