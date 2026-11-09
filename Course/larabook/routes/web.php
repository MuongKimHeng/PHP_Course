<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('child');
});
// Route::prefix('topics')->group(function(){
//     Route::get('/index', function(){return '';})->name('topics.index');
// });
// Route::prefix('blocks')->group(function(){
//     Route::get('/create', function(){return '';})->name('blocks.create');
// });
Route::resource('topics',TopicController::class);
Route::resource('blocks',BlockController::class);