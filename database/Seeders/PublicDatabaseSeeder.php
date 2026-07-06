<?php

namespace Modules\Public\Database\Seeders;

use Illuminate\Database\Seeder;

class PublicDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionPublicSeeder::class);

        $this->call(DemoPublicSeeder::class);
        $this->call(LandingContentSeeder::class);
        $this->call(LandingSectionSeeder::class);
    }
}
