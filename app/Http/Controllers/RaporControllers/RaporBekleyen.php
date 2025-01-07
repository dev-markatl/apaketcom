<?php

namespace App\Http\Controllers\RaporControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Classes\YukleyiciIslemleri;
use App\Models\Kullanici;
use App\Models\Ilce;
use App\Models\Il;




class RaporBekleyen 
{
  
   
    public function index(Request $request)
    {
        try
        {
            
            $bakiye=Auth::guard("RobotAuth")->user()->sistemBakiye;
            $robotId=Auth::guard("RobotAuth")->user()->id;
            return view("RaporEkranlari/RaporAnasayfa",array("bakiye"=>$bakiye,"id"=>$robotId));
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
    }
   
  
    

}


