<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Modules\Public\app\Http\Requests\ReorderRequest;
use Modules\Public\app\Http\Requests\SlideshowRequest;
use Modules\Public\app\Models\Slideshow;
use Modules\Public\app\Services\SlideshowService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SlideshowController extends Controller
{
    public function __construct(protected SlideshowService $slideshowService) {}

    public function index()
    {
        $slideshows = Slideshow::orderBy('seq')->get();

        return view('public::pages.cms.slideshow.index', compact('slideshows'));
    }

    public function reorder(ReorderRequest $request)
    {
        $order = $request->validated()['order'] ?? [];
        if ($order) {
            $this->slideshowService->reorderSlideshows($order);

            return jsonSuccess('Urutan slideshow berhasil diperbarui.');
        }

        return jsonError('Data urutan tidak valid.');
    }

    public function data(Request $request)
    {
        $query = $this->slideshowService->getFilteredQuery($request->all());

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('image_url', function ($row) {
                $html = '<div class="position-relative d-inline-block">';
                $html .= '<img src="'.$row->thumb_url.'" class="img-fluid rounded shadow-sm" style="max-height: 50px;" />';
                if ($row->is_external_image) {
                    $html .= '<a href="'.$row->image_url.'" target="_blank" class="position-absolute top-0 end-0 bg-primary text-white p-1 rounded-circle flex align-items-center justify-content-center shadow-sm" style="margin: -5px -5px 0 0; width: 18px; height: 18px;" title="Buka Link Gambar">';
                    $html .= '<i class="ti ti-link fs-6"></i>';
                    $html .= '</a>';
                }
                $html .= '</div>';

                return $html;
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success text-white">Aktif</span>'
                    : '<span class="badge bg-secondary text-white">Draft</span>';
            })
            ->addColumn('action', function ($row) {
                return view('components.ui.datatables-actions', [
                    'editUrl' => route('public.cms.slideshow.edit', $row->encrypted_slideshow_id),
                    'editModal' => true,
                    'deleteUrl' => route('public.cms.slideshow.destroy', $row->encrypted_slideshow_id),
                ])->render();
            })
            ->rawColumns(['image_url', 'is_active', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('public::pages.cms.slideshow.create-edit-ajax', ['slideshow' => new Slideshow]);
    }

    public function store(SlideshowRequest $request)
    {
        $this->slideshowService->createSlideshow($request->validated());

        return jsonSuccess('Slideshow berhasil ditambahkan.', route('public.cms.slideshow.index'));
    }

    public function edit(Slideshow $slideshow)
    {
        return view('public::pages.cms.slideshow.create-edit-ajax', compact('slideshow'));
    }

    public function update(SlideshowRequest $request, Slideshow $slideshow)
    {
        $this->slideshowService->updateSlideshow($slideshow->getKey(), $request->validated());

        return jsonSuccess('Slideshow berhasil diperbarui.', route('public.cms.slideshow.index'));
    }

    public function destroy(Slideshow $slideshow)
    {
        $this->slideshowService->deleteSlideshow($slideshow->getKey());

        return jsonSuccess('Slideshow berhasil dihapus.', route('public.cms.slideshow.index'));
    }
}
