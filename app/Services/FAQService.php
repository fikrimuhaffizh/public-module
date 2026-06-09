<?php

namespace Modules\Public\app\Services;

use Modules\Public\app\Models\FAQ;
use Illuminate\Support\Facades\DB;

class FAQService
{
    public function getFilteredQuery(array $filters = [])
    {
        return FAQ::query();
    }

    public function createFAQ(array $data): FAQ
    {
        return DB::transaction(function () use ($data) {
            $faq = FAQ::create($data);

            logActivity('faq_management', "Menambah FAQ baru: {$faq->question}", $faq);

            return $faq;
        });
    }

    public function updateFAQ(FAQ $faq, array $data): bool
    {
        return DB::transaction(function () use ($faq, $data) {
            $faq->update($data);

            logActivity('faq_management', "Memperbarui FAQ: {$faq->question}", $faq);

            return true;
        });
    }

    public function deleteFAQ(FAQ $faq): bool
    {
        return DB::transaction(function () use ($faq) {
            $question = $faq->question;

            $faq->delete();

            logActivity('faq_management', "Menghapus FAQ: {$question}");

            return true;
        });
    }

    public function getAllGrouped()
    {
        return FAQ::orderBy('category')->orderBy('seq')->get()->groupBy('category');
    }

    public function reorderFAQs(array $order)
    {
        return DB::transaction(function () use ($order) {
            foreach ($order as $category => $items) {
                // If category is "null" string or empty, treat as null (General)
                $catValue = ($category === 'null' || $category === '') ? null : $category;

                if (is_array($items)) {
                    foreach ($items as $index => $encryptedId) {
                        $id = decryptIdIfEncrypted($encryptedId, false);
                        if ($id) {
                            FAQ::where('id', $id)->update([
                                'seq' => $index + 1,
                                'category' => $catValue,
                            ]);
                        }
                    }
                }
            }
            logActivity('faq_management', 'Memperbarui urutan FAQ');

            return true;
        });
    }
}
