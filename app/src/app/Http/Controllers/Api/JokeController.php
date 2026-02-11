<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JokeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class JokeController extends Controller
{
    public function __construct(
        private readonly JokeService $jokeService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $jokes = $this->jokeService->fetchJokes();

            return response()->json([
                'data' => $jokes,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch jokes', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Unable to fetch jokes at this time',
            ], 500);
        }
    }
}
