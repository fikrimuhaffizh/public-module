<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_products';

    protected $primaryKey = 'product_id';

    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'demo_url',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('image');
        return $media ? sys_media_url($media, null, 60, 'card') : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 640, 400)
            ->keepOriginalImageFormat()
            ->nonQueued();
    }
}
