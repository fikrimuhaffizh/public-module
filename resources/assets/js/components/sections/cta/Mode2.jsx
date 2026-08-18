import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Button } from '@public/components/ui/button';

/** CTA Mode 2 — banner penuh dengan latar gambar + overlay. Prop: { section, data } */
export default function CtaMode2({ section, data }) {
    const cta = data.landing?.cta;
    const title = section?.title || cta?.title || 'Siap modernisasi kampus Anda?';
    const text = section?.subtitle || cta?.description || 'Jadwalkan demo gratis.';
    return (
        <section className="cta-banner">
            {cta?.backgroundImage && <img src={cta.backgroundImage} alt="" aria-hidden="true" />}
            <div className="cta-banner-scrim" />
            <div className="shell cta-banner-inner">
                {section?.pre_title && <span className="eyebrow eyebrow--light">{section.pre_title}</span>}
                <h2>{title}</h2>
                <p>{text}</p>
                <Button asChild size="lg">
                    <a href={cta?.buttonLink || data.site.contactUrl}>
                        {cta?.buttonText || 'Hubungi kami'} <ArrowRight size={18} />
                    </a>
                </Button>
            </div>
        </section>
    );
}
