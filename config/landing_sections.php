<?php

if (!function_exists('autoVariants')) {
// Auto-discovery: daftar mode dibaca dari file komponen section
// (resources/assets/js/components/sections/<dir>/Mode{n}.jsx) - sama seperti
// registry.js frontend. Developer cukup menambah file Mode{n}.jsx, mode
// otomatis terdaftar dengan nama "Mode {n}".
function autoVariants(string $dir, string $prefix): array
{
    $files = glob(__DIR__ . "/../resources/assets/js/components/sections/{$dir}/Mode[0-9]*.jsx") ?: [];
    $variants = [];
    foreach ($files as $file) {
        if (preg_match('/Mode(\d+)\.jsx$/', $file, $m)) {
            $n = (int) $m[1];
            $variants[$prefix . '_' . $n] = "Mode {$n}";
        }
    }
    ksort($variants, SORT_NATURAL);
    return $variants;
}

}

// Base sections configuration
$baseSections = [
    'topbar' => [
        'name' => 'Top Bar',
        'area' => 'top',
        'component' => 'TopBarSection',
        'variants' => autoVariants('topbar', 'topbar'),
        'default_variant' => 'topbar_1',
        'default_limit' => null,
        'manage_data_route' => null,
    ],
        'pageheader' => [
        'name' => 'Page Header',
        'area' => 'top',
        'component' => 'PageHeaderSection',
        'variants' => autoVariants('pageheader', 'pageheader'),
        'default_variant' => 'pageheader_1',
        'default_limit' => 6,
    ],
    'navbar' => [
        'name' => 'Navbar',
        'area' => 'top',
        'component' => 'NavbarSection',
        'variants' => autoVariants('navbar', 'navbar'),
        'default_variant' => 'navbar_1',
        'default_limit' => null,
        'manage_data_route' => null,
    ],
    'hero' => [
        'name' => 'Hero',
        'area' => 'top',
        'component' => 'HeroSection',
        'variants' => autoVariants('hero', 'hero'),
        'default_variant' => 'hero_1',
        'default_limit' => 1,
        'manage_data_route' => null,
    ],
    'product' => [
        'name' => 'Produk / Modul',
        'area' => 'middle',
        'component' => 'ProductSection',
        'variants' => autoVariants('product', 'product'),
        'default_variant' => 'product_1',
        'default_limit' => 6,
        'manage_data_route' => 'cms.product.index',
    ],
    'statistic' => [
        'name' => 'Statistik',
        'area' => 'middle',
        'component' => 'StatsSection',
        'variants' => autoVariants('statistic', 'statistic'),
        'default_variant' => 'statistic_1',
        'default_limit' => 4,
        'manage_data_route' => 'cms.statistic.index',
    ],
    'feature' => [
        'name' => 'Fitur',
        'area' => 'middle',
        'component' => 'FeatureSection',
        'variants' => autoVariants('feature', 'feature'),
        'default_variant' => 'feature_1',
        'default_limit' => 6,
        'manage_data_route' => 'cms.feature.index',
    ],
    'testimonial' => [
        'name' => 'Testimoni',
        'area' => 'middle',
        'component' => 'TestimonialSection',
        'variants' => autoVariants('testimonial', 'testimonial'),
        'default_variant' => 'testimonial_1',
        'default_limit' => 3,
        'manage_data_route' => 'cms.testimonial.index',
    ],
    'client' => [
        'name' => 'Client',
        'area' => 'middle',
        'component' => 'ClientSection',
        'variants' => autoVariants('client', 'client'),
        'default_variant' => 'client_1',
        'default_limit' => 8,
        'manage_data_route' => 'cms.client.index',
    ],
    'faq' => [
        'name' => 'FAQ',
        'area' => 'middle',
        'component' => 'FaqSection',
        'variants' => autoVariants('faq', 'faq'),
        'default_variant' => 'faq_1',
        'default_limit' => 8,
        'manage_data_route' => 'cms.faq.index',
    ],
    'pengumuman' => [
        'name' => 'Pengumuman',
        'area' => 'middle',
        'component' => 'AnnouncementSection',
        'variants' => autoVariants('announcement', 'pengumuman'),
        'default_variant' => 'pengumuman_1',
        'default_limit' => 6,
        'manage_data_route' => 'cms.pengumuman.index',
    ],
    'cta' => [
        'name' => 'Call To Action',
        'area' => 'bottom',
        'component' => 'CtaSection',
        'variants' => autoVariants('cta', 'cta'),
        'default_variant' => 'cta_1',
        'default_limit' => 1,
        'manage_data_route' => 'cms.cta.index',
    ],
    'price' => [
        'name' => 'Harga / Paket',
        'area' => 'middle',
        'component' => 'PriceSection',
        'variants' => autoVariants('price', 'price'),
        'default_variant' => 'price_1',
        'default_limit' => 3,
        'manage_data_route' => null,
    ],
    'footer' => [
        'name' => 'Footer',
        'area' => 'bottom',
        'component' => 'FooterSection',
        'variants' => autoVariants('footer', 'footer'),
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
