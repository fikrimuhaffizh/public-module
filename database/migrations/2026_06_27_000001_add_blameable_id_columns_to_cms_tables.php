<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'cms_page',
        'cms_menu',
        'cms_pengumuman',
        'cms_slideshow',
        'cms_faq',
        'cms_testimonial',
        'cms_partner',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) continue;
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'created_by_id')) {
                    $table->unsignedBigInteger('created_by_id')->nullable()->after('created_by');
                }
                if (! Schema::hasColumn($tableName, 'updated_by_id')) {
                    $table->unsignedBigInteger('updated_by_id')->nullable()->after('updated_by');
                }
                if (! Schema::hasColumn($tableName, 'deleted_by_id')) {
                    $table->unsignedBigInteger('deleted_by_id')->nullable()->after('deleted_by');
                }
            });
        }
    }

    public function down(): void {}
};