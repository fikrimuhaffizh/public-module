<?php

/*
|--------------------------------------------------------------------------
| Website Builder — Page Templates
|--------------------------------------------------------------------------
|
| Template hanya merupakan "initial configuration". Saat admin membuat page
| dari template, daftar section di bawah disalin ke page tersebut sebagai
| section-section awal. Setelah itu page bisa diedit bebas.
|
| Setiap section mengacu pada `type` yang terdaftar di builder_sections.
| Format konten mengikuti `defaults.content` dari registry section.
|
*/

$templates = [

    'landing' => [
        'name' => 'Landing SaaS',
        'description' => 'Halaman landing untuk promosi produk atau layanan digital.',
        'sections' => [
            ['type' => 'hero'],
            ['type' => 'logo'],
            ['type' => 'features'],
            ['type' => 'statistics'],
            ['type' => 'testimonials'],
            ['type' => 'pricing'],
            ['type' => 'faq'],
            ['type' => 'cta'],
        ],
    ],

    'company' => [
        'name' => 'Company Profile',
        'description' => 'Profil perusahaan: cerita, tim, dan pencapaian.',
        'sections' => [
            ['type' => 'hero'],
            ['type' => 'image_text'],
            ['type' => 'statistics'],
            ['type' => 'features'],
            ['type' => 'testimonials'],
            ['type' => 'contact'],
            ['type' => 'cta'],
        ],
    ],

    'product' => [
        'name' => 'Product',
        'description' => 'Halaman promosi produk dengan rincian fitur dan harga.',
        'sections' => [
            ['type' => 'hero'],
            ['type' => 'features'],
            ['type' => 'image_text'],
            ['type' => 'gallery'],
            ['type' => 'pricing'],
            ['type' => 'faq'],
            ['type' => 'cta'],
        ],
    ],

    'event' => [
        'name' => 'Event',
        'description' => 'Landing acara, seminar, atau konferensi.',
        'sections' => [
            ['type' => 'hero'],
            ['type' => 'statistics'],
            ['type' => 'gallery'],
            ['type' => 'testimonials'],
            ['type' => 'faq'],
            ['type' => 'cta'],
        ],
    ],

    'documentation' => [
        'name' => 'Documentation',
        'description' => 'Beranda dokumentasi produk dengan daftar fitur dan pertanyaan.',
        'sections' => [
            ['type' => 'hero'],
            ['type' => 'features'],
            ['type' => 'faq'],
            ['type' => 'contact'],
        ],
    ],

    'blank' => [
        'name' => 'Blank',
        'description' => 'Halaman kosong — mulai dari nol.',
        'sections' => [],
    ],
];

return [
    'templates' => $templates,
];