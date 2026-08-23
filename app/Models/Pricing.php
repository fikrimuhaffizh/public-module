<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pricing extends Model
{
    use BelongsToTenant, Blameable, HashidBinding, SoftDeletes;

    protected $table = 'cms_pricing';

    protected $primaryKey = 'pricing_id';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'period',
        'features',
        'highlight',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'highlight' => 'boolean',
        'is_active' => 'boolean',
    ];
}
