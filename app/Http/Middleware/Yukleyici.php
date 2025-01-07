<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class Yukleyici
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
        if(Auth::guard("RobotAuth")->check())
        {
            if (Auth::guard("RobotAuth")->user()->yukleyici=="1")
            {
                return $next($request);
            }
            else if(Auth::guard("RobotAuth")->user()->yetkiYukle=="1")
            {
                return $next($request);
            }
            else
            {
            return redirect('yukleyici-giris');  
            }
        }
        else
        {
            if(Auth::check())
            {
                if (Auth::user()->rolId=="1")
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
}

?>