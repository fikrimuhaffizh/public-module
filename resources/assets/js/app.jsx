import React from 'react';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
const pages = import.meta.glob('./pages/*.jsx');

createInertiaApp({
    resolve: (name) => pages[`./pages/${name}.jsx`](),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: { color: '#2563eb', showSpinner: false },
});
