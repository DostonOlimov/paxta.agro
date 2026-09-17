<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Allows the request only when the session crop (getApplicationType()) is one of the given types.
 * Usage: ->middleware('crop:2') or 'crop:3,4'
 */
class EnsureCropType
{
    public function handle(Request $request, Closure $next, ...$types)
    {
        if (!in_array((int) getApplicationType(), array_map('intval', $types), true)) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
