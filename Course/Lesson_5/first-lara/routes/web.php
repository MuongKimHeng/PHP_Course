<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function (){
    return view('child');
});

// Route::get('/hello', function () {
//     return view('hello');
// });

// Route::get('/about', [AboutController::class, 'about']);
// Route::get('/student/{name}',function(string $name){
//     return '<h1> Student name. '.$name. '</h1>';
// });

Route::resource('topics', TopicController::class);
Route::resource('blocks', BlockController::class);
Route::get('/users',function(){})->name('users.index');
