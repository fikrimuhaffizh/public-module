<?php

/*
|--------------------------------------------------------------------------
| Website Builder — Section Registry
|--------------------------------------------------------------------------
|
| Daftar section yang tersedia untuk Website Builder. Admin memilih section
| dari library ini, content disimpan sebagai JSON, dan renderer memetakan
| `type` -> Blade component (resources/views/components/builder/{view}.blade.php).
|
| Setiap section punya:
|   - name         : label tampilan di panel admin
|   - category     : basic | marketing | content | layout
|   - icon         : nama ikon Tabler (tanpa prefix "ti-")
|   - component    : path view Blade di bawah components/builder/
|   - defaults     : content & settings default (muncul saat section ditambahkan)
|   - content_fields / settings_fields : skema properti untuk panel edit (Phase 2)
|
*/

$alignOptions = [
    'left' => 'Kiri',
    'center' => 'Tengah',
    'right' => 'Kanan',
];

$backgroundOptions = [
    'white' => 'Putih',
    'gray' => 'Abu-abu',
    'dark' => 'Gelap',
    'brand' => 'Warna Brand',
    'image' => 'Gambar',
];

$paddingOptions = [
    'none' => 'Tanpa Spasi',
    'sm' => 'Kecil',
    'md' => 'Sedang',
    'lg' => 'Besar',
    'xl' => 'Sangat Besar',
];

