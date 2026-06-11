<?php

namespace Modules\Public\app\Models;

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
        return $this->getFirstMediaUrl('logo', 'logo') ?: null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('favicon', 'favicon') ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('favicon')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media?->collection_name === 'logo') {
            $this->addMediaConversion('logo')
                ->fit(Fit::Contain, 320, 120)
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
