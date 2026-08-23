<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Public\Traits\ClearsDynamicBlockCache;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Slideshow extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, ClearsDynamicBlockCache, HasFactory, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_slideshow';

    protected $primaryKey = 'slideshow_id';

    protected $appends = ['has_image', 'is_external_image'];

    public function getHasImageAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;

        return ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) || $this->hasMedia('slideshow_image');
    }

    public function getIsExternalImageAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;

        return $imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL);
    }

    protected $fillable = [
        'image_url',
        'title',
        'caption',
        'link',
        'seq',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('slideshow_image')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 400, 400)
            ->keepOriginalImageFormat()
            ->nonQueued();

        $this->addMediaConversion('large')
            ->fit(Fit::Crop, 1200, 600)
            ->keepOriginalImageFormat()
            ->nonQueued();
    }

    public function getImageUrlAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;
        if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }
        $media = $this->getFirstMedia('slideshow_image');

        return $media ? sys_media_url($media) : null;
    }

    public function getThumbUrlAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;
        if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }
        $media = $this->getFirstMedia('slideshow_image');

        return $media ? sys_media_url($media, null, 60, 'thumb') : null;
    }

    public function getLargeUrlAttribute()
    {
        $imageUrl = $this->attributes['image_url'] ?? null;
        if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }
        $media = $this->getFirstMedia('slideshow_image');

        return $media ? sys_media_url($media, null, 60, 'large') : null;
    }

    protected static function dynamicBlockType(): string
    {
        return 'slideshow';
    }
}
