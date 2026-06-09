<?php

namespace Modules\Public\app\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Blameable;
use App\Traits\HashidBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use BelongsToTenant, Blameable, HasFactory, HashidBinding, InteractsWithMedia, SoftDeletes;

    protected $table = 'cms_pages';

    protected $primaryKey = 'page_id';

    protected $appends = ['encrypted_page_id'];

    public function getEncryptedPageIdAttribute()
    {
        return encryptId($this->page_id);
    }

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_desc',
        'meta_keywords',
        'is_published',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_image')
            ->singleFile();

        $this->addMediaCollection('attachments');
    }
}
