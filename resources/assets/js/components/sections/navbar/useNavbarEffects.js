import { useState, useEffect } from 'react';

/**
 * Scroll Shadow — returns true when the page has been scrolled past 10px.
 * Used by all navbar modes to add a subtle shadow on scroll.
 */
export function useScrollShadow(threshold = 10) {
    const [scrolled, setScrolled] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > threshold);
        onScroll(); // check initial state
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, [threshold]);

    return scrolled;
}

/**
 * Active Page Detection — compares a menu URL against the current Inertia URL.
 * Handles: exact match, startsWith (for sub-pages), trailing slash normalization.
 */
export function isMenuActive(menuUrl, currentUrl) {
    if (!menuUrl || !currentUrl) return false;

    // Normalize: strip trailing slash, lowercase
    const normalize = (u) => u.replace(/\/+$/, '').toLowerCase();
    const menu = normalize(menuUrl);
    const current = normalize(currentUrl);

    // Exact match (home page)
    if (menu === '' || menu === '/') {
        return current === '' || current === '/';
    }

    // Exact match or startsWith (sub-pages)
    return current === menu || current.startsWith(menu + '/');
}
