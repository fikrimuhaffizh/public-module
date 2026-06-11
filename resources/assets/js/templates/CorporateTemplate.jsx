import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Building2,
    ChartNoAxesCombined,
    CheckCircle2,
    Globe2,
    Layers3,
    Quote,
    ShieldCheck,
} from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@public/components/ui/card';
import { Reveal, Stagger } from '@public/components/motion/effects';
import {
    FaqSection,
    NewsGrid,
    PartnerCloud,
    Section,
} from '@public/components/sections/LandingSections';

const pillars = [
    {
        icon: Layers3,
        title: 'Ekosistem terintegrasi',
        text: 'Informasi, layanan, dan komunikasi institusi hadir dalam satu pengalaman digital.',
    },
    {
        icon: ShieldCheck,
        title: 'Tata kelola terpercaya',
        text: 'Konten terstruktur membantu institusi menjaga akurasi dan konsistensi informasi.',
    },
    {
        icon: ChartNoAxesCombined,
        title: 'Pertumbuhan berkelanjutan',
        text: 'Fondasi digital yang siap berkembang mengikuti kebutuhan dan strategi institusi.',
    },
];

export default function CorporateTemplate({ data }) {
    const hero = data.slides[0];
    const featuredTestimonial = data.testimonials?.[0];

    return (
        <>
            <section className="corporate-hero">
                <div className="corporate-grid-lines" aria-hidden="true" />
                <div className="shell corporate-hero-grid">
                    <Reveal className="corporate-hero-copy">
                        <Badge variant="outline">
                            <Building2 size={14} /> Institutional digital excellence
                        </Badge>
                        <h1>{hero?.title || data.site.name}</h1>
                        <p>{hero?.caption || data.site.tagline}</p>
                        <div className="hero-actions">
                            <Button asChild size="lg">
                                <a href={hero?.link || '#kapabilitas'}>
                                    Jelajahi kapabilitas <ArrowRight />
                                </a>
                            </Button>
                            <Button variant="outline" asChild size="lg">
                                <Link href={data.site.contactUrl}>Hubungi institusi</Link>
                            </Button>
                        </div>
                        <div className="corporate-assurance">
                            <span><CheckCircle2 /> Informasi terkelola</span>
                            <span><CheckCircle2 /> Pengalaman responsif</span>
                            <span><CheckCircle2 /> Siap berkembang</span>
                        </div>
                    </Reveal>

                    <Reveal className="corporate-visual" delay={0.12}>
                        {hero?.image && <img src={hero.image} alt={hero.title || data.site.name} />}
                        <div className="corporate-visual-overlay">
                            <span>Digital presence</span>
                            <strong>{data.site.name}</strong>
                        </div>
                        <Card className="corporate-floating-card">
                            <CardContent>
                                <Globe2 />
                                <div>
                                    <strong>{data.pages.length}+</strong>
                                    <span>Informasi terhubung</span>
                                </div>
                            </CardContent>
                        </Card>
                    </Reveal>
                </div>
            </section>

            <section className="corporate-metrics">
                <div className="shell corporate-metrics-grid">
                    {[
                        [`${data.pages.length}+`, 'Halaman informasi'],
                        [`${data.partners.length}+`, 'Partner kolaborasi'],
                        [`${data.announcements.length}+`, 'Informasi terkini'],
                        ['24/7', 'Akses digital'],
                    ].map(([value, label]) => (
                        <div key={label}><strong>{value}</strong><span>{label}</span></div>
                    ))}
                </div>
            </section>

            <PartnerCloud partners={data.partners} />

            <section id="kapabilitas" className="corporate-capabilities">
                <div className="shell">
                    <Reveal className="corporate-section-heading">
                        <div>
                            <span className="eyebrow">Kapabilitas utama</span>
                            <h2>Fondasi digital untuk institusi yang terus bergerak maju</h2>
                        </div>
                        <p>
                            Dirancang untuk membangun kepercayaan, memperkuat komunikasi,
                            dan menghadirkan layanan informasi yang berkelas.
                        </p>
                    </Reveal>
                    <Stagger className="corporate-bento">
                        {pillars.map(({ icon: Icon, title, text }, index) => (
                            <Card className={index === 0 ? 'corporate-pillar corporate-pillar--lead' : 'corporate-pillar'} key={title}>
                                <CardHeader>
                                    <span className="corporate-pillar-icon"><Icon /></span>
                                    <CardTitle>{title}</CardTitle>
                                    <CardDescription>{text}</CardDescription>
                                </CardHeader>
                                <CardFooter>
                                    <span>0{index + 1}</span>
                                    <ArrowRight />
                                </CardFooter>
                            </Card>
                        ))}
                    </Stagger>
                </div>
            </section>

            {featuredTestimonial && (
                <section className="corporate-quote">
                    <div className="shell">
                        <Reveal className="corporate-quote-card">
                            <Quote />
                            <blockquote>“{featuredTestimonial.quote}”</blockquote>
                            <div>
                                {featuredTestimonial.photo
                                    ? <img src={featuredTestimonial.photo} alt={featuredTestimonial.name} />
                                    : <span>{featuredTestimonial.name.slice(0, 2).toUpperCase()}</span>}
                                <p>
                                    <strong>{featuredTestimonial.name}</strong>
                                    <small>{[featuredTestimonial.position, featuredTestimonial.organization].filter(Boolean).join(' · ')}</small>
                                </p>
                            </div>
                        </Reveal>
                    </div>
                </section>
            )}

            <Section
                eyebrow="Corporate insights"
                title="Informasi dan perkembangan terbaru"
                text="Ikuti agenda, pencapaian, dan kabar penting dari ekosistem institusi."
            >
                <NewsGrid announcements={data.announcements} />
            </Section>

            <Section dark eyebrow="Informasi praktis" title="Pertanyaan yang sering diajukan" narrow>
                <FaqSection faqs={data.faqs} />
            </Section>

            <section className="corporate-closing">
                <div className="shell">
                    <Reveal className="corporate-closing-inner">
                        <div>
                            <span className="eyebrow">Bangun koneksi bernilai</span>
                            <h2>Mari membuka peluang kolaborasi berikutnya.</h2>
                        </div>
                        <Button asChild size="lg">
                            <Link href={data.site.contactUrl}>Mulai percakapan <ArrowRight /></Link>
                        </Button>
                    </Reveal>
                </div>
            </section>
        </>
    );
}
