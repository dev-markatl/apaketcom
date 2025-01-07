<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Genelayarlar;


class OzelAyarlar 
{
    public function UpdateAyarlar(Request $request)
    {
        try
        {
        
            $genelAyarlar = Genelayarlar::first();
            $robotAltLimit = $request->robotAltLimit;
            $istekIptalSuresi = $request->istekIptalSuresi;

            $genelAyarlar->robotAltLimit = $robotAltLimit;
            $genelAyarlar->istekIptalSuresi = $istekIptalSuresi;


            $genelAyarlar->save();

            return response()->json([
            
                "status"=>"true",
                "message"=>"İslem Başarılı! ",
                
            ]);     
    
        }
        catch(\Exception $e)
        {
            $message="";
            if($e->getCode()==23000)
                $message="Bu kullanıcıAdı veya CepTel kullanımda";
            return response()->json([
                "status"=>"false",
                "message"=>"Işlem Başarısız!(".$e->getCode().") $message"
                
            ]);
        }
    }
    public function Ayarlar(Request $request)
    {
        try
        {
            
            $genelAyarlar = Genelayarlar::first();

            $robotAltLimit = $genelAyarlar->robotAltLimit;
            $istekIptalSuresi = $genelAyarlar->istekIptalSuresi;

            return view("ayarlar/OzelAyarlar",array(
                "robotAltLimit"=>$robotAltLimit,
                "istekIptalSuresi"=>$istekIptalSuresi,
                "status"=>"true",
                "message"=>"İslem Başarılı! ",
                
            ));
    
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



