<?php

declare(strict_types=1);

use App\Http\Controllers\Api\JokeController;
use Illuminate\Support\Facades\Route;

Route::get('/jokes', [JokeController::class, 'index'])->name('api.jokes.index');
