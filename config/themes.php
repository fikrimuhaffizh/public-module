<?php

/**
 * Theme registry — SATU sumber kebenaran untuk semua tema landing page.
 *
 * Menambah tema baru cukup satu entri di sini + komponen React di
 * resources/assets/js/templates/ + blok CSS `.theme-<key>` di landing.css.
 * Backend (validasi, UI CMS, props Inertia) membaca semuanya dari file ini.
 *
 * Struktur per entri:
 *   key  => [
 *     'name'        => string,  // label di CMS/picker
 *     'category'    => 'institutional' | 'umkm',   // pengelompokan UI
 *     'description' => string,  // deskripsi singkat di CMS
 *     'icon'        => string,  // ikon Tabler (tanpa prefix "ti ti-")
 *     'palette'     => ['primary' => ..., 'primary-dark' => ...],
 *
 *     // Preset desain — diterapkan OTOMATIS saat tema dipilih (lihat
 *     // ThemeCustomizerContext). Key sama dengan knob Theme Settings:
 *     //   font/radius/nav/card/button  => key di presets.js (FONT_OPTIONS dll.)
 *     //   dark                         => bool
 *     //   sectionVariants              => canonical section key => variant key
 *     //                                  (daftar variant: registry.js)
 *     //   sectionColors                => canonical section key => [bg, text, accent]
 *     'preset'     => [
 *         'font' => 'modern', 'radius' => 'default', 'nav' => 'glass',
 *         'card' => 'solid', 'button' => 'default', 'dark' => false,
 *         'sectionVariants' => ['hero' => 'hero_2', 'cta' => 'cta_1'],
 *         'sectionColors' => ['hero' => ['bg' => '#f8f4ec']],
 *     ],
 *   ]
 */
