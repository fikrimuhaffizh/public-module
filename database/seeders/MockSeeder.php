<?php

namespace Modules\Public\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class MockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder is for testing purposes only to insert 500,000 random permissions.
     * It will be deleted after testing.
     */
    public function run(): void
    {
        $permissions = [];
        $batchSize = 1000; // Insert in batches to prevent memory issues

        echo "Starting to create 500,000 random permissions...\n";

        for ($i = 0; $i < 500; $i++) {
            $permissions[] = [
                'name' => 'test_permission_'.Str::random(10).'_'.$i,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert in batches to prevent memory issues
            if (($i + 1) % $batchSize === 0) {
                Permission::insert($permissions);
                $permissions = []; // Reset the array
                echo 'Inserted batch ending with permission '.($i + 1)."\n";
            }
        }

        // Insert remaining permissions if any
        if (! empty($permissions)) {
            Permission::insert($permissions);
            echo "Inserted final batch of permissions\n";
        }

        echo "Successfully created 500,000 random permissions\n";
    }
}
