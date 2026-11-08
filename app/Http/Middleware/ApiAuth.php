<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $user = $token ? User::where('api_token', $token)->first() : null;

        if (! $user) {
            return response()->json(['message' => 'Please login first.'], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
