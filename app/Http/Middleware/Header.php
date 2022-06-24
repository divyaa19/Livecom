<?php

namespace App\Http\Middleware;

use Closure;

class Header
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->add([
            'content-type' => 'application/json',
            'Content-Length' => 0
        ]);


        return $next($request);
    }
}
