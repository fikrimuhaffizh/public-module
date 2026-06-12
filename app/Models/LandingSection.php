<?php

namespace Modules\Public\app\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LandingSection extends Model
{
    use BelongsToTenant, Blameable, HashidBinding;

    protected $table = 'cms_landing_sections';

    protected $primaryKey = 'landing_section_id';

    protected $appends = ['encrypted_landing_section_id'];

    protected $fillable = [
        'section_key',
        'section_name',
        'area',
        'component_name',
        'variant',
        'title',
        'pre_title',
        'post_title',
        'subtitle',
        'description',
        'sort_order',
        'limit_data',
        'is_active',
        'settings',
        'created_by',
        'created_by_id',
        'updated_by',
        'updated_by_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'limit_data' => 'integer',
    ];

    public function getEncryptedLandingSectionIdAttribute(): string
    {
        return encryptId($this->getKey());
    }

    public static function registry(): array
    {
        return config('landing_sections.sections', []);
    }

    public static function defaultRows(int $tenantId): array
    {
        $rows = [];
        $sort = ['top' => 0, 'middle' => 0, 'bottom' => 0];

        // Only use canonical keys (skip singular/plural aliases)
        $canonicalKeys = array_keys(config('landing_sections.sections', []));
        $aliasKeys = ['products', 'stats', 'features', 'testimonials', 'clients', 'announcement'];

        foreach (self::registry() as $key => $meta) {
            if (in_array($key, $aliasKeys, true)) {
                continue;
            }

            $area = $meta['area'];
            $sort[$area]++;

            $rows[] = [
                'tenant_id' => $tenantId,
                'section_key' => $key,
                'section_name' => $meta['name'],
                'area' => $area,
                'component_name' => $meta['component'],
                'variant' => $meta['default_variant'],
                'sort_order' => $sort[$area],
                'limit_data' => $meta['default_limit'] ?? 6,
                'is_active' => true,
                'settings' => ['show_title' => true, 'text_align' => 'left'],
                'pre_title' => null,
                'post_title' => null,
            ];
        }

        return $rows;
    }

    /**
     * Scope: order sections by area (top → middle → bottom), then by sort_order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw(
            "CASE area WHEN 'top' THEN 1 WHEN 'middle' THEN 2 WHEN 'bottom' THEN 3 ELSE 99 END"
        )->orderBy('sort_order');
    }
}
