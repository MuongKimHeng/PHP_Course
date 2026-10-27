<?php

use App\Http\Controllers\AboutController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return view('hello');
});

Route::get('/about', [AboutController::class, 'about']);