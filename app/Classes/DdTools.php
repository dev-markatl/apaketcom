<?php

namespace App\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Operator ;
use App\Models\Tip;
use App\Models\Kullanici;
use App\Models\Rol;
use App\Models\RobotTip;




class DdTools 
{
    public function DdBayiler( )
    {
       
        $sorgu= "SELECT k.takmaAd as adi,k.ad,k.id FROM kullanici k";
        $bayiler = DB::select($sorgu);
       
        return $bayiler;
    }

    public function DdRobotTuru( )
    {
        $robotTip=RobotTip::all();
       
        return $robotTip;
    }
    public function DdOperator( )
    {
        $operator=Operator::all();
       
        return $operator;
    }
    public function DdTip( )
    {
        $tip=Tip::all();
       
        return $tip;
    }

}



