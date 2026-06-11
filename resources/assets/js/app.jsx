import React from 'react';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import Home from '@public/pages/Home';
import Contact from '@public/pages/Contact';
import ContentPage from '@public/pages/ContentPage';
import NewsDetail from '@public/pages/NewsDetail';
import NewsIndex from '@public/pages/NewsIndex';

const pages = { Home, Contact, ContentPage, NewsDetail, NewsIndex };

createInertiaApp({
    resolve: (name) => pages[name],
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: { color: '#2563eb', showSpinner: false },
});
