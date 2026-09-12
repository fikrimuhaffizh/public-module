import React from 'react';
import { Check } from 'lucide-react';
import { Stagger } from '@public/components/motion/effects';

/**
 * CTA Mode 6 — Floating cards: teks center + kartu fitur.
 * Animasi: stagger — kartu muncul satu per satu (budgeted).
 */
export default function CtaMode6({ section, data }) {
    const cta = data.landing?.cta;
    const features = [
        section.settings?.cta_feature_1 || 'Setup 5 menit',
        section.settings?.cta_feature_2 || 'Tanpa kartu kredit',
        section.settings?.cta_feature_3 || 'Support 24/7',
    ];

    return (
        <section className="cta cta--cards">
            <div className="shell" style={{ textAlign: section.settings?.text_align || 'center' }}>
                {section.pre_title && <span className="eyebrow">{section.pre_title}</span>}
                <h2 style={{ color: 'var(--sec-title, inherit)' }}>{section.title || 'Mengapa Memilih Kami?'}</h2>
                <p style={{ color: 'var(--sec-posttext, inherit)', maxWidth: 520, margin: '0 auto 32px' }}>
                    {section.subtitle || section.post_title || ''}
                </p>
                <Stagger className="cta-cards-row" itemClassName="cta-card-item gen-card" budgeted>
                    {features.map((f, i) => (
                        <React.Fragment key={i}>
                            <Check size={20} style={{ color: 'var(--primary)' }} />
                            <span>{f}</span>
                        </React.Fragment>
                    ))}
                </Stagger>
                {cta?.link && (
                    <a className="ui-btn ui-btn--primary ui-btn--lg" href={cta.link} style={{ marginTop: 28 }}>
                        {cta.text || 'Mulai Sekarang'}
                    </a>
                )}
            </div>
        </section>
    );
}
