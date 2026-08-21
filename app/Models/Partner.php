<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Public\Traits\ClearsDynamicBlockCache;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Partner extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, ClearsDynamicBlockCache, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_partner';

    protected $primaryKey = 'partner_id';

    protected $appends = ['logo_url'];

    protected $fillable = [
        'name',
        'category',
        'website_url',
        'seq',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getLogoUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('logo');
        return $media ? sys_media_url($media, null, 60, 'logo') : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('logo')
            ->fit(Fit::Contain, 360, 160)
            ->nonQueued();
    }

    protected static function dynamicBlockType(): string
    {
        return 'partner';
    }
}
