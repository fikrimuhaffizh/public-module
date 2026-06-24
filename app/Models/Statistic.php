<?php

namespace Modules\Public\app\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Statistic extends Model
{
    use BelongsToTenant, Blameable, HashidBinding, SoftDeletes;

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
}
