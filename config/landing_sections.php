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
        'manage_data_route' => null,
    ],
    'product' => [
        'name' => 'Produk / Modul',
        'area' => 'middle',
        'component' => 'ProductSection',
        'variants' => ['product_1' => 'Card grid', 'product_2' => 'Horizontal showcase'],
        'default_variant' => 'product_1',
        'default_limit' => 6,
        'manage_data_route' => 'cms.product.index',
    ],
    'statistic' => [
        'name' => 'Statistik',
        'area' => 'middle',
        'component' => 'StatsSection',
        'variants' => ['stats_1' => 'Counter sederhana', 'stats_2' => 'Card statistik'],
        'default_variant' => 'stats_1',
        'default_limit' => 4,
        'manage_data_route' => 'cms.statistic.index',
    ],
    'feature' => [
        'name' => 'Fitur',
        'area' => 'middle',
        'component' => 'FeatureSection',
        'variants' => ['feature_1' => 'Grid 3 kolom', 'feature_2' => 'Icon card'],
        'default_variant' => 'feature_1',
        'default_limit' => 6,
        'manage_data_route' => 'cms.feature.index',
    ],
    'testimonial' => [
        'name' => 'Testimoni',
        'area' => 'middle',
        'component' => 'TestimonialSection',
        'variants' => ['testimonial_1' => 'Card grid', 'testimonial_2' => 'Highlighted'],
        'default_variant' => 'testimonial_1',
        'default_limit' => 3,
        'manage_data_route' => 'cms.testimonial.index',
    ],
    'client' => [
        'name' => 'Client',
        'area' => 'middle',
        'component' => 'ClientSection',
        'variants' => ['logos_1' => 'Grid logo', 'logos_2' => 'Logo carousel'],
        'default_variant' => 'logos_1',
        'default_limit' => 8,
        'manage_data_route' => 'cms.client.index',
    ],
    'faq' => [
        'name' => 'FAQ',
        'area' => 'middle',
        'component' => 'FaqSection',
        'variants' => ['faq_1' => 'Accordion', 'faq_2' => 'Dua kolom'],
        'default_variant' => 'faq_1',
        'default_limit' => 8,
        'manage_data_route' => 'cms.faq.index',
    ],
    'pengumuman' => [
        'name' => 'Pengumuman',
        'area' => 'middle',
        'component' => 'AnnouncementSection',
        'variants' => ['announcement_1' => 'News grid', 'announcement_2' => 'List kompak'],
        'default_variant' => 'announcement_1',
        'default_limit' => 6,
        'manage_data_route' => 'cms.pengumuman.index',
    ],
    'cta' => [
        'name' => 'Call To Action',
        'area' => 'bottom',
        'component' => 'CtaSection',
        'variants' => ['cta_1' => 'Simple CTA', 'cta_2' => 'CTA background image'],
        'default_variant' => 'cta_1',
        'default_limit' => 1,
        'manage_data_route' => 'cms.cta.index',
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

// Add fallback keys (plural/alias forms for backward compat)
$sections = $baseSections;

// Plural aliases (backward compat for existing DB records)
if (isset($sections['product'])) $sections['products'] = $sections['product'];
if (isset($sections['statistic'])) $sections['stats'] = $sections['statistic'];
if (isset($sections['feature'])) $sections['features'] = $sections['feature'];
if (isset($sections['testimonial'])) $sections['testimonials'] = $sections['testimonial'];
if (isset($sections['client'])) $sections['clients'] = $sections['client'];

// Pengumuman/Announcement fallback
if (isset($sections['pengumuman'])) $sections['announcement'] = $sections['pengumuman'];

return [
    'sections' => $sections,
];