return [

    // ── Institusi / platform (default) ────────────────────────────────
    'modern' => [
        'name'        => 'Modern',
        'category'    => 'institutional',
        'description' => 'Visual progresif dengan pengalaman digital yang dinamis.',
        'icon'        => 'sparkles',
        'palette'     => ['primary' => '#1d4ed8', 'primary-dark' => '#1e40af'],
        'preset'      => [
            'font' => 'modern', 'radius' => 'default', 'nav' => 'glass',
            'card' => 'solid', 'button' => 'default', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_1',                 'navbar' => 'navbar_1', 'hero' => 'hero_2', 'product' => 'product_1',
                'statistic' => 'statistic_1', 'feature' => 'feature_1', 'testimonial' => 'testimonial_1',
                'client' => 'client_3', 'faq' => 'faq_1', 'pengumuman' => 'pengumuman_1',
                'cta' => 'cta_1', 'footer' => 'footer_2',
            ],
        ],
    ],
    'editorial' => [
        'name'        => 'Editorial',
        'category'    => 'institutional',
        'description' => 'Berorientasi konten dengan tipografi dan berita yang kuat.',
        'icon'        => 'news',
        'palette'     => ['primary' => '#b34719', 'primary-dark' => '#7a2d0e'],
        'preset'      => [
            'font' => 'serif', 'radius' => 'default', 'nav' => 'solid',
            'card' => 'outline', 'button' => 'default', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_1',                 'navbar' => 'navbar_1', 'hero' => 'hero_2', 'product' => 'product_3',
                'statistic' => 'statistic_3', 'feature' => 'feature_3', 'testimonial' => 'testimonial_3',
                'client' => 'client_2', 'faq' => 'faq_2', 'pengumuman' => 'pengumuman_1',
                'cta' => 'cta_3', 'footer' => 'footer_1',
            ],
            'sectionColors' => [
                'hero' => ['bg' => '#f8f4ec', 'text' => '#241c12'],
            ],
        ],
    ],
    'corporate' => [
        'name'        => 'Corporate',
        'category'    => 'institutional',
        'description' => 'Tampilan mewah dan elegan untuk institusi serta mitra korporat.',
        'icon'        => 'building-skyscraper',
        'palette'     => ['primary' => '#a87932', 'primary-dark' => '#7a541e'],
        'preset'      => [
            'font' => 'inter', 'radius' => 'rounded', 'nav' => 'dark',
            'card' => 'elevated', 'button' => 'default', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_1',                 'navbar' => 'navbar_1', 'hero' => 'hero_1', 'product' => 'product_1',
                'statistic' => 'statistic_2', 'feature' => 'feature_1', 'testimonial' => 'testimonial_2',
                'client' => 'client_1', 'faq' => 'faq_2', 'pengumuman' => 'pengumuman_2',
                'cta' => 'cta_2', 'footer' => 'footer_3',
            ],
        ],
    ],
    'launch' => [
        'name'        => 'Launch UI',
        'category'    => 'institutional',
        'description' => 'Desain segar dengan hero, fitur, produk, statistik, dan CTA yang dapat dikelola penuh.',
        'icon'        => 'rocket',
        'palette'     => ['primary' => '#4f46e5', 'primary-dark' => '#3730a3'],
        'preset'      => [
            'font' => 'space', 'radius' => 'rounded', 'nav' => 'primary',
            'card' => 'outline', 'button' => 'pill', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_3',                 'navbar' => 'navbar_1', 'hero' => 'hero_2', 'product' => 'product_3',
                'statistic' => 'statistic_3', 'feature' => 'feature_4', 'testimonial' => 'testimonial_1',
                'client' => 'client_3', 'faq' => 'faq_1', 'pengumuman' => 'pengumuman_3',
                'cta' => 'cta_1', 'footer' => 'footer_2',
            ],
            'sectionColors' => [
                'hero' => ['bg' => '#0f172a', 'text' => '#e2e8f0', 'accent' => '#818cf8'],
                'cta' => ['bg' => '#1e1b4b', 'text' => '#e0e7ff', 'accent' => '#a5b4fc'],
            ],
        ],
    ],
    'aurora' => [
        'name'        => 'Aurora',
        'category'    => 'institutional',
        'description' => 'Dark-mode bento grid dengan efek aurora dan glassmorphism untuk nuansa SaaS modern.',
        'icon'        => 'brand-aurora',
        'palette'     => ['primary' => '#8b5cf6', 'primary-dark' => '#6d28d9'],
        'preset'      => [
            'font' => 'sora', 'radius' => 'rounded', 'nav' => 'glass',
            'card' => 'gradient', 'button' => 'pill', 'dark' => true,
            'sectionVariants' => [
                'topbar' => 'topbar_3',                 'navbar' => 'navbar_2', 'hero' => 'hero_2', 'product' => 'product_4',
                'statistic' => 'statistic_2', 'feature' => 'feature_4', 'testimonial' => 'testimonial_4',
                'client' => 'client_4', 'faq' => 'faq_4', 'pengumuman' => 'pengumuman_4',
                'cta' => 'cta_2', 'footer' => 'footer_3',
            ],
        ],
    ],
    'enterprise' => [
        'name'        => 'Enterprise',
        'category'    => 'institutional',
        'description' => 'Tampilan profesional monokrom dengan aksen biru, fokus pada kepercayaan dan data.',
        'icon'        => 'shield-check',
        'palette'     => ['primary' => '#2563eb', 'primary-dark' => '#1e40af'],
        'preset'      => [
            'font' => 'inter', 'radius' => 'square', 'nav' => 'solid',
            'card' => 'elevated', 'button' => 'sharp', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_1',                 'navbar' => 'navbar_1', 'hero' => 'hero_2', 'product' => 'product_1',
                'statistic' => 'statistic_1', 'feature' => 'feature_1', 'testimonial' => 'testimonial_1',
                'client' => 'client_1', 'faq' => 'faq_1', 'pengumuman' => 'pengumuman_1',
                'cta' => 'cta_1', 'footer' => 'footer_1',
            ],
        ],
    ],
    'registration' => [
        'name'        => 'Registration',
        'category'    => 'institutional',
        'description' => 'Berorientasi pendaftaran dengan form ringkasan, langkah-langkah, dan testimoni.',
        'icon'        => 'clipboard-check',
        'palette'     => ['primary' => '#0f766e', 'primary-dark' => '#115e59'],
        'preset'      => [
            'font' => 'jakarta', 'radius' => 'rounded', 'nav' => 'solid',
            'card' => 'outline', 'button' => 'default', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_3',                 'navbar' => 'navbar_1', 'hero' => 'hero_1', 'product' => 'product_2',
                'statistic' => 'statistic_2', 'feature' => 'feature_3', 'testimonial' => 'testimonial_1',
                'client' => 'client_2', 'faq' => 'faq_2', 'pengumuman' => 'pengumuman_2',
                'cta' => 'cta_3', 'footer' => 'footer_1',
            ],
        ],
    ],
    'profile' => [
        'name'        => 'Profile',
        'category'    => 'institutional',
        'description' => 'Elegant company profile dengan tipografi serif, kutipan, dan visual bersih.',
        'icon'        => 'building-arch',
        'palette'     => ['primary' => '#78350f', 'primary-dark' => '#451a03'],
        'preset'      => [
            'font' => 'serif', 'radius' => 'default', 'nav' => 'dark',
            'card' => 'solid', 'button' => 'default', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_1',                 'navbar' => 'navbar_1', 'hero' => 'hero_1', 'product' => 'product_3',
                'statistic' => 'statistic_3', 'feature' => 'feature_2', 'testimonial' => 'testimonial_3',
                'client' => 'client_2', 'faq' => 'faq_1', 'pengumuman' => 'pengumuman_2',
                'cta' => 'cta_1', 'footer' => 'footer_2',
            ],
        ],
    ],
    'campus' => [
        'name'        => 'Campus',
        'category'    => 'institutional',
        'description' => 'Beranda kampus akademik dengan foto, highlight bericon, program, dan statistik.',
        'icon'        => 'school',
        'palette'     => ['primary' => '#0f766e', 'primary-dark' => '#134e4a'],
        'preset'      => [
            'font' => 'modern', 'radius' => 'rounded', 'nav' => 'solid',
            'card' => 'elevated', 'button' => 'default', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_1',                 'navbar' => 'navbar_1', 'hero' => 'hero_2', 'product' => 'product_1',
                'statistic' => 'statistic_1', 'feature' => 'feature_1', 'testimonial' => 'testimonial_1',
                'client' => 'client_3', 'faq' => 'faq_1', 'pengumuman' => 'pengumuman_1',
                'cta' => 'cta_2', 'footer' => 'footer_3',
            ],
        ],
    ],
    'admissions' => [
        'name'        => 'Admissions',
        'category'    => 'institutional',
        'description' => 'Halaman pendaftaran dengan alur langkah, jalur masuk, dan informasi biaya.',
        'icon'        => 'clipboard-list',
        'palette'     => ['primary' => '#7c3aed', 'primary-dark' => '#5b21b6'],
        'preset'      => [
            'font' => 'poppins', 'radius' => 'rounded', 'nav' => 'primary',
            'card' => 'outline', 'button' => 'pill', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_1',                 'navbar' => 'navbar_1', 'hero' => 'hero_1', 'product' => 'product_2',
                'statistic' => 'statistic_2', 'feature' => 'feature_3', 'testimonial' => 'testimonial_2',
                'client' => 'client_1', 'faq' => 'faq_2', 'pengumuman' => 'pengumuman_1',
                'cta' => 'cta_3', 'footer' => 'footer_1',
            ],
        ],
    ],
    'tracer' => [
        'name'        => 'Tracer Study',
        'category'    => 'institutional',
        'description' => 'Jejak alumni dengan dashboard data, metrik lulusan, dan kuesioner tracer.',
        'icon'        => 'chart-bar',
        'palette'     => ['primary' => '#0ea5e9', 'primary-dark' => '#0369a1'],
        'preset'      => [
            'font' => 'figtree', 'radius' => 'default', 'nav' => 'glass',
            'card' => 'elevated', 'button' => 'default', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_3',                 'navbar' => 'navbar_1', 'hero' => 'hero_2', 'product' => 'product_1',
                'statistic' => 'statistic_2', 'feature' => 'feature_3', 'testimonial' => 'testimonial_1',
                'client' => 'client_1', 'faq' => 'faq_1', 'pengumuman' => 'pengumuman_1',
                'cta' => 'cta_2', 'footer' => 'footer_1',
            ],
        ],
    ],

    // ── UMKM (prototipe pipeline 100 tema) ────────────────────────────
    // Pola: satu tema = satu entri config + satu komponen React + satu blok CSS.
    'umkm_warung' => [
        'name'        => 'Warung & Kuliner',
        'category'    => 'umkm',
        'description' => 'Hangat dan menggugah selera — cocok untuk rumah makan, kafe, dan produk kuliner.',
        'icon'        => 'coffee',
        'palette'     => ['primary' => '#c2410c', 'primary-dark' => '#9a3412'],
        'preset'      => [
            'font' => 'rounded', 'radius' => 'rounded', 'nav' => 'solid',
            'card' => 'elevated', 'button' => 'pill', 'dark' => false,
            'sectionVariants' => [
                'topbar' => 'topbar_2',                 'navbar' => 'navbar_1', 'hero' => 'hero_3', 'product' => 'product_4',
                'statistic' => 'statistic_2', 'feature' => 'feature_3', 'testimonial' => 'testimonial_4',
                'client' => 'client_3', 'faq' => 'faq_1', 'pengumuman' => 'pengumuman_4',
                'cta' => 'cta_2', 'price' => 'price_1', 'footer' => 'footer_3',
            ],
            'sectionColors' => [
                'hero' => ['bg' => '#fff7ed', 'text' => '#431407', 'accent' => '#ea580c'],
            ],
        ],
    ],
];
