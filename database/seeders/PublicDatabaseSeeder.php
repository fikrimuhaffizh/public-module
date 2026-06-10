<?php

namespace Modules\Public\Database\Seeders;

use Illuminate\Database\Seeder;

class PublicDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionPublicSeeder::class);

        if (config('app.env') !== 'production') {
            $this->call([
                SlideshowSeeder::class,
                FAQSeeder::class,
            ]);
        }
    }
}
