<?php

namespace App\Http\Middleware;

use Closure;

class EnableDebugbarMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user() &&
            in_array(auth()->id(), explode(',', config('debugbar.users'))) &&
            !in_array($request->route()->getName(), [
                'report.vehicles.full'
            ])
        ) {
            \Barryvdh\Debugbar\Facade::enable();
        } else {
            \Barryvdh\Debugbar\Facade::disable();
        }

        return $next($request);
    }
}
