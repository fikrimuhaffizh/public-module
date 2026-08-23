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

class Section extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_sections';
    protected $primaryKey = 'section_id';
    protected $appends = ['image_url'];

    protected $fillable = [
        'type', 'title', 'slug', 'description', 'icon',
        'sort_order', 'settings', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings'  => 'array',
    ];

    const TYPE_FEATURE     = 'feature';
    const TYPE_PRODUCT     = 'product';
    const TYPE_CLIENT      = 'client';
    const TYPE_PARTNER     = 'partner';
    const TYPE_TESTIMONIAL = 'testimonial';
    const TYPE_CTA         = 'cta';
    const TYPE_STATISTIC   = 'statistic';
    const TYPE_FAQ         = 'faq';
    const TYPE_SLIDESHOW   = 'slideshow';
    const TYPE_PRICING     = 'pricing';

    const TYPES = [
        self::TYPE_SLIDESHOW   => 'Slideshow',
        self::TYPE_FEATURE     => 'Fitur',
        self::TYPE_PRODUCT     => 'Produk',
        self::TYPE_CLIENT      => 'Client',
        self::TYPE_PARTNER     => 'Mitra',
        self::TYPE_TESTIMONIAL => 'Testimoni',
        self::TYPE_FAQ         => 'FAQ',
        self::TYPE_PRICING     => 'Pricing',
        self::TYPE_STATISTIC   => 'Statistik',
    ];

    const TYPE_ICONS = [
        self::TYPE_SLIDESHOW   => 'ti ti-photo',
        self::TYPE_FEATURE     => 'ti ti-star',
        self::TYPE_PRODUCT     => 'ti ti-package',
        self::TYPE_CLIENT      => 'ti ti-users-group',
        self::TYPE_PARTNER     => 'ti ti-building-community',
        self::TYPE_TESTIMONIAL => 'ti ti-message-star',
        self::TYPE_FAQ         => 'ti ti-help',
        self::TYPE_PRICING     => 'ti ti-receipt',
        self::TYPE_CTA         => 'ti ti-alert-circle',
        self::TYPE_STATISTIC   => 'ti ti-chart-bar',
    ];

    const MEDIA_COLLECTIONS = [
        self::TYPE_FEATURE     => 'image',
        self::TYPE_PRODUCT     => 'image',
        self::TYPE_CLIENT      => 'logo',
        self::TYPE_PARTNER     => 'logo',
        self::TYPE_TESTIMONIAL => 'photo',
        self::TYPE_CTA         => 'background',
        self::TYPE_SLIDESHOW   => 'slideshow_image',
        self::TYPE_PRICING     => 'image',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if ($this->type === self::TYPE_SLIDESHOW) {
            $externalUrl = $this->getSetting('external_image_url');
            if ($externalUrl && filter_var($externalUrl, FILTER_VALIDATE_URL)) {
                return $externalUrl;
            }
        }

        $collection = self::MEDIA_COLLECTIONS[$this->type] ?? null;
        if (! $collection) return null;

        $media = $this->getFirstMedia($collection);
        if (! $media) return null;

        $conversion = match ($this->type) {
            self::TYPE_FEATURE     => 'thumb',
            self::TYPE_PRODUCT     => 'card',
            self::TYPE_CLIENT      => 'logo',
            self::TYPE_PARTNER     => 'logo',
            self::TYPE_TESTIMONIAL => 'thumb',
            self::TYPE_CTA         => 'bg',
            self::TYPE_SLIDESHOW   => 'large',
            default                => null,
        };

        return sys_media_url($media, null, 60, $conversion);
    }

    public function getEncryptedSectionIdAttribute(): string
    {
        return encryptId($this->section_id);
    }

    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, $value): self
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        return $this;
    }

    public function registerMediaCollections(): void
    {
        $collectionName = self::MEDIA_COLLECTIONS[$this->type] ?? null;
        if ($collectionName) {
            $this->addMediaCollection($collectionName)->singleFile();
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $self = $this;

        match ($self->type) {
            self::TYPE_FEATURE     => $self->addMediaConversion('thumb')
                ->fit(Fit::Crop, 480, 320)
                ->keepOriginalImageFormat()
                ->nonQueued(),
            self::TYPE_PRODUCT     => $self->addMediaConversion('card')
                ->fit(Fit::Crop, 640, 400)
                ->keepOriginalImageFormat()
                ->nonQueued(),
            self::TYPE_CLIENT      => $self->addMediaConversion('logo')
                ->fit(Fit::Max, 360, 160)
                ->keepOriginalImageFormat()
                ->nonQueued(),
            self::TYPE_PARTNER     => $self->addMediaConversion('logo')
                ->fit(Fit::Max, 360, 160)
                ->keepOriginalImageFormat()
                ->nonQueued(),
            self::TYPE_TESTIMONIAL => $self->addMediaConversion('thumb')
                ->fit(Fit::Crop, 240, 240)
                ->keepOriginalImageFormat()
                ->nonQueued(),
            self::TYPE_CTA         => $self->addMediaConversion('bg')
                ->fit(Fit::Crop, 1920, 800)
                ->keepOriginalImageFormat()
                ->nonQueued(),
            self::TYPE_SLIDESHOW   => $self->addMediaConversion('thumb')
                ->fit(Fit::Crop, 400, 400)
                ->keepOriginalImageFormat()
                ->nonQueued(),
            self::TYPE_PRICING     => $self->addMediaConversion('card')
                ->fit(Fit::Crop, 640, 400)
                ->keepOriginalImageFormat()
                ->nonQueued(),
            default                => null,
        };

        // Slideshow needs an additional 'large' conversion
        if ($self->type === self::TYPE_SLIDESHOW) {
            $self->addMediaConversion('large')
                ->fit(Fit::Crop, 1200, 600)
                ->keepOriginalImageFormat()
                ->nonQueued();
        }
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public static function typeLabel(string $type): string
    {
        return self::TYPES[$type] ?? ucfirst($type);
    }

    public static function typeIcon(string $type): string
    {
        return self::TYPE_ICONS[$type] ?? 'ti ti-layout-list';
    }
}
