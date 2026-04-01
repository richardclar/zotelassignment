<?php

declare(strict_types=1);

use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/search', [SearchController::class, 'search'])->name('api.search');
Route::get('/health', fn () => response()->json(['status' => 'ok']))->name('api.health');
