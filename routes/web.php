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
};

Route::group([], $pages);
Route::prefix('ru')->name('ru.')->group($pages);

Route::post('/lead', [LeadController::class, 'store'])->middleware('throttle:6,1')->name('lead.store');
