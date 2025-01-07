<?php

namespace App\Http\Controllers\BayiControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;



class BayiDuyurular 
{
    public function index(Request $request)
    {
        // if (session_status() == PHP_SESSION_NONE) 
        //     session_start();
        return view("BayiEkranlari/anasayfa/Duyurular");
    }
   

}


