<?php

namespace Modules\Public\Database\Seeders;

use Modules\Account\Models\User;
use Illuminate\Database\Seeder;
use Modules\Public\Models\FAQ;
use Modules\Public\Models\Menu;
use Modules\Public\Models\Page;
use Modules\Public\Models\Partner;
use Modules\Public\Models\Pengumuman;
use Modules\Public\Models\Slideshow;
use Modules\Public\Models\Testimonial;

class DemoPublicSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;
        $author = User::withoutGlobalScopes()->where('tenant_id', $tenantId)->firstOrFail();

        Menu::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Pengumuman::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Page::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        FAQ::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Slideshow::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Testimonial::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();
        Partner::withoutGlobalScopes()->where('tenant_id', $tenantId)->forceDelete();

        $pages = collect([
            ['Tentang Kampus', 'tentang-kampus'],
            ['Visi dan Misi', 'visi-dan-misi'],
            ['Program Studi', 'program-studi'],
            ['Fasilitas Kampus', 'fasilitas-kampus'],
            ['Layanan Mahasiswa', 'layanan-mahasiswa'],
            ['Kerja Sama', 'kerja-sama'],
            ['Beasiswa', 'beasiswa'],
            ['Kontak', 'kontak'],
        ])->map(fn (array $page) => Page::create([
            'tenant_id' => $tenantId,
            'title' => $page[0],
            'slug' => $page[1],
            'content' => '<h2>'.$page[0].'</h2><p>Informasi demo '.$page[0].' yang dapat dikelola melalui CMS.</p>',
            'meta_desc' => 'Informasi '.$page[0],
            'meta_keywords' => str_replace('-', ', ', $page[1]),
            'is_published' => true,
        ]));

        foreach ($pages as $index => $page) {
            Menu::create([
                'tenant_id' => $tenantId,
                'title' => $page->title,
                'type' => 'page',
                'page_id' => $page->page_id,
                'position' => $index < 5 ? 'header' : 'footer',
                'sequence' => $index + 1,
                'is_active' => true,
            ]);
        }

        foreach (range(1, 12) as $index) {
            Pengumuman::create([
                'tenant_id' => $tenantId,
                'judul' => 'Pengumuman Kampus '.$index,
                'isi' => 'Isi pengumuman demo untuk kegiatan dan layanan kampus nomor '.$index.'.',
                'jenis' => ['Akademik', 'Kemahasiswaan', 'Umum'][$index % 3],
                'penulis_id' => $author->id,
                'is_published' => $index !== 12,
                'published_at' => now()->subDays($index),
            ]);
        }

        foreach (range(1, 8) as $index) {
            FAQ::create([
                'tenant_id' => $tenantId,
                'question' => 'Pertanyaan umum kampus nomor '.$index.'?',
                'answer' => 'Jawaban informatif untuk pertanyaan umum nomor '.$index.'.',
                'category' => ['Akademik', 'PMB', 'Layanan', 'Fasilitas'][$index % 4],
                'seq' => $index,
                'is_active' => true,
            ]);
        }

        foreach (range(1, 5) as $index) {
            Slideshow::create([
                'tenant_id' => $tenantId,
                'image_url' => 'static/img/slides/slide-'.$index.'.jpg',
                'title' => 'Informasi Kampus '.$index,
                'caption' => 'Sorotan program dan fasilitas kampus.',
                'link' => '/public/preview',
                'seq' => $index,
                'is_active' => true,
            ]);
        }

        collect([
            ['Nadia Pratama', 'Alumni', 'Program Studi Teknologi Informasi', 'Platform kampus membantu saya menemukan layanan dan informasi akademik dengan jauh lebih cepat.'],
            ['Rizky Mahendra', 'Mahasiswa', 'Fakultas Teknik', 'Informasi kegiatan, pengumuman, dan layanan kampus terasa lebih terhubung dan mudah dipahami.'],
            ['Dr. Maya Lestari', 'Dosen', 'Pusat Inovasi', 'Pengelolaan informasi yang konsisten membuat kolaborasi dan komunikasi institusi menjadi lebih efektif.'],
            ['Andi Saputra', 'Mitra Industri', 'Nusantara Digital', 'Kami melihat komitmen institusi terhadap transformasi digital dan kolaborasi yang berkelanjutan.'],
        ])->each(function (array $item, int $index) use ($tenantId) {
            Testimonial::create([
                'tenant_id' => $tenantId,
                'name' => $item[0],
                'position' => $item[1],
                'organization' => $item[2],
                'quote' => $item[3],
                'rating' => 5,
                'seq' => $index + 1,
                'is_active' => true,
            ]);
        });

        collect([
            ['Nusantara Digital', 'Industri Teknologi', 'https://example.com'],
            ['Bank Mitra Indonesia', 'Industri Keuangan', 'https://example.com'],
            ['Pemerintah Kota', 'Pemerintah', 'https://example.com'],
            ['Global Education Network', 'Pendidikan', 'https://example.com'],
            ['Creative Innovation Hub', 'Riset dan Inovasi', 'https://example.com'],
            ['Future Manufacturing', 'Industri Manufaktur', 'https://example.com'],
        ])->each(function (array $item, int $index) use ($tenantId) {
            Partner::create([
                'tenant_id' => $tenantId,
                'name' => $item[0],
                'category' => $item[1],
                'website_url' => $item[2],
                'seq' => $index + 1,
                'is_active' => true,
            ]);
        });
    }
}
