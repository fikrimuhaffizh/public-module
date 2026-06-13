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

class HeroSection extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_hero_sections';

    protected $primaryKey = 'hero_id';

    protected $appends = ['encrypted_hero_id', 'image_url'];

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_primary_text',
        'button_primary_link',
        'button_secondary_text',
        'button_secondary_link',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getEncryptedHeroIdAttribute(): string
    {
        return encryptId($this->getKey());
    }

    public function getImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('image');
        return $media ? sys_media_url($media, null, 60, 'hero') : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('hero')
            ->fit(Fit::Crop, 1600, 900)
            ->nonQueued();
    }
}
