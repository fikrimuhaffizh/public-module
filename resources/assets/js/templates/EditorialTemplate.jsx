import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { Reveal } from '@public/components/motion/effects';
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

export default function EditorialTemplate({ data }) {
    const sections = data.sections || [];
    const hero = data.landing?.hero;

    const renderSection = (section) => {
        if (!section.is_active) return null;
        const key = section.landing_section_id;

        switch (section.section_key) {
            case 'hero':
                return (
                    <React.Fragment key={key}>
                        <Reveal className="editorial-hero shell">
                            <div className="editorial-kicker">
                                <span>Wawasan</span>
                                <span>Inovasi</span>
                                <span>Kolaborasi</span>
                                <span>Transformasi digital</span>
                            </div>
                            <h1>{hero?.title || data.site.name}</h1>
                            {hero?.subtitle && <p className="editorial-hero-sub">{hero.subtitle}</p>}
                            <div className="editorial-hero-grid">
                                <div className="editorial-image-frame">
                                    {hero?.image && <img src={hero.image} alt={hero.title || data.site.name} />}
                                    <span>{section.post_title || 'Platform informasi institusi'}</span>
                                </div>
                                <div className="editorial-summary">
                                    <p>{hero?.description || data.site.tagline}</p>
                                    <Button asChild size="lg">
                                        <a href={hero?.buttonPrimary?.link || '#berita'}>
                                            {hero?.buttonPrimary?.text || 'Temukan lebih jauh'} <ArrowRight size={18} />
                                        </a>
                                    </Button>
                                </div>
                            </div>
                        </Reveal>
                        <ValueStrip />
                    </React.Fragment>
                );

            case 'features':
            case 'feature':
                return (
                    <PlatformOverview key={key}
                        site={data.site}
                        image={hero?.image}
                        pageCount={data.pages.length}
                    />
                );

            case 'clients':
            case 'client':
                return <PartnerCloud key={key} partners={data.partners} />;

            case 'pengumuman':
            case 'announcement':
                return (
                    <Section key={key} id="berita" eyebrow={section.pre_title || 'Sorotan utama'} title={section.title || 'Cerita dan perkembangan terbaru'}>
                        <NewsGrid announcements={data.announcements} editorial />
                    </Section>
                );

            case 'products':
            case 'product':
                return (
                    <section key={key} className="editorial-pages shell">
                        <div>
                            <span className="eyebrow">{section.pre_title || 'Jelajahi institusi'}</span>
                            <h2>{section.title || 'Informasi yang membentuk pengalaman akademik'}</h2>
                            <p>{section.subtitle || 'Akses cepat menuju profil, layanan, program, dan sumber informasi penting.'}</p>
                        </div>
                        <PagesGrid pages={data.pages} />
                    </section>
                );

            case 'testimonials':
            case 'testimonial':
                return <TestimonialSection key={key} testimonials={data.testimonials} />;

            case 'faq':
                return (
                    <Section key={key} dark eyebrow={section.pre_title || 'Informasi praktis'} title={section.title || 'Pertanyaan umum'} narrow>
                        <FaqSection faqs={data.faqs} />
                    </Section>
                );

            case 'cta':
                return <CtaSection key={key} site={data.site} />;

            default:
                return null;
        }
    };

    return <>{sections.map(renderSection)}</>;
}
