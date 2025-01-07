<?php

namespace App\Http\Controllers\BayiControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Models\Kullanici;
use App\Models\Ilce;
use App\Models\Il;




class BayiAyarlar 
{
    public function KullaniciGuncelle(Request $request)
    {
        try
        {
            
            $r=Kullanici::where("id",Auth::user()->id)->first();
            $m=$r->sifre;
            $mevcutSifre=$request->mSifre;
            $yeniSifre=Hash::make($request->sifre1);
            if(Hash::check($mevcutSifre,$r->sifre))
            {
                $r->sifre=$yeniSifre;
                $r->sonSifre=$request->sifre1;
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
            $uq=Kullanici::where("takmaAd",$request->takmaAd)->first();
            if($uq!=null)
            {
                return response()->json([
                    "status"=>"false",
                    "message"=>"İslem Başarısız! (Bu kullanici adi kullanimda!)"
                    
                ]);
            }
            else
            {
                return response()->json([
                    "status"=>"false",
                    "message"=>"İslem Başarısız! (".$e->getMessage().")"
                    
                ]);
            }

            
        }
    }
    public function index(Request $request)
    {
        try
        {
            
            $r=Auth::user();
            $ilce=Ilce::where('id',$r->ilceId)->first();
            $ilceAdi=$ilce->adi;
            $ilAdi=Il::where("id",$ilce->ilId)->first()->adi;
            return view("BayiEkranlari/ayarlar/BayiAyarlar",array(
                "id"=>$r->id,
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


