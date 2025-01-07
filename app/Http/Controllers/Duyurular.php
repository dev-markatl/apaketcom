<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Paket ;
use App\Models\Ilce;
use App\Models\Kullanici;
use App\Models\Rol;
use App\Classes\SessionManager;
use App\Classes\DdTools;


class Duyurular 
{
    public function index(Request $request)
    {
        // if (session_status() == PHP_SESSION_NONE) 
        //     session_start();
        return view("anasayfa/Duyurular");
    }
    public function indir(Request $request)
    {
        if($request->tip=="v")
            $file="./download/robotv.zip";
        else
            $file="./download/roboti.zip";
            
        return response()->download(public_path($file));
    }

}


