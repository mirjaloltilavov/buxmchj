<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
 | O'zbekcha sahifalar "/" da, ruschasi "/ru" prefiksi bilan.
 | Har bir route ikki marta ro'yxatdan o'tadi: "home" va "ru.home".
 */
$pages = function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/courses', [PageController::class, 'courses'])->name('courses');
    Route::get('/courses/{course}', [PageController::class, 'course'])->name('course');
    Route::get('/services', [PageController::class, 'services'])->name('services');
    Route::get('/blog', [PageController::class, 'blog'])->name('blog');
    Route::get('/articles/{article}', [PageController::class, 'article'])->name('article');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::get('/maxfiylik-siyosati', [PageController::class, 'privacy'])->name('privacy');
    Route::get('/ommaviy-oferta', [PageController::class, 'offer'])->name('offer');
    // Admin → Sahifalar: qo'shimcha sahifalar (eng oxirida — boshqa manzillarni egallamasin)
    Route::get('/{page}', [PageController::class, 'page'])->where('page', '(?!(?:'.implode('|', \App\Models\Page::RESERVED).')$)[a-z0-9]+(?:-[a-z0-9]+)*')->name('page');
};

/*
 | Qidiruv tizimlari uchun tasdiqlash fayllari (Google Search Console, Yandex, Bing).
 | Admin panel → Sayt sozlamalari → Google bo'limidan yuklanadi va sayt ildizida ochiladi.
 */
Route::get('/{file}', function (string $file) {
    $path = 'verification/'.basename($file);
    abort_unless(\Illuminate\Support\Facades\Storage::disk('local')->exists($path), 404);
    $type = str_ends_with($file, '.xml') ? 'application/xml' : (str_ends_with($file, '.txt') ? 'text/plain' : 'text/html');

    return response(\Illuminate\Support\Facades\Storage::disk('local')->get($path), 200, ['Content-Type' => $type.'; charset=UTF-8']);
})->where('file', '(google[0-9a-f]+\.html|yandex_[0-9a-f]+\.html|BingSiteAuth\.xml|[A-Za-z0-9_-]+-verification\.(html|txt))')->name('verification.file');

Route::group([], $pages);
Route::prefix('ru')->name('ru.')->group($pages);

Route::get('/robots.txt', [\App\Http\Controllers\SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoController::class, 'sitemap'])->name('sitemap');

// Sayt faolligi (admin → Mijozlar → Sayt faolligi)
Route::post('/t/pv', [\App\Http\Controllers\TrackController::class, 'pv'])->middleware('throttle:120,1')->name('track.pv');
Route::post('/t/ping', [\App\Http\Controllers\TrackController::class, 'ping'])->middleware('throttle:240,1')->name('track.ping');

Route::post('/lead', [LeadController::class, 'store'])->middleware('throttle:6,1')->name('lead.store');


