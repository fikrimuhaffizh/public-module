import React from 'react';
import { ArrowRight, CheckCircle2, MessageCircle } from 'lucide-react';
import { Button } from '@public/components/ui/button';

/**
 * HeroActions — aksi hero yang FOKUS KONVERSI.
 *
 *  • Satu CTA utama: chat WhatsApp bila nomor tersedia (site.whatsapp),
 *    fallback ke buttonPrimary dari CMS. CTA sekunder (outline) tetap ada
 *    sebagai pintu alternatif, bukan penyaing utama.
 *  • Microcopy anti-keberatan di bawah tombol — menjawab keraguan sebelum
 *    muncul: kecepatan respon, gratis, tanpa komitmen. Daftar bisa
 *    di-override per-hero via `hero.microcopy` (array of strings).
 *
 * Dipakai oleh Hero Mode1/2/3 agar perilakunya konsisten.
 * Prop: { hero, site, align }
 */
const DEFAULT_MICROCOPY = [];

export default function HeroActions({ hero, site, align = 'left' }) {
    const wa = site?.whatsapp;
    const microcopy = Array.isArray(hero?.microcopy) && hero.microcopy.length
        ? hero.microcopy
        : DEFAULT_MICROCOPY;

    // CTA utama: WhatsApp kalau ada nomor, selain itu tombol primary CMS.
    const primaryHref = hero?.buttonPrimary?.link || (wa ? `https://wa.me/${wa}` : '#informasi');
    const primaryText = hero?.buttonPrimary?.text || (wa ? 'Chat WhatsApp' : 'Mulai menjelajah');
    const primaryExternal = /^https?:\/\//i.test(primaryHref);

    const secondaryHref = hero?.buttonSecondary?.link || '#berita';
    const secondaryText = hero?.buttonSecondary?.text || 'Kabar terbaru';

    return (
        <div className={`hero-actions hero-actions--stacked hero-actions--${align}`}>
            <div className="hero-actions-row">
                <Button asChild size="lg">
                    <a href={primaryHref} {...(primaryExternal ? { target: '_blank', rel: 'noreferrer' } : {})}>
                        {primaryExternal ? <MessageCircle size={18} /> : null}
                        {primaryText}
                        {primaryExternal ? null : <ArrowRight size={18} />}
                    </a>
                </Button>
                <Button variant="outline" asChild size="lg">
                    <a href={secondaryHref}>{secondaryText}</a>
                </Button>
            </div>
            {microcopy.length > 0 && (
                <ul className="hero-microcopy">
                    {microcopy.map((item, i) => (
                        <li key={i}>
                            <CheckCircle2 size={13} />
                            <span>{item}</span>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}
