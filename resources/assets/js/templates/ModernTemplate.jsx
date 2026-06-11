import React from 'react';
import { ArrowRight, Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { BackgroundBeams, Marquee, Reveal } from '@public/components/motion/effects';
import { FaqSection, NewsGrid, PagesGrid, Section } from '@public/components/sections/LandingSections';

export default function ModernTemplate({ data }) {
    const hero = data.slides[0];
    return <><section className="hero hero--modern"><BackgroundBeams /><div className="hero-orb hero-orb--one" /><div className="hero-orb hero-orb--two" /><div className="shell modern-hero-grid"><Reveal className="hero-content"><Badge><Sparkles size={14} /> Kampus digital masa depan</Badge><h1>{hero?.title || data.site.name}</h1><p>{hero?.caption || data.site.tagline}</p><div className="hero-actions"><Button asChild><a href={hero?.link || '#informasi'}>Mulai menjelajah <ArrowRight size={18} /></a></Button><Button variant="outline" asChild><a href="#berita">Kabar terbaru</a></Button></div></Reveal><Reveal className="modern-visual" delay={.15}><img src={hero?.image} alt={hero?.title} /><div className="floating-stat"><strong>{data.pages.length}+</strong><span>Layanan informasi</span></div></Reveal></div></section>
    <Marquee items={['Kampus digital','Akses inklusif','Informasi real-time','Pengalaman modern','Ekosistem terhubung']} /><Section id="informasi" eyebrow="Satu ekosistem" title="Semua yang dibutuhkan sivitas akademika"><PagesGrid pages={data.pages} /></Section><Section id="berita" tint eyebrow="Tetap terhubung" title="Kabar terbaru kampus"><NewsGrid announcements={data.announcements} /></Section><Section eyebrow="Butuh jawaban?" title="Kami membantu Anda menemukan informasi" narrow><FaqSection faqs={data.faqs} /></Section></>;
}
