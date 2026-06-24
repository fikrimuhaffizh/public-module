<?php

namespace Modules\Public\app\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Cta extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_ctas';

    protected $primaryKey = 'cta_id';

    protected $appends = ['background_image_url'];

    protected $fillable = [
        'title',
        'description',
        'button_text',
        'button_link',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getBackgroundImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('background');
        return $media ? sys_media_url($media, null, 60, 'bg') : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('background')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('bg')
            ->fit(Fit::Crop, 1920, 800)
            ->nonQueued();
    }
}
