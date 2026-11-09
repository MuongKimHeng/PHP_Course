<?php

use App\Http\Controllers\AboutController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return view('hello');
});

Route::get('/about', [AboutController::class, 'about']);
Route::get('/student/{name}',function(string $name){
    return '<h1> Student name. '.$name. '</h1>';
});