<?php

use App\Http\Controllers\Web\JokeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/jokes', [JokeController::class, 'index'])->name('jokes.index');
