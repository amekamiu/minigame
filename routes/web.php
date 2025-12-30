<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\CatalogController;

Route::get('/', [GameController::class, 'top'])->name('top');
Route::get('/donuts', [GameController::class, 'donuts'])->name('donuts');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/game3', [GameController::class, 'game3'])->name('game3');