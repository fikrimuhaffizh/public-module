import React, { useEffect, useMemo, useRef, useState } from 'react';
import { animate, useInView } from 'framer-motion';

/**
 * Parsing nilai statistik — dukung format Indonesia: "15.000+" → 15000,
 * "98,5%" → 98.5. Titik = pemisah ribuan, koma = desimal.
 */
function parseStatValue(raw) {
    const str = String(raw ?? '').trim();
    const match = str.match(/^(-?[\d.,]+)(.*)$/);
    if (!match) return { number: 0, decimals: 0, suffix: str };

    const numStr = match[1].replace(/\./g, '').replace(',', '.');
    const number = Number.parseFloat(numStr);
    const decimals = match[1].includes(',') ? (match[1].split(',')[1] || '').length : 0;

    return { number: Number.isFinite(number) ? number : 0, decimals, suffix: match[2] };
}

function formatCount(value, decimals) {
    if (decimals > 0) return value.toFixed(decimals);
    return Math.round(value).toLocaleString('id-ID');
}

/**
 * Angka berjalan naik (count-up) saat masuk viewport.
 * Perilaku DEFAULT semua mode Statistik — bukan variant tersendiri.
 */
export default function CountUp({ value, duration = 1.6 }) {
    const ref = useRef(null);
    const inView = useInView(ref, { once: true, amount: 0.5 });
    const { number, decimals, suffix } = useMemo(() => parseStatValue(value), [value]);
    const [display, setDisplay] = useState(() => formatCount(0, decimals) + suffix);

    useEffect(() => {
        if (!inView) return undefined;
        const controls = animate(0, number, {
            duration,
            ease: [0.22, 1, 0.36, 1],
            onUpdate: v => setDisplay(formatCount(v, decimals) + suffix),
        });

        return () => controls.stop();
    }, [inView, number, decimals, suffix, duration]);

    return <span ref={ref}>{display}</span>;
}
