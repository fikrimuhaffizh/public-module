<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Public\Traits\ClearsDynamicBlockCache;

class FAQ extends Model
{
    use BelongsToTenant, Blameable, ClearsDynamicBlockCache, HasFactory, HashidBinding, SoftDeletes;

    protected $table = 'cms_faq';

    protected $primaryKey = 'faq_id';

    protected $fillable = [
        'question',
        'answer',
        'category',
        'seq',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function dynamicBlockType(): string
    {
        return 'faq';
    }
}
