<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome'); // Or 'child', depending on your view name
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// --- YOUR CUSTOM LARABOOK ROUTES ---
Route::post('topics/search', [TopicController::class, 'search'])->name('topics.search');
Route::middleware('auth')->group(function(){
    Route::resource('topics', TopicController::class)->only(['create', 'store']);
    Route::resource('blocks', BlockController::class)->except(['index', 'show']);
});

Route::resource('topics', TopicController::class)->only(['index', 'show']);

// --- BREEZE PROFILE ROUTES ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';