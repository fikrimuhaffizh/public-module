<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Public\Traits\ClearsDynamicBlockCache;

class Statistic extends Model
{
    use BelongsToTenant, Blameable, ClearsDynamicBlockCache, HashidBinding, SoftDeletes;

    protected $table = 'cms_statistics';

    protected $primaryKey = 'statistic_id';

    protected $fillable = [
        'label',
        'value',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected static function dynamicBlockType(): string
    {
        return 'statistik';
    }
}
