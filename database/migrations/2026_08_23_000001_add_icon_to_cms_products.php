<?php

use App\Database\Migrations\BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends BaseMigration
{
    public function up(): void
    {
        Schema::table('cms_products', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('demo_url');
        });
    }

    public function down(): void
    {
        Schema::table('cms_products', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
