<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class Bayi
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
        if(Auth::check())
        {
            if (Auth::user()->rolId=="2")
            {
                return $next($request);
            }else
            {
            return redirect('/');  
            }
        }
        else
        {
            return redirect('/');  
        }
        
    }
}
