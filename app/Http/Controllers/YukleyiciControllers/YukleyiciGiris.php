<?php

namespace App\Http\Controllers\YukleyiciControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Robot;
use App\Models\Rol;

class YukleyiciGiris 
{
    public function index(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) 
            session_start();
        return view("YukleyiciEkranlari/YukleyiciGiris");
    }
    public function giris(Request $request)
    {
        $ip   = $_SERVER["REMOTE_ADDR"];
        $auth = Robot::where('adi',$request->ceptelNo)->where('aktif',1)->where("yukleyici",1)->first();

        
        if($auth)
        {
       
           
         
            Auth::guard("RobotAuth")->loginUsingId($auth->id,false);
            if (Auth::guard("RobotAuth")->check()) 
                return redirect()->to("yukleyici-bekleyen");
            else
                return view("YukleyiciEkranlari/YukleyiciGiris",array("message"=>"Kullanıcı Adı Yada şifre hatalı!"));
        }
        return view("YukleyiciEkranlari/YukleyiciGiris",array("message"=>"Kullanıcı Adı Yada şifre hatalı!"));

    }
    public function cikis(Request $request)
    {
        if (Auth::guard("RobotAuth")->check()) 
        {
            // The user is logged in...
            Auth::guard("RobotAuth")->logout();
        }
        return redirect()->to("yukleyici-giris");
    }
  

}


