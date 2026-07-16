<?php

namespace Tests\Unit\PublicModule;

use PHPUnit\Framework\TestCase;

class LandingSettingsSoftDeletesMigrationTest extends TestCase
{
    public function test_soft_delete_columns_are_added_to_landing_settings_tables(): void
    {
        $migration = file_get_contents(
            dirname(__DIR__, 2).'/database/migrations/2026_07_16_000001_add_soft_deletes_to_cms_landing_settings_tables.php'
        );

        $this->assertIsString($migration);
        $this->assertStringContainsString("Schema::table('cms_landing_page_settings'", $migration);
        $this->assertStringContainsString("Schema::table('cms_landing_sections'", $migration);
        $this->assertSame(2, substr_count($migration, '$table->softDeletes();'));
    }
}
