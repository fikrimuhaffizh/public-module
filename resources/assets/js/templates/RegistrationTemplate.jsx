import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Award,
    BookOpen,
    CalendarCheck,
    CheckCircle2,
    ChevronRight,
    FileText,
    GraduationCap,
    MapPin,
    Quote,
    Sparkles,
    Star,
    Upload,
    Users,
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
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';
import {
    FaqSection,
    NewsGrid,
    Section,
    SectionHeader,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

const steps = [
    {
        icon: FileText,
        title: 'Isi data diri',
        text: 'Lengkapi informasi pribadi dan pilih program yang sesuai.',
    },
    {
        icon: Upload,
        title: 'Unggah dokumen',
        text: 'Lampirkan berkas persyaratan secara digital.',
    },
    {
        icon: CheckCircle2,
        title: 'Verifikasi & konfirmasi',
        text: 'Tim kami akan memverifikasi dan mengonfirmasi pendaftaran Anda.',
    },
];

const benefits = [
    'Proses pendaftaran online, tanpa datang ke kampus',
    'Konfirmasi cepat dalam 1x24 jam',
    'Panduan lengkap setiap tahap pendaftaran',
    'Akses informasi biaya dan beasiswa',
];

export default function RegistrationTemplate({ data }) {
    const sections = data.sections || [];
    const { landing } = data;
    const hero = landing?.hero;

        const renderSection = (section) => {
        if (!section.is_active) return null;
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}
