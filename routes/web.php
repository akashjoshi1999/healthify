<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('activities.index');
Route::match(['get', 'post'], '/filter', [HomeController::class, 'filter'])->name('activities.filter');
Route::post('/recalculate', [HomeController::class, 'recalculate'])->name('activities.recalculate');
