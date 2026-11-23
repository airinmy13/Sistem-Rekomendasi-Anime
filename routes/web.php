<?php

use App\Http\Controllers\AnimeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AnimeController::class, 'index'])->name('anime.index');
Route::get('/api/anime/search', [AnimeController::class, 'search'])->name('anime.search');
Route::get('/api/anime/{id}/recommendations', [AnimeController::class, 'recommendations'])->name('anime.recommendations');

