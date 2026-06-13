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

class Feature extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_features';

    protected $primaryKey = 'feature_id';

    protected $appends = ['encrypted_feature_id', 'image_url'];

    protected $fillable = [
        'title',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getEncryptedFeatureIdAttribute(): string
    {
        return encryptId($this->getKey());
    }

    public function getImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('image');
        return $media ? sys_media_url($media, null, 60, 'thumb') : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 480, 320)
            ->nonQueued();
    }
}
