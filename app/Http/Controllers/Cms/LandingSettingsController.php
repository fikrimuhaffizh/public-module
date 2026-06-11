<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Public\app\Services\LandingPageService;

class LandingSettingsController extends Controller
{
    public function __construct(private LandingPageService $landing)
    {
        $this->middleware('permission:public.cms.view')->only('edit');
        $this->middleware('permission:public.cms.update')->only('update');
    }

    public function edit()
    {
        return view('public::pages.cms.landing-settings.edit', [
            'selectedTemplate' => $this->landing->template(),
            'templates' => LandingPageService::TEMPLATES,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'landing_template' => ['required', Rule::in(LandingPageService::TEMPLATES)],
        ]);

        $this->landing->saveTemplate($data['landing_template']);

        return redirect()->route('public.cms.landing.edit')
            ->with('success', 'Template landing page berhasil diperbarui.');
    }
}
