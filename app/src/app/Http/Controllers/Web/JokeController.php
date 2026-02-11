<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\JokeService;
use Illuminate\View\View;

class JokeController extends Controller
{
    public function __construct(
        private readonly JokeService $jokeService
    ) {}

    public function index(): View
    {
        try {
            $jokes = $this->jokeService->fetchJokes();
            $error = null;

            if (empty($jokes)) {
                $error = 'Unable to fetch jokes at this time. Please try again.';
            }
        } catch (\Exception $e) {
            $jokes = [];
            $error = 'Unable to fetch jokes at this time. Please try again.';
        }

        return view('jokes.index', [
            'jokes' => $jokes,
            'error' => $error,
        ]);
    }
}
