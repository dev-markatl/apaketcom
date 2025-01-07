<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class Rapor
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
            if (Auth::guard("RobotAuth")->user()->yetkiYukle=="1" && Auth::guard("RobotAuth")->user()->yukleyici=="0")
            {
                return $next($request);
            }else
            {
            return redirect('rapor-giris');  
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