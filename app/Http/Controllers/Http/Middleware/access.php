<?php

namespace App\Http\Middleware;

use Closure;

use Session;

class access
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
        if(Session::get('mem_comp') == 'null')
            return redirect('error/access-undefined');

        return $next($request);
    }
}
