<?php

use App\Http\Controllers\SearchPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SearchPageController::class, 'index'])->name('search.index');
Route::get('/search', [SearchPageController::class, 'search'])->name('search.submit');
