<?php

namespace Modules\Public\app\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Modules\Public\app\Http\Requests\LandingPageSettingRequest;
use Modules\Public\app\Models\LandingPageSetting;

class LandingPageSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:public.cms.settings.view')->only('edit');
        $this->middleware('permission:public.cms.settings.update')->only('update');
    }

    public function edit()
    {
        return view('public::pages.cms.landing-settings.settings', [
            'settings' => LandingPageSetting::forCurrentTenant(),
        ]);
    }

    public function update(LandingPageSettingRequest $request)
    {
        $settings = LandingPageSetting::forCurrentTenant();
        $settings->update(Arr::except($request->validated(), ['logo', 'favicon']));

        if ($request->hasFile('logo')) {
            $settings->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        if ($request->hasFile('favicon')) {
            $settings->addMediaFromRequest('favicon')->toMediaCollection('favicon');
        }

        return redirect()->route('cms.landing.index')
            ->with('success', 'Pengaturan landing page berhasil disimpan.');
    }
}
