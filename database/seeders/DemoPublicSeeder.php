<?php

namespace Modules\Public\Database\Seeders;

use App\Models\Sys\User;
use Illuminate\Database\Seeder;
use Modules\Public\app\Models\FAQ;
use Modules\Public\app\Models\Menu;
use Modules\Public\app\Models\Page;
use Modules\Public\app\Models\Pengumuman;
use Modules\Public\app\Models\Slideshow;

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
    }
}
