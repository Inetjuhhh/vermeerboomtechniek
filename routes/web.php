<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/diensten', [SiteController::class, 'services'])->name('services.index');
Route::get('/diensten/{service}', [SiteController::class, 'service'])->name('services.show');
Route::get('/bereken-je-offerte', [SiteController::class, 'calculator'])->name('calculator');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,10');

// Old WordPress addresses, so links and Google results keep working
Route::permanentRedirect('/aanbod-boomverzorging', '/diensten');
Route::permanentRedirect('/advies', '/diensten/boomtechnisch-advies');
Route::permanentRedirect('/boom-effect-analyse', '/diensten/boom-effect-analyse');

// Free pages like /het-bedrijf. Keep this last: it catches single-word addresses,
// except the ones the admin and Livewire use.
Route::get('/{page}', [SiteController::class, 'page'])
    ->where('page', '(?!admin$|livewire|up$|storage$)[a-z0-9-]+')
    ->name('page');
