<?php

use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'index']);
Route::get('/{kategori}', [FrontController::class, 'kategori'])->where('kategori', 'makanan|minuman|jajan|gorengan');

// ... existing code ...