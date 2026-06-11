import React from 'react';
import { ArrowRight, CheckCircle2, Sparkles } from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { BackgroundBeams, Marquee, Reveal } from '@public/components/motion/effects';
import { FaqSection, NewsGrid, PagesGrid, Section } from '@public/components/sections/LandingSections';

export default function InstitutionalTemplate({ data }) {
    const hero = data.slides[0];
    return <><section className="hero hero--institutional" style={{ backgroundImage: `linear-gradient(90deg, rgba(7,34,65,.94), rgba(7,34,65,.42)), url("${hero?.image}")` }}><BackgroundBeams /><Reveal className="shell hero-content"><Badge><Sparkles size={14} /> Pendidikan yang berdampak</Badge><h1>{hero?.title || data.site.name}</h1><p>{hero?.caption || data.site.tagline}</p><div className="hero-actions"><Button asChild><a href={hero?.link || '#informasi'}>Jelajahi Kampus <ArrowRight size={18} /></a></Button></div></Reveal></section>
    <Marquee items={['Pembelajaran relevan','Layanan kampus terintegrasi','Mutu berbasis data','Kolaborasi industri','Inovasi berkelanjutan']} />
    <section className="trust-strip"><div className="shell trust-grid">{[['Program Terpadu','Pembelajaran relevan dengan kebutuhan masa depan.'],['Ekosistem Digital','Layanan kampus terhubung dalam satu platform.'],['Mutu Terjamin','Peningkatan berkelanjutan berbasis data.']].map(([title,text]) => <div key={title}><CheckCircle2 /><div><strong>{title}</strong><span>{text}</span></div></div>)}</div></section>
    <Section id="informasi" eyebrow="Tentang institusi" title="Informasi utama kampus"><PagesGrid pages={data.pages} /></Section><Section dark eyebrow="Informasi terkini" title="Berita dan pengumuman"><NewsGrid announcements={data.announcements} /></Section><Section eyebrow="Pusat bantuan" title="Pertanyaan yang sering diajukan" narrow><FaqSection faqs={data.faqs} /></Section></>;
}
