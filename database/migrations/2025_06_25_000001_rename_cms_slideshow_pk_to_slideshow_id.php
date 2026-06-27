<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename PK column from 'id' to 'slideshow_id' on cms_slideshow.
        // Use raw SQL because Laravel's Schema::renameColumn has known limitations
        // on primary key / auto-increment columns across DB drivers.
        // All FK references and consumers were audited beforehand:
        //   - No other table references cms_slideshow.id as a foreign key.
        //   - No other table has a column literally named `slideshow_id` that would clash.
        //   - Application code already uses encrypted_slideshow_id, which becomes valid
        //     once $primaryKey on Slideshow model is overridden (see HashidBinding trait).
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // CHANGE COLUMN preserves AUTO_INCREMENT and PRIMARY KEY attributes
            // when they are re-declared explicitly in the column definition.
            DB::statement(
                'ALTER TABLE `cms_slideshow` '
                .'CHANGE COLUMN `id` `slideshow_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'
            );
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE cms_slideshow RENAME COLUMN id TO slideshow_id');
        } elseif ($driver === 'sqlite') {
            throw new \RuntimeException(
                'SQLite does not support renaming a primary key column via ALTER TABLE. '
                .'Please convert this migration to a table-recreation strategy for SQLite.'
            );
        } else {
            throw new \RuntimeException(
                "Unsupported database driver [{$driver}] for cms_slideshow PK rename."
            );
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                'ALTER TABLE `cms_slideshow` '
                .'CHANGE COLUMN `slideshow_id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'
            );
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE cms_slideshow RENAME COLUMN slideshow_id TO id');
        } elseif ($driver === 'sqlite') {
            throw new \RuntimeException(
                'SQLite rollback requires table recreation for PK column rename. '
                .'Please convert this migration manually for SQLite.'
            );
        } else {
            throw new \RuntimeException(
                "Unsupported database driver [{$driver}] for cms_slideshow PK rename rollback."
            );
        }
    }
};
