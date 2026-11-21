<?php

use App\Http\Controllers\ManageController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShortUrlController::class, 'create'])->name('home');

Route::post('/encurtar', [ShortUrlController::class, 'store'])
    ->middleware('throttle:shorten')
    ->name('short-urls.store');

Route::prefix('gerenciar')->name('manage.')->group(function () {
    Route::get('/', [ManageController::class, 'form'])->name('form');
    Route::post('/', [ManageController::class, 'unlock'])->middleware('throttle:pin')->name('unlock');
    Route::get('/{shortUrl}', [ManageController::class, 'show'])->name('show');
});

// Deve ser a última rota: captura qualquer código curto.
Route::get('/{code}', RedirectController::class)
    ->where('code', '[A-Za-z0-9-]{1,50}')
    ->name('redirect');
