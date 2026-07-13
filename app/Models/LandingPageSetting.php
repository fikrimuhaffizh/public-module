<?php

namespace Modules\Public\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LandingPageSetting extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, InteractsWithMedia;
use App\Traits\HashidBinding;
use App\Traits\SoftDeletes;

    protected $table = 'cms_landing_page_settings';

    protected $fillable = [
        'site_title',
        'site_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'contact_email',
        'contact_phone',
        'whatsapp',
        'address',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
    ];

    protected $appends = ['logo_url', 'favicon_url'];

    public function getLogoUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('logo');
        return $media ? sys_media_url($media, null, 60, 'logo') : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('favicon');
        return $media ? sys_media_url($media, null, 60, 'favicon') : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('favicon')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media?->collection_name === 'logo') {
            // Use Fit::Max to resize without adding background color
            $this->addMediaConversion('logo')
                ->fit(Fit::Max, 320, 120)
                ->nonQueued();
        }

        if ($media?->collection_name === 'favicon') {
            $this->addMediaConversion('favicon')
                ->fit(Fit::Crop, 64, 64)
                ->nonQueued();
        }
    }

    public static function forCurrentTenant(): self
    {
        return static::firstOrCreate(['tenant_id' => sys_tenant_id()]);
    }
}
