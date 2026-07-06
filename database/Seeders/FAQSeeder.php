<?php

namespace Modules\Public\Database\Seeders;

use Modules\Public\Models\FAQ;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FAQ::updateOrCreate(['tenant_id' => 1, 'seq' => 1], [
            'tenant_id' => 1,
            'question' => 'Bagaimana cara mendaftar sebagai mahasiswa baru?',
            'answer' => 'Pendaftaran dapat dilakukan secara online melalui portal PMB di pmb.pcr.ac.id.',
            'category' => 'Pendaftaran',
            'seq' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        FAQ::updateOrCreate(['tenant_id' => 1, 'seq' => 2], [
            'tenant_id' => 1,
            'question' => 'Program studi apa saja yang tersedia?',
            'answer' => 'Kami memiliki berbagai program studi di bidang Teknik Elektro, Teknik Komputer, Akuntansi, dan lainnya.',
            'category' => 'Akademik',
            'seq' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        FAQ::updateOrCreate(['tenant_id' => 1, 'seq' => 3], [
            'tenant_id' => 1,
            'question' => 'Kapan batas waktu pengajuan software lab?',
            'answer' => 'Pengajuan software lab biasanya dibuka di awal setiap semester. Pantau portal Lab untuk info lebih lanjut.',
            'category' => 'Fasilitas Lab',
            'seq' => 3,
            'is_active' => true,
            'created_by' => 1,
        ]);
    }
}
