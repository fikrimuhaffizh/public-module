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

class DemoSeeder extends Seeder
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

        // Pages & Menu
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

        // Pengumuman
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

        // FAQ
        FAQ::updateOrCreate(['tenant_id' => 1, 'seq' => 1], [
            'tenant_id' => 1, 'question' => 'Bagaimana cara mendaftar sebagai mahasiswa baru?',
            'answer' => 'Pendaftaran dapat dilakukan secara online melalui portal PMB.', 'category' => 'Pendaftaran',
            'seq' => 1, 'is_active' => true, 'created_by' => 1,
        ]);
        FAQ::updateOrCreate(['tenant_id' => 1, 'seq' => 2], [
            'tenant_id' => 1, 'question' => 'Program studi apa saja yang tersedia?',
            'answer' => 'Kami memiliki berbagai program studi di bidang Teknik, Komputer, Akuntansi, dan lainnya.',
            'category' => 'Akademik', 'seq' => 2, 'is_active' => true, 'created_by' => 1,
        ]);
        FAQ::updateOrCreate(['tenant_id' => 1, 'seq' => 3], [
            'tenant_id' => 1, 'question' => 'Kapan batas waktu pengajuan software lab?',
            'answer' => 'Pengajuan software lab biasanya dibuka di awal setiap semester.',
            'category' => 'Fasilitas Lab', 'seq' => 3, 'is_active' => true, 'created_by' => 1,
        ]);

        // Slideshow
        Slideshow::updateOrCreate(['tenant_id' => 1, 'seq' => 1], [
            'tenant_id' => 1, 'image_url' => 'static/img/slides/slide-1.jpg',
            'title' => 'Excellence in Education',
            'caption' => 'Empowering the next generation of industry leaders through innovative technology.',
            'link' => 'https://example.com/pmb', 'seq' => 1, 'is_active' => true, 'created_by' => 1,
        ]);
        Slideshow::updateOrCreate(['tenant_id' => 1, 'seq' => 2], [
            'tenant_id' => 1, 'image_url' => 'static/img/slides/slide-2.jpg',
            'title' => 'Modern Laboratory Facilities',
            'caption' => 'State-of-the-art labs equipped with the latest industry-standard software.',
            'link' => 'https://example.com/fasilitas', 'seq' => 2, 'is_active' => true, 'created_by' => 1,
        ]);
        Slideshow::updateOrCreate(['tenant_id' => 1, 'seq' => 3], [
            'tenant_id' => 1, 'image_url' => 'static/img/slides/slide-3.jpg',
            'title' => 'Vibrant Campus Life',
            'caption' => 'A supportive and diverse community where students can grow.',
            'link' => 'https://example.com/kemahasiswaan', 'seq' => 3, 'is_active' => true, 'created_by' => 1,
        ]);

        // Testimonials
        collect([
            ['Nadia Pratama', 'Alumni', 'Program Studi Teknologi Informasi', 'Platform kampus membantu saya menemukan layanan dan informasi akademik dengan jauh lebih cepat.'],
            ['Rizky Mahendra', 'Mahasiswa', 'Fakultas Teknik', 'Informasi kegiatan, pengumuman, dan layanan kampus terasa lebih terhubung dan mudah dipahami.'],
            ['Dr. Maya Lestari', 'Dosen', 'Pusat Inovasi', 'Pengelolaan informasi yang konsisten membuat kolaborasi dan komunikasi institusi menjadi lebih efektif.'],
            ['Andi Saputra', 'Mitra Industri', 'Nusantara Digital', 'Kami melihat komitmen institusi terhadap transformasi digital dan kolaborasi yang berkelanjutan.'],
        ])->each(function (array $item, int $index) use ($tenantId) {
            Testimonial::create([
                'tenant_id' => $tenantId, 'name' => $item[0], 'position' => $item[1],
                'organization' => $item[2], 'quote' => $item[3], 'rating' => 5,
                'seq' => $index + 1, 'is_active' => true,
            ]);
        });

        // Partners
        collect([
            ['Nusantara Digital', 'Industri Teknologi', 'https://example.com'],
            ['Bank Mitra Indonesia', 'Industri Keuangan', 'https://example.com'],
            ['Pemerintah Kota', 'Pemerintah', 'https://example.com'],
            ['Global Education Network', 'Pendidikan', 'https://example.com'],
            ['Creative Innovation Hub', 'Riset dan Inovasi', 'https://example.com'],
            ['Future Manufacturing', 'Industri Manufaktur', 'https://example.com'],
        ])->each(function (array $item, int $index) use ($tenantId) {
            Partner::create([
                'tenant_id' => $tenantId, 'name' => $item[0], 'category' => $item[1],
                'website_url' => $item[2], 'seq' => $index + 1, 'is_active' => true,
            ]);
        });
    }
}
