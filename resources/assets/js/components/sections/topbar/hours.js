import React from 'react';

/**
 * Helper jam operasional — murni, bisa dipakai topbar & section lain.
 * Format yang didukung, contoh:
 *   "Senin–Jumat 08.00–17.00, Sabtu 09.00–14.00"
 *   "Setiap hari 08.00–22.00"
 *   "Senin-Jumat 08:00-17:00, Sabtu Tutup, Minggu Tutup"
 */

const DAY_NAMES = {
    minggu: 0, senin: 1, selasa: 2, rabu: 3, kamis: 4, jumat: 5, sabtu: 6,
    sunday: 0, monday: 1, tuesday: 2, wednesday: 3, thursday: 4, friday: 5, saturday: 6,
};

const DAY_ORDER = Object.keys(DAY_NAMES).sort((a, b) => b.length - a.length);

/** Parse satu baris jam menjadi daftar aturan [{ days: [0..6], start|null, end|null }]. */
export function parseHoursText(text) {
    if (!text) return [];
    const rules = [];
    for (const seg of String(text).split(/[,;]/)) {
        const s = seg.trim();
        if (!s) continue;

        const days = extractDays(s);
        const timeMatch = s.match(/(\d{1,2})[:.](\d{2})\s*[-–—]\s*(\d{1,2})[:.](\d{2})/);

        if (/tutup|libur|closed/i.test(s) || !timeMatch) {
            for (const d of days) rules.push({ days: [d], start: null, end: null });
            continue;
        }

        const start = parseInt(timeMatch[1], 10) * 60 + parseInt(timeMatch[2], 10);
        const end = parseInt(timeMatch[3], 10) * 60 + parseInt(timeMatch[4], 10);
        for (const d of days) rules.push({ days: [d], start, end });
    }
    return rules;
}

/** Hari-hari yang disebut dalam teks (mendukung rentang "Senin–Jumat", "Setiap hari"). */
function extractDays(text) {
    const lower = text.toLowerCase();
    const found = [];
    for (const name of DAY_ORDER) {
        if (lower.includes(name)) found.push(DAY_NAMES[name]);
    }

    if (!found.length) {
        return [0, 1, 2, 3, 4, 5, 6]; // tidak ada hari disebut → semua hari
    }

    const rangeMatch = lower.match(/([a-z]+)\s*[-–—]\s*([a-z]+)/);
    if (rangeMatch) {
        const a = DAY_NAMES[rangeMatch[1]];
        const b = DAY_NAMES[rangeMatch[2]];
        if (a !== undefined && b !== undefined && a !== b) {
            const days = [];
            let i = a;
            while (true) {
                days.push(i);
                if (i === b) break;
                i = (i + 1) % 7;
                if (days.length > 7) break; // pengaman
            }
            return days;
        }
    }

    return [...new Set(found)];
}

/** Status buka/tutup sekarang; null bila jam tidak bisa diparsing. */
export function openStatusNow(text, now = new Date()) {
    const rules = parseHoursText(text);
    if (!rules.length) return null;

    const day = now.getDay();
    const minutes = now.getHours() * 60 + now.getMinutes();
    let matchedDay = false;

    for (const rule of rules) {
        if (!rule.days.includes(day)) continue;
        matchedDay = true;
        if (rule.start === null || rule.end === null) return { open: false, label: 'Tutup' };
        if (rule.start <= rule.end) {
            if (minutes >= rule.start && minutes < rule.end) return { open: true, label: 'Buka' };
        } else if (minutes >= rule.start || minutes < rule.end) {
            return { open: true, label: 'Buka' }; // lewat tengah malam
        }
    }

    return { open: false, label: matchedDay ? 'Tutup' : 'Libur' };
}

/** Segarkan "sekarang" tiap menit (agar status akurat melewati jam buka/tutup). */
export function useNow() {
    const [now, setNow] = React.useState(() => new Date());
    React.useEffect(() => {
        const id = setInterval(() => setNow(new Date()), 60_000);
        return () => clearInterval(id);
    }, []);
    return now;
}
