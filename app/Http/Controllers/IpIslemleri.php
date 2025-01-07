<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Ip ;







class IpIslemleri 
{
    public function IpSil(Request $request)
    {
        try
        {
            $ipId=$request->id;
            DB::delete("DELETE FROM ip WHERE id=?",array($ipId));
            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı"
                
            ]);
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getCode().")"
                
            ]);
        }
    }
   
    public function SunucuEkle(Request $request)
    {
        try
        {
            
            $ip = new Ip;
            $ip->ipAdres=$request->ipAdres;
            $ip->kullaniciId=$request->id;
            $ip->sonDegisiklikYapan=Auth::user()->takmaAd;
            $ip->isyeri=0;
            $ip->save();
            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı"
                
            ]);
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getCode().")"
                
            ]);
        }
    }

    public function IsyeriEkle(Request $request)
    {
        try
        {
            $ip = new Ip;
            $ip->ipAdres=$request->ipAdres;
            $ip->kullaniciId=$request->id;
            $ip->sonDegisiklikYapan=Auth::user()->takmaAd;
            $ip->isyeri=1;
            $ip->save();
            
            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı"
                
            ]);
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getCode().")"
                
            ]);
        }
    }
}



