<?php

namespace Modules\Public\app\Services;

use Modules\Public\app\Models\Slideshow;
use Illuminate\Support\Facades\DB;

class SlideshowService
{
    public function getFilteredQuery(array $filters = [])
    {
        return Slideshow::query();
    }

    public function createSlideshow(array $data): Slideshow
    {
        return DB::transaction(function () use ($data) {
            $slideshow = Slideshow::create($data);

            if (isset($data['slideshow_image'])) {
                $slideshow->addMedia($data['slideshow_image'])
                    ->toMediaCollection('slideshow_image');
            }

            logActivity('slideshow_management', "Menambah slideshow baru: {$slideshow->title}", $slideshow);

            return $slideshow;
        });
    }

    public function updateSlideshow(Slideshow $slideshow, array $data): bool
    {
        return DB::transaction(function () use ($slideshow, $data) {
            $slideshow->update($data);

            if (isset($data['slideshow_image'])) {
                $slideshow->addMedia($data['slideshow_image'])
                    ->toMediaCollection('slideshow_image');
            }

            logActivity('slideshow_management', "Memperbarui slideshow: {$slideshow->title}", $slideshow);

            return true;
        });
    }

    public function deleteSlideshow(Slideshow $slideshow): bool
    {
        return DB::transaction(function () use ($slideshow) {
            $title = $slideshow->title;

            $slideshow->delete();

            logActivity('slideshow_management', "Menghapus slideshow: {$title}");

            return true;
        });
    }

    public function reorderSlideshows(array $order): bool
    {
        return DB::transaction(function () use ($order) {
            foreach ($order as $index => $encryptedId) {
                // We use decryptIdIfEncrypted helper, but here we might need manual decrypt if encrypted ID is passed
                // The Helper expects decryptIdIfEncrypted to handle both.
                // However, standard is to use encrypted_{entity}_id.
                // Controller was doing: $id = decryptIdIfEncrypted($encryptedId, false);
                // Let's replicate that logic safely.

                $id = decryptIdIfEncrypted($encryptedId, false);
                if ($id) {
                    Slideshow::where('id', $id)->update(['seq' => $index + 1]);
                }
            }

            logActivity('slideshow_management', 'Memperbarui urutan slideshow');

            return true;
        });
    }
}
