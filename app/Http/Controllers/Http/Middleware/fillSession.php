<?php

namespace App\Http\Middleware;

use Session;

use Closure;

use Auth;

class fillSession
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
        if(count(Auth::user()->company) == 1)
            return redirect('dashboard');

        return $next($request);
    }
}
