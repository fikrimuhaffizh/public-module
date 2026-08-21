import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Button } from '@public/components/ui/button';

/** CTA Mode 3 — band split: teks di kiri, tombol di kanan. Prop: { section, data } */
export default function CtaMode3({ section, data }) {
    const cta = data.landing?.cta;
    const title = section?.title || cta?.title || 'Siap modernisasi kampus Anda?';
    const text = section?.subtitle || cta?.description || 'Jadwalkan demo gratis.';
    return (
        <section className="cta-split">
            <div className="shell cta-split-inner">
                <div>
                    {section?.pre_title && <span className="eyebrow" style={{ color: 'var(--sec-pretext, inherit)' }}>{section.pre_title}</span>}
                    <h2 style={{ color: 'var(--sec-title, inherit)' }}>{title}</h2>
                    <p style={{ color: 'var(--sec-posttext, inherit)' }}>{text}</p>
                </div>
                <Button asChild size="lg">
                    <a href={cta?.buttonLink || data.site.contactUrl}>
                        {cta?.buttonText || 'Hubungi kami'} <ArrowRight size={18} />
                    </a>
                </Button>
            </div>
        </section>
    );
}
