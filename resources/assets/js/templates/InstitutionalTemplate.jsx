import React from 'react';
import { ArrowRight, CheckCircle2, Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { BackgroundBeams, Marquee, Reveal } from '@public/components/motion/effects';
import {
    CtaSection,
    FaqSection,
    NewsGrid,
    PartnerCloud,
    PagesGrid,
    PlatformOverview,
    Section,
    TestimonialSection,
    ValueStrip,
} from '@public/components/sections/LandingSections';

const trustItems = [
    ['Program Terpadu', 'Pembelajaran relevan dengan kebutuhan masa depan.'],
    ['Ekosistem Digital', 'Layanan kampus terhubung dalam satu platform.'],
    ['Mutu Terjamin', 'Peningkatan berkelanjutan berbasis data.'],
];

export default function InstitutionalTemplate({ data }) {
    const hero = data.slides[0];
    const heroStyle = hero?.image
        ? { backgroundImage: `linear-gradient(90deg, rgba(7,34,65,.96), rgba(7,34,65,.42)), url("${hero.image}")` }
        : undefined;

    return (
        <>
            <section className="hero hero--institutional" style={heroStyle}>
                <BackgroundBeams />
                <Reveal className="shell hero-content">
                    <Badge><Sparkles size={14} /> Institusi modern, layanan terintegrasi</Badge>
                    <h1>{hero?.title || data.site.name}</h1>
                    <p>{hero?.caption || data.site.tagline}</p>
                    <div className="hero-actions">
                        <Button asChild size="lg">
                            <a href={hero?.link || '#informasi'}>
                                Jelajahi platform <ArrowRight size={18} />
                            </a>
                        </Button>
                        <Button variant="outline" asChild size="lg">
                            <a href="#berita">Lihat kabar terbaru</a>
                        </Button>
                    </div>
                </Reveal>
            </section>

            <ValueStrip />
            <Marquee items={[
                'Pembelajaran relevan',
                'Layanan kampus terintegrasi',
                'Mutu berbasis data',
                'Kolaborasi industri',
                'Inovasi berkelanjutan',
            ]} />

            <section className="trust-strip">
                <div className="shell trust-grid">
                    {trustItems.map(([title, text]) => (
                        <div key={title}>
                            <CheckCircle2 />
                            <div><strong>{title}</strong><span>{text}</span></div>
                        </div>
                    ))}
                </div>
            </section>

            <PlatformOverview
                site={data.site}
                image={hero?.image}
                pageCount={data.pages.length}
            />
            <PartnerCloud partners={data.partners} />

            <Section
                id="informasi"
                eyebrow="Kapabilitas institusi"
                title="Informasi utama dalam satu ekosistem"
                text="Akses profil, layanan, program, dan informasi penting melalui pengalaman digital yang konsisten."
            >
                <PagesGrid pages={data.pages} />
            </Section>

            <TestimonialSection testimonials={data.testimonials} />

            <Section id="berita" dark eyebrow="Informasi terkini" title="Berita dan pengumuman">
                <NewsGrid announcements={data.announcements} />
            </Section>

            <Section eyebrow="Pusat bantuan" title="Pertanyaan yang sering diajukan" narrow>
                <FaqSection faqs={data.faqs} />
            </Section>

            <CtaSection site={data.site} />
        </>
    );
}
