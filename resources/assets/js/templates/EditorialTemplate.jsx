import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Button } from '@public/components/ui/button';
import { Reveal } from '@public/components/motion/effects';
import { FaqSection, NewsGrid, PagesGrid, Section } from '@public/components/sections/LandingSections';

export default function EditorialTemplate({ data }) {
    const hero = data.slides[0];
    return <><Reveal className="editorial-hero shell"><div className="editorial-kicker"><span>Wawasan</span><span>Inovasi</span><span>Kolaborasi</span></div><h1>{hero?.title || data.site.name}</h1><div className="editorial-hero-grid"><img src={hero?.image} alt={hero?.title} /><div><p>{hero?.caption || data.site.tagline}</p><Button asChild><a href={hero?.link || '#berita'}>Temukan lebih jauh <ArrowRight size={18} /></a></Button></div></div></Reveal><Section id="berita" eyebrow="Sorotan utama" title="Cerita dan perkembangan terbaru"><NewsGrid announcements={data.announcements} editorial /></Section><section className="editorial-pages shell"><div><span className="eyebrow">Jelajahi kampus</span><h2>Informasi yang membentuk pengalaman akademik</h2></div><PagesGrid pages={data.pages} /></section><Section dark eyebrow="Informasi praktis" title="Pertanyaan umum" narrow><FaqSection faqs={data.faqs} /></Section></>;
}
