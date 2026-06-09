<?php

use Illuminate\Support\Facades\Route;
use Modules\Public\app\Http\Controllers\Admin\FAQController;
use Modules\Public\app\Http\Controllers\Admin\PengumumanController;
use Modules\Public\app\Http\Controllers\Admin\PublicMenuController;
use Modules\Public\app\Http\Controllers\Admin\PublicPageController;
use Modules\Public\app\Http\Controllers\Admin\SlideshowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Admin Area (CMS)
Route::middleware(['auth', 'check.expired'])->prefix('admin/cms')->name('public.cms.')->group(function () {
    
    // FAQ
    Route::get('faq/data', [FAQController::class, 'data'])->name('faq.data');
    Route::resource('faq', FAQController::class);

    // Pengumuman
    Route::get('pengumuman/data', [PengumumanController::class, 'data'])->name('pengumuman.data');
    Route::resource('pengumuman', PengumumanController::class);

    // Slideshow
    Route::get('slideshow/data', [SlideshowController::class, 'data'])->name('slideshow.data');
    Route::resource('slideshow', SlideshowController::class);

    // Pages
    Route::get('page/data', [PublicPageController::class, 'data'])->name('page.data');
    Route::resource('page', PublicPageController::class);

    // Public Menu
    Route::get('menu/data', [PublicMenuController::class, 'data'])->name('menu.data');
    Route::resource('menu', PublicMenuController::class);
});

// Web Area (Landing Page)
Route::name('public.')->group(function () {
    Route::get('/', function() {
        return view('public::pages.web.index');
    })->name('index');
});
