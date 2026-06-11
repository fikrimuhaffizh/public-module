import React from 'react';
import { ArrowRight, Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { BackgroundBeams, Marquee, Reveal } from '@public/components/motion/effects';
import {
    CtaSection,
    FaqSection,
    NewsGrid,
    PagesGrid,
    PlatformOverview,
    Section,
    ValueStrip,
} from '@public/components/sections/LandingSections';

export default function ModernTemplate({ data }) {
    const hero = data.slides[0];

    return (
        <>
            <section className="hero hero--modern">
                <BackgroundBeams />
                <div className="hero-orb hero-orb--one" />
                <div className="hero-orb hero-orb--two" />
                <div className="shell modern-hero-grid">
                    <Reveal className="hero-content">
                        <Badge><Sparkles size={14} /> Digital campus platform</Badge>
                        <h1>{hero?.title || data.site.name}</h1>
                        <p>{hero?.caption || data.site.tagline}</p>
                        <div className="hero-actions">
                            <Button asChild size="lg">
                                <a href={hero?.link || '#informasi'}>
                                    Mulai menjelajah <ArrowRight size={18} />
                                </a>
                            </Button>
                            <Button variant="outline" asChild size="lg">
                                <a href="#berita">Kabar terbaru</a>
                            </Button>
                        </div>
                    </Reveal>
                    <Reveal className="modern-visual" delay={0.15}>
                        <div className="modern-browser">
                            <div className="modern-browser-bar"><i /><i /><i /></div>
                            {hero?.image && <img src={hero.image} alt={hero.title || data.site.name} />}
                        </div>
                        <div className="floating-stat">
                            <strong>{data.pages.length}+</strong>
                            <span>Layanan informasi</span>
                        </div>
                        <div className="floating-status">
                            <span className="status-dot" />
                            Sistem informasi aktif
                        </div>
                    </Reveal>
                </div>
            </section>

            <ValueStrip />
            <Marquee items={[
                'Kampus digital',
                'Akses inklusif',
                'Informasi real-time',
                'Pengalaman modern',
                'Ekosistem terhubung',
            ]} />

            <PlatformOverview
                site={data.site}
                image={hero?.image}
                pageCount={data.pages.length}
            />

            <Section
                id="informasi"
                eyebrow="Satu ekosistem"
                title="Semua yang dibutuhkan sivitas akademika"
                text="Pengalaman digital yang sederhana di depan, dengan pengelolaan konten yang terstruktur di belakang."
            >
                <PagesGrid pages={data.pages} />
            </Section>

            <Section id="berita" tint eyebrow="Tetap terhubung" title="Kabar terbaru kampus">
                <NewsGrid announcements={data.announcements} />
            </Section>

            <Section eyebrow="Butuh jawaban?" title="Temukan informasi dengan lebih cepat" narrow>
                <FaqSection faqs={data.faqs} />
            </Section>

            <CtaSection site={data.site} />
        </>
    );
}
