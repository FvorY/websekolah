<?php

namespace App\Http\Middleware;

use Closure;

use Auth;

class step
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
        if(substr(Auth::user()->m_intro, 0, 1) == 0){
            return redirect('login/email-verification');
        }
        else if(substr(Auth::user()->m_intro, 1, 1) == 0){
            return redirect('gate/step1');
        }
        else if(substr(Auth::user()->m_intro, 2, 1) == 0){
            return redirect('gate/step2');
        }

        return $next($request);
    }
}
