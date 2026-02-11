<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->tokens()->first();

        if (! $token) {
            return response()->json([
                'error' => 'No token found',
            ], 404);
        }

        return response()->json([
            'token' => $token->getKey(),
        ]);
    }
}
