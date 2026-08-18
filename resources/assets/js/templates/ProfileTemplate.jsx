import { SectionVariantRenderer } from '@public/components/sections/renderer';
import React from 'react';
import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Award,
    Banknote,
    BarChart3,
    BookOpen,
    Building2,
    CheckCircle2,
    ChevronRight,
    Clock,
    Compass,
    ExternalLink,
    GraduationCap,
    HeartHandshake,
    Lightbulb,
    MapPin,
    MessageSquare,
    Phone,
    Quote,
    Shield,
    Target,
    TrendingUp,
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
    CtaSection,
    FaqSection,
    NewsGrid,
    Section,
    SectionHeader,
    combinedText,
    heroCopy,
    sectionKey,
} from '@public/components/sections/LandingSections';

// ─── Premium milestones (company journey) ─────────────────────────────────
const milestones = [
    {
        year: '2018',
        icon: Compass,
        title: 'Perencanaan & Fondasi',
        text: 'Menyusun arsitektur sistem dan kebutuhan teknis bersama tim ahli multidisiplin.',
    },
    {
        year: '2020',
        icon: Building2,
        title: 'Pengembangan & Integrasi',
        text: 'Membangun dan mengintegrasikan modul-modul inti sistem informasi terpadu.',
    },
    {
        year: '2022',
        icon: TrendingUp,
        title: 'Implementasi & Skala',
        text: 'Meluncurkan platform dan mengoptimalkan berdasarkan umpan balik pengguna di 50+ institusi.',
    },
    {
        year: '2024',
        icon: Award,
        title: 'Ekspansi & Inovasi',
        text: 'Menjangkau lebih banyak institusi dengan solusi AI dan analitik cerdas.',
    },
];

// ─── Core values (used as feature section content) ─────────────────────────
const values = [
    {
        icon: Target,
        title: 'Visi Strategis',
        text: 'Kami percaya teknologi adalah katalis untuk transformasi pendidikan yang lebih inklusif dan berdaya saing.',
    },
    {
        icon: HeartHandshake,
        title: 'Kolaborasi Erat',
        text: 'Bekerja bersama institusi untuk menghadirkan solusi yang tepat sasaran dan berkelanjutan.',
    },
    {
        icon: Lightbulb,
        title: 'Inovasi Berkelanjutan',
        text: 'Terus mengembangkan sistem mengikuti kebutuhan dan perkembangan zaman dengan riset mendalam.',
    },
];

// ─── Contact info sidebar items ───────────────────────────────────────────
const contactItems = [
    { icon: MapPin, label: 'Kantor Pusat', value: 'Jl. Pendidikan No. 123, Jakarta' },
    { icon: Phone, label: 'Telepon', value: '+62 21 1234 5678' },
    { icon: MessageSquare, label: 'Email', value: 'hello@perusahaan.co.id' },
    { icon: Clock, label: 'Jam Kerja', value: 'Sen–Jum, 08:00–17:00 WIB' },
];

// ─── Stat cards data ──────────────────────────────────────────────────────
const statCards = [
    { icon: Building2, value: '50+', label: 'Institusi Mitra' },
    { icon: Users, value: '10.000+', label: 'Pengguna Aktif' },
    { icon: BarChart3, value: '98%', label: 'Kepuasan Klien' },
    { icon: Award, value: '5', label: 'Penghargaan' },
];

// ─── Service cards (shown when no products) ────────────────────────────────
const serviceCards = [
    {
        icon: GraduationCap,
        title: 'Sistem Akademik',
        text: 'Platform manajemen akademikmenyeluruh yang mencakup kurikulum, KRS, nilai, dan wisuda.',
    },
    {
        icon: Banknote,
        title: 'Manajemen Keuangan',
        text: 'Modul keuangan terintegrasi untuk UKT, beasiswa, gaji, dan pelaporan real-time.',
    },
    {
        icon: Shield,
        title: 'Keamanan Data',
        text: 'Sistem enkripsi multi-layer dan kepatuhan terhadap standar perlindungan data nasional.',
    },
    {
        icon: BookOpen,
        title: 'E-Learning',
        text: 'LMS modern dengan dukungan konten interaktif, forum diskusi, dan penilaian otomatis.',
    },
    {
        icon: BarChart3,
        title: 'Analitik & AI',
        text: 'Dashboard prediktif berbasis AI untuk mengidentifikasi tren dan risiko akademik.',
    },
    {
        icon: Users,
        title: 'SDM & Organisasi',
        text: 'Manajemen sumber daya manusia dari rekrutmen hingga pengembangan karir.',
    },
];

export default function ProfileTemplate({ data }) {
    const sections = data.sections || [];
    const { landing } = data;
    const hero = landing?.hero;

        const renderSection = (section) => {
        if (!section.is_active) return null;
        return <SectionVariantRenderer key={section.landing_section_id} section={section} data={data} />;
    };

    return <>{sections.map(renderSection)}</>;

}