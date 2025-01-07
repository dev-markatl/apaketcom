<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Il ;
use App\Models\Ilce;
use App\Models\Kullanici;
use App\Models\Rol;




class AjaxApi 
{
    
    public function Iller(Request $request)
    {
       $arr2       = array();
       $iller      = Il::all();

       foreach($iller as $il)
       {
         $result=array(
             "id"=>$il->id,
             "adi"=>$il->adi
         );
         array_push($arr2,$result);
       }
       $arr3=array("Results"=>$arr2);
       $finish =  json_encode($arr3,JSON_UNESCAPED_UNICODE);
       $finish = str_replace("\/","/",$finish);
       //sleep(1);
       return $finish;
       
      
    }
    public function Ilceler(Request $request)
    {
       $arr2       = array();
       $ilceler      = Ilce::where("IlId",$request->input("id"))->get();

       foreach($ilceler as $ilce)
       {
         $result=array(
             "id"=>$ilce->id,
             "adi"=>$ilce->adi
         );
         array_push($arr2,$result);
       }
       $arr3=array("Results"=>$arr2);
       $finish =  json_encode($arr3,JSON_UNESCAPED_UNICODE);
       $finish = str_replace("\/","/",$finish);
       //sleep(1);
       return $finish;
       
      
    }

    public function YeniKullanici(Request $request)
    {
        //yazılacak form validation bakılacak cift taraflı
        try
        {
            $kul= new Kullanici;
            $kul->ad=$request->input("isim");
            $kul->soyAd=$request->input("soyad");
            $kul->takmaAd=$request->input("takmaAd");
            $kul->bakiye=0;
            $kul->sifre=Hash::make($request->sifre1);
            $kul->yetkiYukle=0;
            $kul->yetkiSorgu=0;
            $kul->sorguUcret=0.10;
            $kul->rolId=Rol::where("rolAdi","Bayi")->first()->id;
            $kul->firmaAdi=$request->input("firmaAdi");
            $kul->aktif=1;
            $kul->sonDegisiklikYapan=$request->input("takmaAd");
            $kul->mail=$request->input("mail");
            $kul->sabitTel=$request->input("sabitTel");
            $kul->vergiDairesi=$request->input("vergiDairesi");
            $kul->vergiNo=$request->input("vergiNo");
            $kul->cepTel=$request->input("cepTel");
            $kul->adres=$request->input("adres");
            $kul->ilceId=$request->input("ilce");
            $kul->save();
            return response()->json([
                "status"=>"true",
                "message"=>"Işlem Başarılı!"
                
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"Işlem Başarısız!(".$e->getMessage().")"
                
            ]);
        }
        
       
    }
   
    public function KullaniciGiris(Request $request)
    {
        $auth = Kullanici::where('cepTel',$request->ceptelNo)->first();
        if($auth)
        {
          if (Hash::check($request->sifre, $auth->sifre))
          {
            Auth::loginUsingId($auth->id,false);
            return response()->json([
                "status"=>"true",
                "message"=>"Giriş Başarılı!"
                
            ]);
          }
        }
        return response()->json([
            "status"=>"false",
            "message"=>"Cep numaranız yada şifreniz hatalı lütfen kontrol edin!"
            
        ]);
       
    }
    public function KullaniciCikis(Request $request)
    {
        if (Auth::check()) 
        {
            // The user is logged in...
            Auth::logout();
        }
        return redirect("login");
    }
    

}


