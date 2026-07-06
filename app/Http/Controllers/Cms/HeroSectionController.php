<?php

namespace Modules\Public\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\Http\Requests\HeroSectionRequest;
use Modules\Public\Models\HeroSection;

class HeroSectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.hero.view')->only('index');
        $this->middleware('permission:public.cms.hero.create')->only(['create', 'store']);
        $this->middleware('permission:public.cms.hero.update')->only(['edit', 'update']);
        $this->middleware('permission:public.cms.hero.delete')->only('destroy');
    }

    public function index()
    {
        return view('public::pages.cms.section.hero.index', [
            'heroes' => HeroSection::orderByDesc('is_active')->orderByDesc('updated_at')->get(),
        ]);
    }

    public function create()
    {
        return view('public::pages.cms.section.hero.create-edit-ajax', ['hero' => new HeroSection]);
    }

    public function store(HeroSectionRequest $request)
    {
        $data = Arr::except($request->validated(), ['image']);
        $hero = HeroSection::create($data);

        if ($request->hasFile('image')) {
            $hero->addMediaFromRequest('image')->toMediaCollection('image');
        }

        if ($hero->is_active) {
            $this->deactivateOthers($hero);
        }

        return jsonSuccess('Hero section berhasil ditambahkan.', route('cms.hero.index'));
    }

    public function edit(HeroSection $hero)
    {
        return view('public::pages.cms.section.hero.create-edit-ajax', compact('hero'));
    }

    public function update(HeroSectionRequest $request, HeroSection $hero)
    {
        $hero->update(Arr::except($request->validated(), ['image']));

        if ($request->hasFile('image')) {
            $hero->addMediaFromRequest('image')->toMediaCollection('image');
        }

        if ($hero->is_active) {
            $this->deactivateOthers($hero);
        }

        return jsonSuccess('Hero section berhasil diperbarui.', route('cms.hero.index'));
    }

    public function destroy(HeroSection $hero)
    {
        $hero->delete();

        return jsonSuccess('Hero section berhasil dihapus.');
    }

    private function deactivateOthers(HeroSection $active): void
    {
        HeroSection::whereKeyNot($active->getKey())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
