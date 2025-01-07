<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Il ;
use App\Models\Ilce  ;
use App\Models\Kullanici  ;






class AdminAyarlari 
{
    public function UpdateAdmin(Request $request)
    {
        try
        {
            
            $r=Kullanici::where("rolId",1)->first();
            $m=$r->sifre;
            $mevcutSifre=$request->mSifre;
            $yeniSifre=Hash::make($request->sifre1);
            if(Hash::check($mevcutSifre,$r->sifre))
            {
                $r->sifre=$yeniSifre;
                $r->takmaAd=$request->takmaAd;
                $r->save();
                return response()->json([
                
                    "status"=>"true",
                    "message"=>"İslem Başarılı! ",
                    
                ]);
            }
            else
            {
                return response()->json([
                
                    "status"=>"false",
                    "message"=>"Mevcut Şifrenizi Hatalı Girdiniz Lütfen Kontrol Edin! ",
                    
                ]);
            }
           
            
    
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
    public function GetAdmin(Request $request)
    {
        try
        {
            
            $r=Kullanici::where("rolId",1)->first();

            $ilce=Ilce::where('id',$r->ilceId)->first();
            $ilceAdi=$ilce->adi;
            $ilAdi=Il::where("id",$ilce->ilId)->first()->adi;
            $isyeriIpler=DB::select("SELECT * FROM ip WHERE kullaniciId=? AND isyeri=1",array($r->id));

            return view("ayarlar/AdminAyar",array(
                "id"=>$r->id,
                "isyeriIpler"=>$isyeriIpler,
                "ad"=>$r->ad,
                "soyAd"=>$r->soyAd,
                "takmaAd"=>$r->takmaAd,
                "bakiye"=>$r->bakiye,
                "sifre"=>$r->sifre,
                "yetkiYukle"=>$r->yetkiYukle,
                "yetkiSorgu"=>$r->yetkiSorgu,
                "sorguUcret"=>$r->sorguUcret,
                "firmaAdi"=>$r->firmaAdi,
                "aktif"=>$r->aktif,
                "mail"=>$r->mail,
                "sabitTel"=>$r->sabitTel,
                "vergiDairesi"=>$r->vergiDairesi,
                "vergiNo"=>$r->vergiNo,
                "cepTel"=>$r->cepTel,
                "adres"=>$r->adres,
                "ilce"=>$ilceAdi,
                "il"=>$ilAdi,
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



