<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Katalog starter template (custom / GrapesJS). Admin "pick a template"
 * saat membuat halaman custom; gjs_project template dijadikan titik awal
 * di editor, lalu admin bebas mengedit.
 */
class BuilderTemplate extends Model
{
    use BelongsToTenant, Blameable, HashidBinding, HasFactory;

    protected $table = 'cms_page_templates';

    protected $primaryKey = 'template_id';

    protected $fillable = [
        'tenant_id',
        'key',
        'name',
        'description',
        'thumbnail_url',
        'category',
        'gjs_project',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'gjs_project' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}