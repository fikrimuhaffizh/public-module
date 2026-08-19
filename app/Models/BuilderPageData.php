<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Data 1:1 untuk halaman render_mode='custom' (GrapesJS freeform).
 *
 * gjs_project  = editor.getProjectData() — sumber kebenaran, untuk reload editor.
 * html_compiled / css_compiled = hasil compile editor yang sudah disanitasi
 *                                server-side, siap dirender di sisi publik.
 */
class BuilderPageData extends Model
{
    use BelongsToTenant, Blameable;

    protected $table = 'cms_page_builder_data';

    protected $primaryKey = 'page_id';

    public $incrementing = false;

    protected $fillable = [
        'page_id',
        'gjs_project',
        'html_compiled',
        'css_compiled',
        'compiled_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'gjs_project' => 'array',
        'compiled_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id', 'page_id');
    }
}