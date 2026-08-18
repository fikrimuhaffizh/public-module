import { useEffect } from 'react';

/**
 * usePauseOffscreenAnimations — hentikan animasi CSS tak-terbatas pada elemen
 * yang tidak terlihat (di luar viewport) untuk menghemat kompositing/paint.
 *
 * Elemen ditandai dengan atribut `data-pause-offscreen`; saat keluar viewport
 * (dengan margin 120px) `animation-play-state` di-set paused, dan dilanjutkan
 * saat kembali terlihat. Scan ulang otomatis memakai MutationObserver sehingga
 * elemen baru (Inertia page transition) ikut terpantau.
 */
export function usePauseOffscreenAnimations() {
    useEffect(() => {
        const observed = new WeakSet();
        const observer = new IntersectionObserver((entries) => {
            for (const entry of entries) {
                const el = entry.target;
                // '' mengembalikan kontrol ke CSS (termasuk :hover pause).
                el.style.animationPlayState = entry.isIntersecting ? '' : 'paused';
            }
        }, { rootMargin: '120px 0px', threshold: 0 });

        const scan = () => {
            for (const el of document.querySelectorAll('[data-pause-offscreen]')) {
                if (!observed.has(el)) {
                    observed.add(el);
                    observer.observe(el);
                }
            }
        };

        scan();
        const mo = new MutationObserver(scan);
        mo.observe(document.body, { childList: true, subtree: true });

        return () => {
            mo.disconnect();
            observer.disconnect();
        };
    }, []);
}
