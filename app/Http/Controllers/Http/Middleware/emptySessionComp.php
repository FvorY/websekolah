<?php

namespace App\Http\Middleware;

use Closure;

use Session;

class emptySessionComp
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
        if(Session::get('mem_comp') == '')
            return redirect('login/comp-gate');

        return $next($request);
    }
}
