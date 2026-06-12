<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'cms_hero_sections',
        'cms_features',
        'cms_products',
        'cms_statistics',
        'cms_clients',
        'cms_ctas',
        'cms_landing_page_settings',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'created_by') && ! Schema::hasColumn($tableName, 'created_by_id')) {
                    $table->unsignedBigInteger('created_by_id')->nullable()->after('created_by');
                }
                if (Schema::hasColumn($tableName, 'updated_by') && ! Schema::hasColumn($tableName, 'updated_by_id')) {
                    $table->unsignedBigInteger('updated_by_id')->nullable()->after('updated_by');
                }
                if (Schema::hasColumn($tableName, 'deleted_by') && ! Schema::hasColumn($tableName, 'deleted_by_id')) {
                    $table->unsignedBigInteger('deleted_by_id')->nullable()->after('deleted_by');
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $cols = [];
                if (Schema::hasColumn($tableName, 'created_by_id')) {
                    $cols[] = 'created_by_id';
                }
                if (Schema::hasColumn($tableName, 'updated_by_id')) {
                    $cols[] = 'updated_by_id';
                }
                if (Schema::hasColumn($tableName, 'deleted_by_id')) {
                    $cols[] = 'deleted_by_id';
                }
                if ($cols) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