$sections = [

    // ─── BASIC ────────────────────────────────────────────────

    'heading' => [
        'name' => 'Heading',
        'category' => 'basic',
        'icon' => 'text-caption',
        'component' => 'heading',
        'defaults' => [
            'content' => [
                'title' => 'Judul Heading',
            ],
            'settings' => [
                'align' => 'left',
                'level' => '2',
                'color' => 'default',
                'size' => 'lg',
                'padding_y' => 'md',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Teks', 'type' => 'text'],
        ],
        'settings_fields' => [
            ['name' => 'align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'level', 'label' => 'Tingkat', 'type' => 'select', 'options' => ['1' => 'H1', '2' => 'H2', '3' => 'H3', '4' => 'H4']],
            ['name' => 'color', 'label' => 'Warna', 'type' => 'select', 'options' => ['default' => 'Default', 'brand' => 'Brand', 'muted' => 'Abu-abu']],
            ['name' => 'size', 'label' => 'Ukuran', 'type' => 'select', 'options' => ['sm' => 'Kecil', 'base' => 'Sedang', 'lg' => 'Besar', 'xl' => 'Sangat Besar']],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'text' => [
        'name' => 'Text',
        'category' => 'basic',
        'icon' => 'typography',
        'component' => 'text',
        'defaults' => [
            'content' => [
                'text' => 'Tulis paragraf di sini. Teks dirender sebagai paragraf HTML yang aman.',
            ],
            'settings' => [
                'align' => 'left',
                'padding_y' => 'md',
            ],
        ],
        'content_fields' => [
            ['name' => 'text', 'label' => 'Teks', 'type' => 'textarea'],
        ],
        'settings_fields' => [
            ['name' => 'align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'image' => [
        'name' => 'Image',
        'category' => 'basic',
        'icon' => 'photo',
        'component' => 'image',
        'defaults' => [
            'content' => [
                'image' => null,
                'alt' => '',
                'caption' => '',
            ],
            'settings' => [
                'align' => 'center',
                'rounded' => true,
                'max_width' => 'md',
                'padding_y' => 'md',
            ],
        ],
        'content_fields' => [
            ['name' => 'image', 'label' => 'Gambar', 'type' => 'image'],
            ['name' => 'alt', 'label' => 'Teks Alternatif (Alt)', 'type' => 'text'],
            ['name' => 'caption', 'label' => 'Caption', 'type' => 'text'],
        ],
        'settings_fields' => [
            ['name' => 'align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'rounded', 'label' => 'Sudut Membulat', 'type' => 'toggle'],
            ['name' => 'max_width', 'label' => 'Lebar Maksimal', 'type' => 'select', 'options' => ['sm' => 'Sempit', 'md' => 'Sedang', 'lg' => 'Lebar']],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'button' => [
        'name' => 'Button',
        'category' => 'basic',
        'icon' => 'button',
        'component' => 'button',
        'defaults' => [
            'content' => [
                'text' => 'Klik di sini',
                'url' => '#',
                'variant' => 'primary',
                'icon' => '',
            ],
            'settings' => [
                'align' => 'left',
                'full_width' => false,
                'padding_y' => 'md',
            ],
        ],
        'content_fields' => [
            ['name' => 'text', 'label' => 'Teks', 'type' => 'text'],
            ['name' => 'url', 'label' => 'URL / Link', 'type' => 'text'],
            ['name' => 'variant', 'label' => 'Varian', 'type' => 'select', 'options' => ['primary' => 'Primary', 'secondary' => 'Secondary', 'outline' => 'Outline', 'ghost' => 'Ghost', 'white' => 'Putih']],
            ['name' => 'icon', 'label' => 'Ikon (Tabler)', 'type' => 'text'],
        ],
        'settings_fields' => [
            ['name' => 'align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'full_width', 'label' => 'Lebar Penuh', 'type' => 'toggle'],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'divider' => [
        'name' => 'Divider',
        'category' => 'basic',
        'icon' => 'separator-horizontal',
        'component' => 'divider',
        'defaults' => [
            'content' => [],
            'settings' => [
                'style' => 'solid',
                'width' => 'md',
                'padding_y' => 'md',
            ],
        ],
        'content_fields' => [],
        'settings_fields' => [
            ['name' => 'style', 'label' => 'Gaya', 'type' => 'select', 'options' => ['solid' => 'Solid', 'dashed' => 'Putus-putus', 'dotted' => 'Titik-titik']],
            ['name' => 'width', 'label' => 'Lebar', 'type' => 'select', 'options' => ['sm' => '30%', 'md' => '50%', 'lg' => '100%']],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'container' => [
        'name' => 'Container (Spacer)',
        'category' => 'basic',
        'icon' => 'box',
        'component' => 'container',
        'defaults' => [
            'content' => [],
            'settings' => [
                'background' => 'white',
                'min_height' => 'sm',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'min_height', 'label' => 'Tinggi', 'type' => 'select', 'options' => ['sm' => 'Kecil', 'md' => 'Sedang', 'lg' => 'Besar']],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    // ─── MARKETING ────────────────────────────────────────────

    'hero' => [
        'name' => 'Hero',
        'category' => 'marketing',
        'icon' => 'rocket',
        'component' => 'hero',
        'defaults' => [
            'content' => [
                'eyebrow' => '',
                'title' => 'Selamat datang di platform kami',
                'description' => 'Deskripsi singkat yang menjelaskan nilai utama layanan atau produk Anda.',
                'image' => null,
                'button_text' => 'Mulai Sekarang',
                'button_url' => '#',
                'button_text_2' => '',
                'button_url_2' => '',
            ],
            'settings' => [
                'background' => 'brand',
                'text_align' => 'center',
                'padding_y' => 'xl',
                'title_size' => 'xl',
            ],
        ],
        'content_fields' => [
            ['name' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text'],
            ['name' => 'title', 'label' => 'Judul', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'],
            ['name' => 'image', 'label' => 'Gambar', 'type' => 'image'],
            ['name' => 'button_text', 'label' => 'Teks Tombol', 'type' => 'text'],
            ['name' => 'button_url', 'label' => 'URL Tombol', 'type' => 'text'],
            ['name' => 'button_text_2', 'label' => 'Teks Tombol Kedua', 'type' => 'text'],
            ['name' => 'button_url_2', 'label' => 'URL Tombol Kedua', 'type' => 'text'],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'text_align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
            ['name' => 'title_size', 'label' => 'Ukuran Judul', 'type' => 'select', 'options' => ['lg' => 'Besar', 'xl' => 'Sangat Besar', '2xl' => 'Raksasa']],
        ],
    ],

    'features' => [
        'name' => 'Features',
        'category' => 'marketing',
        'icon' => 'layout-grid',
        'component' => 'features',
        'defaults' => [
            'content' => [
                'title' => 'Fitur Unggulan',
                'subtitle' => 'Kenapa memilih kami?',
                'items' => [
                    ['icon' => 'bolt', 'title' => 'Cepat', 'description' => 'Performa tinggi sejak awal.'],
                    ['icon' => 'shield', 'title' => 'Aman', 'description' => 'Data terlindungi dengan baik.'],
                    ['icon' => 'adjustments', 'title' => 'Fleksibel', 'description' => 'Mudah disesuaikan kebutuhan.'],
                ],
            ],
            'settings' => [
                'background' => 'white',
                'columns' => '3',
                'padding_y' => 'lg',
                'text_align' => 'center',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul Section', 'type' => 'text'],
            ['name' => 'subtitle', 'label' => 'Sub-judul', 'type' => 'text'],
            ['name' => 'items', 'label' => 'Fitur', 'type' => 'repeater', 'item_fields' => [
                ['name' => 'icon', 'label' => 'Ikon (Tabler)', 'type' => 'text'],
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text'],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'columns', 'label' => 'Kolom', 'type' => 'select', 'options' => ['2' => '2', '3' => '3', '4' => '4']],
            ['name' => 'text_align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'statistics' => [
        'name' => 'Statistics',
        'category' => 'marketing',
        'icon' => 'chart-bar',
        'component' => 'statistics',
        'defaults' => [
            'content' => [
                'title' => 'Angka yang Berbicara',
                'subtitle' => '',
                'items' => [
                    ['value' => '99%', 'label' => 'Kepuasan'],
                    ['value' => '120', 'label' => 'Klien Aktif'],
                    ['value' => '10', 'label' => 'Tahun Pengalaman'],
                ],
            ],
            'settings' => [
                'background' => 'gray',
                'columns' => '3',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul Section', 'type' => 'text'],
            ['name' => 'subtitle', 'label' => 'Sub-judul', 'type' => 'text'],
            ['name' => 'items', 'label' => 'Statistik', 'type' => 'repeater', 'item_fields' => [
                ['name' => 'value', 'label' => 'Nilai', 'type' => 'text'],
                ['name' => 'label', 'label' => 'Label', 'type' => 'text'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'columns', 'label' => 'Kolom', 'type' => 'select', 'options' => ['2' => '2', '3' => '3', '4' => '4']],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'testimonials' => [
        'name' => 'Testimonials',
        'category' => 'marketing',
        'icon' => 'messages',
        'component' => 'testimonials',
        'defaults' => [
            'content' => [
                'title' => 'Kata Mereka',
                'subtitle' => '',
                'items' => [
                    ['quote' => 'Layanan yang luar biasa dan tepat waktu.', 'name' => 'Andi Wijaya', 'position' => 'Direktur', 'photo' => null],
                    ['quote' => 'Sangat membantu pertumbuhan bisnis kami.', 'name' => 'Siti Rahma', 'position' => 'CEO', 'photo' => null],
                ],
            ],
            'settings' => [
                'background' => 'white',
                'columns' => '2',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul Section', 'type' => 'text'],
            ['name' => 'subtitle', 'label' => 'Sub-judul', 'type' => 'text'],
            ['name' => 'items', 'label' => 'Testimoni', 'type' => 'repeater', 'item_fields' => [
                ['name' => 'quote', 'label' => 'Kutipan', 'type' => 'textarea'],
                ['name' => 'name', 'label' => 'Nama', 'type' => 'text'],
                ['name' => 'position', 'label' => 'Jabatan', 'type' => 'text'],
                ['name' => 'photo', 'label' => 'Foto', 'type' => 'image'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'columns', 'label' => 'Kolom', 'type' => 'select', 'options' => ['1' => '1', '2' => '2', '3' => '3']],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'pricing' => [
        'name' => 'Pricing',
        'category' => 'marketing',
        'icon' => 'currency-dollar',
        'component' => 'pricing',
        'defaults' => [
            'content' => [
                'title' => 'Harga & Paket',
                'subtitle' => 'Pilih paket yang paling sesuai',
                'items' => [
                    ['name' => 'Starter', 'price' => '0', 'period' => '/bulan', 'description' => 'Untuk mencoba', 'features' => ['1 pengguna', '1 proyek', 'Support email'], 'button_text' => 'Pilih', 'button_url' => '#', 'featured' => false],
                    ['name' => 'Pro', 'price' => '99', 'period' => '/bulan', 'description' => 'Untuk tim yang sedang berkembang', 'features' => ['10 pengguna', 'Proyek tanpa batas', 'Prioritas support'], 'button_text' => 'Pilih', 'button_url' => '#', 'featured' => true],
                ],
            ],
            'settings' => [
                'background' => 'gray',
                'columns' => '3',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul Section', 'type' => 'text'],
            ['name' => 'subtitle', 'label' => 'Sub-judul', 'type' => 'text'],
            ['name' => 'items', 'label' => 'Paket', 'type' => 'repeater', 'item_fields' => [
                ['name' => 'name', 'label' => 'Nama Paket', 'type' => 'text'],
                ['name' => 'price', 'label' => 'Harga', 'type' => 'text'],
                ['name' => 'period', 'label' => 'Periode', 'type' => 'text'],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'],
                ['name' => 'features', 'label' => 'Fitur', 'type' => 'list'],
                ['name' => 'button_text', 'label' => 'Teks Tombol', 'type' => 'text'],
                ['name' => 'button_url', 'label' => 'URL Tombol', 'type' => 'text'],
                ['name' => 'featured', 'label' => 'Unggulan', 'type' => 'toggle'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'columns', 'label' => 'Kolom', 'type' => 'select', 'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4']],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'faq' => [
        'name' => 'FAQ',
        'category' => 'marketing',
        'icon' => 'help',
        'component' => 'faq',
        'defaults' => [
            'content' => [
                'title' => 'Pertanyaan yang Sering Diajukan',
                'subtitle' => '',
                'items' => [
                    ['question' => 'Bagaimana cara memulai?', 'answer' => 'Daftar akun dan ikuti langkah panduan yang tersedia.'],
                    ['question' => 'Apakah ada biaya tersembunyi?', 'answer' => 'Tidak ada. Harga sesuai paket yang Anda pilih.'],
                ],
            ],
            'settings' => [
                'background' => 'white',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul Section', 'type' => 'text'],
            ['name' => 'subtitle', 'label' => 'Sub-judul', 'type' => 'text'],
            ['name' => 'items', 'label' => 'Pertanyaan', 'type' => 'repeater', 'item_fields' => [
                ['name' => 'question', 'label' => 'Pertanyaan', 'type' => 'text'],
                ['name' => 'answer', 'label' => 'Jawaban', 'type' => 'textarea'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'cta' => [
        'name' => 'CTA',
        'category' => 'marketing',
        'icon' => 'alert-octagon',
        'component' => 'cta',
        'defaults' => [
            'content' => [
                'title' => 'Siap untuk memulai?',
                'description' => 'Bergabunglah bersama ribuan pengguna lain yang sudah terbantu.',
                'button_text' => 'Hubungi Kami',
                'button_url' => '#',
            ],
            'settings' => [
                'background' => 'brand',
                'text_align' => 'center',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'],
            ['name' => 'button_text', 'label' => 'Teks Tombol', 'type' => 'text'],
            ['name' => 'button_url', 'label' => 'URL Tombol', 'type' => 'text'],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'text_align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    // ─── CONTENT ───────────────────────────────────────────────

    'image_text' => [
        'name' => 'Image + Text',
        'category' => 'content',
        'icon' => 'photo-text',
        'component' => 'image-text',
        'defaults' => [
            'content' => [
                'image' => null,
                'image_side' => 'left',
                'heading' => 'Tentang Kami',
                'text' => 'Tuliskan deskripsi singkat yang menjelaskan cerita atau keunggulan Anda.',
                'button_text' => 'Selengkapnya',
                'button_url' => '#',
            ],
            'settings' => [
                'background' => 'white',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'image', 'label' => 'Gambar', 'type' => 'image'],
            ['name' => 'image_side', 'label' => 'Posisi Gambar', 'type' => 'select', 'options' => ['left' => 'Kiri', 'right' => 'Kanan']],
            ['name' => 'heading', 'label' => 'Judul', 'type' => 'text'],
            ['name' => 'text', 'label' => 'Teks', 'type' => 'textarea'],
            ['name' => 'button_text', 'label' => 'Teks Tombol', 'type' => 'text'],
            ['name' => 'button_url', 'label' => 'URL Tombol', 'type' => 'text'],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'gallery' => [
        'name' => 'Gallery',
        'category' => 'content',
        'icon' => 'gallery',
        'component' => 'gallery',
        'defaults' => [
            'content' => [
                'title' => 'Galeri Kegiatan',
                'subtitle' => '',
                'items' => [
                    ['image' => null, 'caption' => ''],
                    ['image' => null, 'caption' => ''],
                    ['image' => null, 'caption' => ''],
                ],
            ],
            'settings' => [
                'background' => 'white',
                'columns' => '3',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul Section', 'type' => 'text'],
            ['name' => 'subtitle', 'label' => 'Sub-judul', 'type' => 'text'],
            ['name' => 'items', 'label' => 'Gambar', 'type' => 'repeater', 'item_fields' => [
                ['name' => 'image', 'label' => 'Gambar', 'type' => 'image'],
                ['name' => 'caption', 'label' => 'Caption', 'type' => 'text'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'columns', 'label' => 'Kolom', 'type' => 'select', 'options' => ['2' => '2', '3' => '3', '4' => '4']],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'logo' => [
        'name' => 'Logo / Partner',
        'category' => 'content',
        'icon' => 'building',
        'component' => 'logo',
        'defaults' => [
            'content' => [
                'title' => 'Dipercaya Oleh',
                'items' => [
                    ['image' => null, 'name' => 'Partner 1', 'url' => '#'],
                    ['image' => null, 'name' => 'Partner 2', 'url' => '#'],
                    ['image' => null, 'name' => 'Partner 3', 'url' => '#'],
                ],
            ],
            'settings' => [
                'background' => 'gray',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul Section', 'type' => 'text'],
            ['name' => 'items', 'label' => 'Partner', 'type' => 'repeater', 'item_fields' => [
                ['name' => 'image', 'label' => 'Logo', 'type' => 'image'],
                ['name' => 'name', 'label' => 'Nama', 'type' => 'text'],
                ['name' => 'url', 'label' => 'URL', 'type' => 'text'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'contact' => [
        'name' => 'Contact Info',
        'category' => 'content',
        'icon' => 'phone',
        'component' => 'contact',
        'defaults' => [
            'content' => [
                'title' => 'Hubungi Kami',
                'description' => 'Kami siap membantu pertanyaan Anda.',
                'email' => 'halo@example.com',
                'phone' => '+62 812 3456 7890',
                'address' => 'Jl. Contoh No. 1, Kota Anda',
                'hours' => 'Senin - Jumat, 08.00 - 17.00',
            ],
            'settings' => [
                'background' => 'white',
                'padding_y' => 'lg',
            ],
        ],
        'content_fields' => [
            ['name' => 'title', 'label' => 'Judul', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Telepon', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Alamat', 'type' => 'textarea'],
            ['name' => 'hours', 'label' => 'Jam Operasional', 'type' => 'text'],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    // ─── LAYOUT ────────────────────────────────────────────────

    'columns-2' => [
        'name' => '2 Columns',
        'category' => 'layout',
        'icon' => 'columns',
        'component' => 'columns',
        'defaults' => [
            'content' => [
                'columns' => [
                    ['heading' => 'Kolom 1', 'text' => 'Isi teks kolom pertama.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                    ['heading' => 'Kolom 2', 'text' => 'Isi teks kolom kedua.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                ],
            ],
            'settings' => [
                'background' => 'white',
                'count' => '2',
                'padding_y' => 'lg',
                'text_align' => 'left',
            ],
        ],
        'content_fields' => [
            ['name' => 'columns', 'label' => 'Kolom', 'type' => 'columns_repeater', 'item_fields' => [
                ['name' => 'heading', 'label' => 'Judul', 'type' => 'text'],
                ['name' => 'text', 'label' => 'Teks', 'type' => 'textarea'],
                ['name' => 'image', 'label' => 'Gambar', 'type' => 'image'],
                ['name' => 'button_text', 'label' => 'Teks Tombol', 'type' => 'text'],
                ['name' => 'button_url', 'label' => 'URL Tombol', 'type' => 'text'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'text_align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'columns-3' => [
        'name' => '3 Columns',
        'category' => 'layout',
        'icon' => 'layout-columns',
        'component' => 'columns',
        'defaults' => [
            'content' => [
                'columns' => [
                    ['heading' => 'Kolom 1', 'text' => 'Isi teks kolom pertama.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                    ['heading' => 'Kolom 2', 'text' => 'Isi teks kolom kedua.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                    ['heading' => 'Kolom 3', 'text' => 'Isi teks kolom ketiga.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                ],
            ],
            'settings' => [
                'background' => 'white',
                'count' => '3',
                'padding_y' => 'lg',
                'text_align' => 'left',
            ],
        ],
        'content_fields' => [
            ['name' => 'columns', 'label' => 'Kolom', 'type' => 'columns_repeater', 'item_fields' => [
                ['name' => 'heading', 'label' => 'Judul', 'type' => 'text'],
                ['name' => 'text', 'label' => 'Teks', 'type' => 'textarea'],
                ['name' => 'image', 'label' => 'Gambar', 'type' => 'image'],
                ['name' => 'button_text', 'label' => 'Teks Tombol', 'type' => 'text'],
                ['name' => 'button_url', 'label' => 'URL Tombol', 'type' => 'text'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'text_align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],

    'columns-4' => [
        'name' => '4 Columns',
        'category' => 'layout',
        'icon' => 'layout-grid',
        'component' => 'columns',
        'defaults' => [
            'content' => [
                'columns' => [
                    ['heading' => 'Kolom 1', 'text' => 'Isi teks kolom pertama.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                    ['heading' => 'Kolom 2', 'text' => 'Isi teks kolom kedua.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                    ['heading' => 'Kolom 3', 'text' => 'Isi teks kolom ketiga.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                    ['heading' => 'Kolom 4', 'text' => 'Isi teks kolom keempat.', 'image' => null, 'button_text' => '', 'button_url' => '#'],
                ],
            ],
            'settings' => [
                'background' => 'white',
                'count' => '4',
                'padding_y' => 'lg',
                'text_align' => 'left',
            ],
        ],
        'content_fields' => [
            ['name' => 'columns', 'label' => 'Kolom', 'type' => 'columns_repeater', 'item_fields' => [
                ['name' => 'heading', 'label' => 'Judul', 'type' => 'text'],
                ['name' => 'text', 'label' => 'Teks', 'type' => 'textarea'],
                ['name' => 'image', 'label' => 'Gambar', 'type' => 'image'],
                ['name' => 'button_text', 'label' => 'Teks Tombol', 'type' => 'text'],
                ['name' => 'button_url', 'label' => 'URL Tombol', 'type' => 'text'],
            ]],
        ],
        'settings_fields' => [
            ['name' => 'background', 'label' => 'Latar Belakang', 'type' => 'select', 'options' => $backgroundOptions],
            ['name' => 'text_align', 'label' => 'Perataan', 'type' => 'select', 'options' => $alignOptions],
            ['name' => 'padding_y', 'label' => 'Spasi', 'type' => 'select', 'options' => $paddingOptions],
        ],
    ],
];

return [
    'sections' => $sections,
];