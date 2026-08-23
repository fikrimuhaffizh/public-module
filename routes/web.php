<?php

use Illuminate\Support\Facades\Route;
use Modules\Public\Http\Controllers\Cms\FAQController;
use Modules\Public\Http\Controllers\Cms\PengumumanController;
use Modules\Public\Http\Controllers\Cms\PublicMenuController;
use Modules\Public\Http\Controllers\Cms\PublicPageController;
use Modules\Public\Http\Controllers\Cms\SlideshowController;
use Modules\Public\Http\Controllers\Cms\ClientController;
use Modules\Public\Http\Controllers\Cms\CtaController;
use Modules\Public\Http\Controllers\Cms\FeatureController;
use Modules\Public\Http\Controllers\Cms\LandingPageSettingController;
use Modules\Public\Http\Controllers\Cms\SectionController;
use Modules\Public\Http\Controllers\Cms\PartnerController;
use Modules\Public\Http\Controllers\Cms\PricingController;
use Modules\Public\Http\Controllers\Cms\ProductController;
use Modules\Public\Http\Controllers\Cms\StatisticController;
use Modules\Public\Http\Controllers\Cms\TestimonialController;
use Modules\Public\Http\Controllers\Web\BuilderPublicController;
use Modules\Public\Http\Controllers\Web\PublicController;
use App\Http\Middleware\HandleInertiaRequests;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Admin Area (CMS)
Route::middleware(['auth', 'check.expired', 'module:public'])->prefix('cms')->name('cms.')->group(function () {
    Route::get('/dashboard', [Modules\Public\Http\Controllers\Cms\DashboardController::class, 'index'])->name('dashboard');

    // Section Management (was landing)
    Route::get('section', [SectionController::class, 'index'])->name('landing.index');
    Route::get('section/template', [SectionController::class, 'edit'])->name('landing.template');
    Route::put('section/template', [SectionController::class, 'update'])->name('landing.template.update');
    Route::get('section/{section}/edit', [SectionController::class, 'editSection'])->name('landing.edit-section');
    Route::put('section/{section}', [SectionController::class, 'updateSection'])->name('landing.update-section');
    Route::post('section/{section}/toggle', [SectionController::class, 'toggleSection'])->name('landing.toggle-section');
    Route::post('section/reorder', [SectionController::class, 'reorderSections'])->name('landing.reorder-sections');
    Route::post('section/reorder-all', [SectionController::class, 'reorderAllSections'])->name('landing.reorder-all-sections');
    Route::post('section/upload-logo', [SectionController::class, 'uploadLogo'])->name('landing.upload-logo');
    Route::post('section/upload-background', [SectionController::class, 'uploadBackground'])->name('landing.upload-background');
    Route::delete('section/delete-logo/{collection}', [SectionController::class, 'deleteLogo'])->name('landing.delete-logo');

    // Legacy redirects (backward compatibility)
    Route::redirect('landing', '/cms/section')->name('landing.redirect');
    Route::redirect('landing-template', '/cms/section/template')->name('landing.redirect-template');
    Route::redirect('landing-sections', '/cms/section')->name('landing.redirect-sections');

    // Combined Settings (Media Sosial + SEO)
    Route::get('settings', [LandingPageSettingController::class, 'editSettings'])->name('settings.edit');
    Route::put('settings', [LandingPageSettingController::class, 'updateSettings'])->name('settings.update');

    // Legacy redirects (backward compatibility)
    Route::redirect('media-social', '/cms/settings')->name('media-social.edit');
    Route::redirect('seo', '/cms/settings')->name('seo.edit');

    Route::post('feature/reorder', [FeatureController::class, 'reorder'])->name('feature.reorder');
    Route::post('feature/{feature}/toggle', [FeatureController::class, 'toggle'])->name('feature.toggle');
    Route::resource('feature', FeatureController::class)->except('show');
    Route::post('product/reorder', [ProductController::class, 'reorder'])->name('product.reorder');
    Route::post('product/{product}/toggle', [ProductController::class, 'toggle'])->name('product.toggle');
    Route::resource('product', ProductController::class)->except('show');
    Route::post('statistic/reorder', [StatisticController::class, 'reorder'])->name('statistic.reorder');
    Route::resource('statistic', StatisticController::class)->except('show');
    Route::post('client/reorder', [ClientController::class, 'reorder'])->name('client.reorder');
    Route::resource('client', ClientController::class)->except('show');
    Route::resource('cta', CtaController::class)->except('show');

    // ── Unified Section routes ──────────────────────────────────────
    Route::get("section/unified", [Modules\Public\Http\Controllers\Cms\SectionControllerUnified::class, "index"])->name("section.index");
    Route::get("section/unified/create", [Modules\Public\Http\Controllers\Cms\SectionControllerUnified::class, "create"])->name("section.create");
    Route::post("section/unified", [Modules\Public\Http\Controllers\Cms\SectionControllerUnified::class, "store"])->name("section.store");
    Route::get("section/unified/{section}/edit", [Modules\Public\Http\Controllers\Cms\SectionControllerUnified::class, "edit"])->name("section.edit");
    Route::put("section/unified/{section}", [Modules\Public\Http\Controllers\Cms\SectionControllerUnified::class, "update"])->name("section.update");
    Route::delete("section/unified/{section}", [Modules\Public\Http\Controllers\Cms\SectionControllerUnified::class, "destroy"])->name("section.destroy");
    Route::post("section/unified/{section}/toggle", [Modules\Public\Http\Controllers\Cms\SectionControllerUnified::class, "toggle"])->name("section.toggle");
    Route::post("section/unified/reorder", [Modules\Public\Http\Controllers\Cms\SectionControllerUnified::class, "reorder"])->name("section.reorder");

    // Pricing
    Route::post('pricing/reorder', [PricingController::class, 'reorder'])->name('pricing.reorder');
    Route::resource('pricing', PricingController::class)->except('show');

    // FAQ
    Route::post('faq/reorder', [FAQController::class, 'reorder'])->name('faq.reorder');
    Route::resource('faq', FAQController::class);

    // Pengumuman
    Route::get('pengumuman/data', [PengumumanController::class, 'data'])->name('pengumuman.data');
    Route::resource('pengumuman', PengumumanController::class);

    // Berita (menggunakan PengumumanController dengan type berita)
    Route::prefix('berita')->name('berita.')->controller(PengumumanController::class)->group(function () {
        Route::get('/data', 'data')->name('data');
        Route::get('/', 'beritaIndex')->name('index');
        Route::get('/create', 'create')->defaults('type', 'berita')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{pengumuman}', 'show')->name('show');
        Route::get('/{pengumuman}/edit', 'edit')->name('edit');
        Route::put('/{pengumuman}', 'update')->name('update');
        Route::delete('/{pengumuman}', 'destroy')->name('destroy');
    });

    // Slideshow
    Route::post('slideshow/reorder', [SlideshowController::class, 'reorder'])->name('slideshow.reorder');
    Route::get('slideshow/data', [SlideshowController::class, 'data'])->name('slideshow.data');
    Route::resource('slideshow', SlideshowController::class);

    Route::post('testimonial/reorder', [TestimonialController::class, 'reorder'])->name('testimonial.reorder');
    Route::resource('testimonial', TestimonialController::class)->except('show');

    Route::post('partner/reorder', [PartnerController::class, 'reorder'])->name('partner.reorder');
    Route::resource('partner', PartnerController::class)->except('show');

    // Pages
    Route::get('public-page/data', [PublicPageController::class, 'data'])->name('page.data');
    Route::resource('public-page', PublicPageController::class)
        ->names('page')
        ->parameters(['public-page' => 'page']);

    // Public Menu
    Route::post('public-menu/reorder', [PublicMenuController::class, 'reorder'])->name('menu.reorder');
    Route::post('public-menu/reorder-position', [PublicMenuController::class, 'reorderPosition'])->name('menu.reorder-position');
    Route::get('public-menu/data', [PublicMenuController::class, 'data'])->name('menu.data');
    Route::resource('public-menu', PublicMenuController::class)
        ->names('menu')
        ->parameters(['public-menu' => 'menu']);

    // Website Builder (render_mode='custom' — GrapesJS freeform)
    Route::prefix('builder')->name('builder.')->controller(Modules\Public\Http\Controllers\Cms\BuilderPageController::class)->group(function () {
        Route::get('pages/data', 'data')->name('pages.data');
        Route::get('pages', 'index')->name('pages.index');
        Route::get('pages/create', 'create')->name('pages.create');
        Route::post('pages', 'store')->name('pages.store');
        Route::get('pages/{page}/edit', 'edit')->name('pages.edit');
        Route::put('pages/{page}', 'update')->name('pages.update');
        Route::delete('pages/{page}', 'destroy')->name('pages.destroy');
        Route::post('pages/{page}/publish', 'publish')->name('pages.publish');
        Route::post('pages/{page}/unpublish', 'unpublish')->name('pages.unpublish');
        Route::get('pages/{page}/preview', 'preview')->name('pages.preview');
        Route::get('pages/{page}/project', 'project')->name('pages.project');
        Route::post('pages/{page}/save-project', 'saveProject')->name('pages.save-project');
        Route::post('pages/{page}/upload', 'upload')->name('pages.upload');
        Route::get('pages/{page}/editor', 'editor')->name('pages.editor');
    });
});

// Web Area (Landing Page) — no prefix, routes register at / level (e.g. /, /contact-us, /page/{slug})
Route::middleware(HandleInertiaRequests::class)->controller(PublicController::class)->name('public.')->group(function () {
    Route::get('/', 'home')->name('index');
    Route::get('/preview', 'preview')->name('preview');
    Route::post('/preview/design', 'saveDesign')->middleware('auth')->name('design.save');
    Route::get('/contact-us', 'contact')->name('contact');
    Route::post('/contact-us', 'sendContact')->middleware('throttle:5,1')->name('contact.send');
    Route::get('/page/{page:slug}', 'showPage')->name('page.show');
    Route::get('/announcements', 'showAllNews')->name('announcements.index');
    Route::get('/news/{pengumuman}', 'showNews')->name('news.show');
});

// Website Builder — halaman public (/{slug}), dirender via Laravel Blade + HTML.
// Existing route di atas diregistrasi lebih dulu sehingga memiliki prioritas.
// Slug hanya mengambil satu segmen lowercase (a-z, angka, strip).
Route::middleware('web')->controller(BuilderPublicController::class)->name('public.')->group(function () {
    Route::get('/{slug}', 'show')
        ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
        ->name('builder.show');
});
