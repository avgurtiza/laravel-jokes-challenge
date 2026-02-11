<?php

declare(strict_types=1);

use App\Http\Controllers\Api\JokeController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jokes', [JokeController::class, 'index'])->name('api.jokes.index');
    Route::get('/token', [TokenController::class, 'show']);
});
