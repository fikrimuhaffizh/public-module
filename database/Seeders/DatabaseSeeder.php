<?php

namespace Modules\Public\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(DataSeeder::class);
        $this->call(DemoSeeder::class);
    }
}
