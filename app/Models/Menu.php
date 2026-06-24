<?php

namespace Modules\Public\app\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use BelongsToTenant, Blameable, HasFactory, HashidBinding, SoftDeletes;

    protected $table = 'cms_menu';

    protected $primaryKey = 'menu_id';

    protected $fillable = [
        'parent_id',
        'title',
        'url',
        'type',
        'page_id',
        'position',
        'target',
        'sequence',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sequence');
    }

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
