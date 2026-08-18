import React from 'react';
import { usePage } from '@inertiajs/react';

/** Ikon WhatsApp resmi (path SVG) — lebih dikenali daripada ikon generik. */
function WhatsAppIcon({ size = 24 }) {
    return (
        <svg width={size} height={size} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12.04 2a9.9 9.9 0 0 0-8.5 14.95L2 22l5.18-1.5A9.94 9.94 0 1 0 12.04 2Zm0 1.67a8.27 8.27 0 1 1-4.2 15.4l-.3-.18-3.07.89.9-2.99-.2-.3a8.27 8.27 0 0 1 6.87-12.82Zm-2.94 3.3c-.2 0-.52.07-.8.36-.27.29-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.21 3.07.15.2 2.02 3.2 4.98 4.37 2.45.97 2.95.78 3.48.73.53-.05 1.71-.7 1.95-1.37.24-.68.24-1.26.17-1.38-.07-.12-.27-.2-.56-.34-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.25-.46-2.38-1.47a8.94 8.94 0 0 1-1.65-2.05c-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.49.1-.2.05-.37-.02-.52-.08-.15-.66-1.63-.93-2.24-.23-.57-.48-.55-.67-.56l-.57-.02Z" />
        </svg>
    );
}

/**
 * Floating WhatsApp button — muncul di SEMUA halaman landing (dipakai di
 * PublicLayout). Nomor dikonfigurasi dari CMS: LandingPageSetting.whatsapp
 * (Cth: 6281234567890). Tidak dirender bila nomor kosong. Pesan default bisa
 * disesuaikan lewat prop `message` (opsional).
 */
export default function FloatingWhatsApp({ message = 'Halo, saya ingin bertanya tentang layanan Anda.' }) {
    const { site } = usePage().props;
    const wa = site?.whatsapp;
    if (!wa) return null;

    const digits = String(wa).replace(/\D/g, '');
    if (!digits) return null;

    const href = `https://wa.me/${digits}?text=${encodeURIComponent(message)}`;

    return (
        <a
            className="wa-float"
            href={href}
            target="_blank"
            rel="noreferrer"
            aria-label="Chat WhatsApp"
            title="Chat WhatsApp"
        >
            <span className="wa-float-icon"><WhatsAppIcon /></span>
            <span className="wa-float-label">Chat WhatsApp</span>
        </a>
    );
}
