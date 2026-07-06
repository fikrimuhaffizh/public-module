<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Modules\Public\Http\Requests\FAQRequest;
use Modules\Public\Models\FAQ;
use Modules\Public\Http\Requests\ReorderRequest;
use Modules\Public\Services\FAQService;

class FAQController extends Controller
{
    public function __construct(protected FAQService $faqService) {}

    public function index()
    {
        $faqs = $this->faqService->getAllGrouped();

        return view('public::pages.cms.section.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('public::pages.cms.section.faq.create-edit-ajax', ['faq' => new FAQ]);
    }

    public function store(FAQRequest $request)
    {
        $this->faqService->createFAQ($request->validated());

        return jsonSuccess('FAQ berhasil ditambahkan.', route('cms.faq.index'));
    }

    public function edit(FAQ $faq)
    {
        return view('public::pages.cms.section.faq.create-edit-ajax', compact('faq'));
    }

    public function update(FAQRequest $request, FAQ $faq)
    {
        $this->faqService->updateFAQ($faq->getKey(), $request->validated());

        return jsonSuccess('FAQ berhasil diperbarui.', route('cms.faq.index'));
    }

    public function destroy(FAQ $faq)
    {
        $this->faqService->deleteFAQ($faq->getKey());

        return jsonSuccess('FAQ berhasil dihapus.', route('cms.faq.index'));
    }

    public function reorder(ReorderRequest $request)
    {
        $order = $request->validated()['order'] ?? [];
        if ($order) {
            $this->faqService->reorderFAQs($order);

            return jsonSuccess('Urutan FAQ berhasil diperbarui.');
        }

        return jsonError('Data urutan tidak valid.');
    }
}
