<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleCheck
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if ($request->user()->role !== $role) {
            return response()->json(['message' => 'You are not allowed to do this.'], 403);
        }

        return $next($request);
    }
}
