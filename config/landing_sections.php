<?php

// Base sections configuration
$baseSections = [
    'navbar' => [
        'name' => 'Navbar',
        'area' => 'top',
        'component' => 'NavbarSection',
        'variants' => ['navbar_1' => 'Simple Navbar', 'navbar_2' => 'Navbar + CTA'],
        'default_variant' => 'navbar_1',
        'default_limit' => null,
        'manage_data_route' => null,
    ],
    'hero' => [
        'name' => 'Hero',
        'area' => 'top',
        'component' => 'HeroSection',
        'variants' => ['hero_1' => 'Teks kiri, gambar kanan', 'hero_2' => 'Centered Hero'],
        'default_variant' => 'hero_1',
        'default_limit' => 1,
        'manage_data_route' => 'public.cms.hero.index',
    ],
    'products' => [
        'name' => 'Produk / Modul',
        'area' => 'middle',
        'component' => 'ProductSection',
        'variants' => ['product_1' => 'Card grid', 'product_2' => 'Horizontal showcase'],
        'default_variant' => 'product_1',
        'default_limit' => 6,
        'manage_data_route' => 'public.cms.product.index',
    ],
    'stats' => [
        'name' => 'Statistik',
        'area' => 'middle',
        'component' => 'StatsSection',
        'variants' => ['stats_1' => 'Counter sederhana', 'stats_2' => 'Card statistik'],
        'default_variant' => 'stats_1',
        'default_limit' => 4,
        'manage_data_route' => 'public.cms.statistic.index',
    ],
    'features' => [
        'name' => 'Fitur',
        'area' => 'middle',
        'component' => 'FeatureSection',
        'variants' => ['feature_1' => 'Grid 3 kolom', 'feature_2' => 'Icon card'],
        'default_variant' => 'feature_1',
        'default_limit' => 6,
        'manage_data_route' => 'public.cms.feature.index',
    ],
    'testimonials' => [
        'name' => 'Testimoni',
        'area' => 'middle',
        'component' => 'TestimonialSection',
        'variants' => ['testimonial_1' => 'Card grid', 'testimonial_2' => 'Highlighted'],
        'default_variant' => 'testimonial_1',
        'default_limit' => 3,
        'manage_data_route' => 'public.cms.testimonial.index',
    ],
    'clients' => [
        'name' => 'Klien / Logo',
        'area' => 'middle',
        'component' => 'ClientSection',
        'variants' => ['logos_1' => 'Grid logo', 'logos_2' => 'Logo carousel'],
        'default_variant' => 'logos_1',
        'default_limit' => 8,
        'manage_data_route' => 'public.cms.client.index',
    ],
    'faq' => [
        'name' => 'FAQ',
        'area' => 'middle',
        'component' => 'FaqSection',
        'variants' => ['faq_1' => 'Accordion', 'faq_2' => 'Dua kolom'],
        'default_variant' => 'faq_1',
        'default_limit' => 8,
        'manage_data_route' => 'public.cms.faq.index',
    ],
    'pengumuman' => [
        'name' => 'Pengumuman',
        'area' => 'middle',
        'component' => 'AnnouncementSection',
        'variants' => ['announcement_1' => 'News grid', 'announcement_2' => 'List kompak'],
        'default_variant' => 'announcement_1',
        'default_limit' => 6,
        'manage_data_route' => 'public.cms.pengumuman.index',
    ],
    'cta' => [
        'name' => 'Call To Action',
        'area' => 'bottom',
        'component' => 'CtaSection',
        'variants' => ['cta_1' => 'Simple CTA', 'cta_2' => 'CTA background image'],
        'default_variant' => 'cta_1',
        'default_limit' => 1,
        'manage_data_route' => 'public.cms.cta.index',
    ],
    'footer' => [
        'name' => 'Footer',
        'area' => 'bottom',
        'component' => 'FooterSection',
        'variants' => ['footer_1' => 'Simple footer', 'footer_2' => 'Footer lengkap'],
        'default_variant' => 'footer_2',
        'default_limit' => null,
        'manage_data_route' => null,
    ],
];

// Add fallback keys (singular/plural, pengumuman/announcement)
$sections = $baseSections;

// Singular/plural fallbacks
if (isset($sections['products'])) $sections['product'] = $sections['products'];
if (isset($sections['stats'])) $sections['statistic'] = $sections['stats'];
if (isset($sections['features'])) $sections['feature'] = $sections['features'];
if (isset($sections['testimonials'])) $sections['testimonial'] = $sections['testimonials'];
if (isset($sections['clients'])) $sections['client'] = $sections['clients'];

// Pengumuman/Announcement fallback
if (isset($sections['pengumuman'])) $sections['announcement'] = $sections['pengumuman'];

return [
    'sections' => $sections,
];
