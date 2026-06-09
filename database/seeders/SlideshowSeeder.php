<?php

namespace Modules\Public\database\seeders;

use Modules\Public\app\Models\Slideshow;
use Illuminate\Database\Seeder;

class SlideshowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slideshow::create([
            'image_url' => 'static/img/slides/slide-1.jpg',
            'title' => 'Excellence in Education',
            'caption' => 'Empowering the next generation of industry leaders through innovative technology and hands-on education.',
            'link' => 'https://pcr.ac.id/pmb',
            'seq' => 1,
            'is_active' => true,
            'created_by' => 1,
        ]);

        Slideshow::create([
            'image_url' => 'static/img/slides/slide-2.jpg',
            'title' => 'Modern Laboratory Facilities',
            'caption' => 'State-of-the-art labs equipped with the latest industry-standard software and hardware.',
            'link' => 'https://pcr.ac.id/fasilitas',
            'seq' => 2,
            'is_active' => true,
            'created_by' => 1,
        ]);

        Slideshow::create([
            'image_url' => 'static/img/slides/slide-3.jpg',
            'title' => 'Vibrant Campus Life',
            'caption' => 'A supportive and diverse community where students can grow both academically and personally.',
            'link' => 'https://pcr.ac.id/kemahasiswaan',
            'seq' => 3,
            'is_active' => true,
            'created_by' => 1,
        ]);
    }
}
