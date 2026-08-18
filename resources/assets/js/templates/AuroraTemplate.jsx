import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    BookOpen,
    CalendarDays,
    CheckCircle2,
    GraduationCap,
    Layers3,
    Moon,
    Quote,
    ShieldCheck,
    Sparkles,
    Star,
    Sun,
    Users,
    Zap,
} from 'lucide-react';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@public/components/ui/accordion';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import { FaqSection, NewsGrid, Section, SectionHeader, combinedText, heroCopy, sectionKey } from '@public/components/sections/LandingSections';

const icons = [BookOpen, GraduationCap, Users, ShieldCheck, Layers3, CalendarDays];

export default function AuroraTemplate({ data }) {
    const sections = data.sections || [];
    const { landing, site } = data;
    const hero = landing?.hero;

    const [isDark, setIsDark] = useState(() => {
        if (typeof window === 'undefined') return true;
        return localStorage.getItem('aurora-theme') !== 'light';
    });

    useEffect(() => {
        const wrapper = document.querySelector('.theme-aurora');
        if (wrapper) {
            wrapper.setAttribute('data-theme', isDark ? 'dark' : 'light');
        }
        localStorage.setItem('aurora-theme', isDark ? 'dark' : 'light');
    }, [isDark]);

    const toggleTheme = () => setIsDark(prev => !prev);

        const renderSection = (section) => {
        if (!section.is_active) return null;
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
