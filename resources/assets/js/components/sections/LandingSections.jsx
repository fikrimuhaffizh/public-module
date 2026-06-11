import React from 'react';
import { Link } from '@inertiajs/react';
import { ArrowRight, BookOpen, Building2, CalendarDays, GraduationCap, ShieldCheck, Users } from 'lucide-react';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@public/components/ui/accordion';
import { Badge } from '@public/components/ui/badge';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';

export function Section({ id, eyebrow, title, text, children, dark = false, tint = false, narrow = false }) {
    return <section id={id} className={`section ${dark ? 'section--dark' : ''} ${tint ? 'section--tint' : ''}`}><div className={`shell ${narrow ? 'shell--narrow' : ''}`}>
        <Reveal className="section-heading"><span className="eyebrow">{eyebrow}</span><h2>{title}</h2>{text && <p>{text}</p>}</Reveal>{children}
    </div></section>;
}

export function PagesGrid({ pages }) {
    const icons = [Building2, GraduationCap, BookOpen, Users, ShieldCheck, CalendarDays];
    return <Stagger className="feature-grid">{pages.slice(0, 6).map((page, index) => {
        const Icon = icons[index % icons.length];
        return <SpotlightCard key={page.id} className="feature-card"><span className="feature-icon"><Icon /></span><h3>{page.title}</h3><p>{page.excerpt}</p><Link className="text-link" href={page.url}>Pelajari <ArrowRight size={16} /></Link></SpotlightCard>;
    })}</Stagger>;
}

export function NewsGrid({ announcements, editorial = false }) {
    return <Stagger className={editorial ? 'news-grid news-grid--editorial' : 'news-grid'}>{announcements.map((item, index) =>
        <SpotlightCard key={item.id} className={index === 0 && editorial ? 'news-card news-card--lead' : 'news-card'}>
            <Link href={item.url}><img src={item.image} alt={item.title} className="news-image" /></Link>
            <div className="news-content"><div className="news-meta"><Badge>{item.type}</Badge><span>{item.date}</span></div><h3><Link href={item.url}>{item.title}</Link></h3><p>{item.excerpt}</p><Link className="text-link" href={item.url}>Baca selengkapnya <ArrowRight size={16} /></Link></div>
        </SpotlightCard>)}</Stagger>;
}

export function FaqSection({ faqs }) {
    return <Accordion type="single" collapsible className="faq-list">{faqs.map(faq =>
        <AccordionItem key={faq.id} value={`faq-${faq.id}`}><AccordionTrigger>{faq.question}</AccordionTrigger><AccordionContent><div dangerouslySetInnerHTML={{ __html: faq.answer }} /></AccordionContent></AccordionItem>)}</Accordion>;
}
