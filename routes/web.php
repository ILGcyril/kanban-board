<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return redirect()->route('spaces.index'); });
Route::get('/dashboard', function() { return redirect()->route('spaces.index'); })->name('dashboard');

Route::middleware('auth')->group(function () {

    //auth breeze routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //spaces routes
    Route::resource('/spaces', SpaceController::class);

    //boards routes
    Route::resource('/spaces/{space}/boards', BoardController::class)->except(['index', 'show', 'edit']);
    Route::get('/spaces/{space}/{board}', [BoardController::class, 'show'])->name('boards.show');

    //tasks routes
    Route::resource('/space/{space}/{board}/tasks', TaskController::class)->only(['store', 'update', 'destroy']);
    Route::post('/tasks/sort', [TaskController::class, 'sort'])->name('tasks.sort');

    //tags routes
    Route::post('/spaces/{space}/tags', [TagController::class, 'store'])->name('tags.store');
    Route::put('/spaces/{space}/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/spaces/{space}/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
});

require __DIR__.'/auth.php';