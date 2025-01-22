<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Retorna uma mensagem JSON para requisições API
        if (!$request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
