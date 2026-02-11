<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ApiTokenComposer
{
    public function compose(View $view): void
    {
        if (Auth::check()) {
            $token = Session::get('api_token');

            if (! $token) {
                $token = Auth::user()->createToken('API Token')->plainTextToken;
                Session::put('api_token', $token);
            }

            $view->with('api_token', $token);
        }
    }
}
