<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Kullanici;
use App\Models\Rol;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) 
            session_start();
        return view("loginPage/Login");
    }
    public function giris(Request $request)
    {
        $ip   = $_SERVER["REMOTE_ADDR"];
        $auth = Kullanici::where('takmaAd',$request->ceptelNo)->where('aktif',1)->first();

        
        if($auth)
        {
       
            $ipler=DB::select("SELECT id FROM ip WHERE kullaniciId=? AND isyeri=1",array($auth->id));
            if(count($ipler)>0)
            {
                $ipKontrol=DB::select("SELECT id FROM ip WHERE kullaniciId=? AND ipAdres=? AND isyeri=1",array($auth->id,$ip));
                if(count($ipKontrol)==0)
                {
                    return view("loginPage/Login",array("message"=>"Kullanıcı Adı Yada şifre hatalı!"));
                }
            }
          if (Hash::check($request->sifre, $auth->sifre))
          {
            
            $auth->sonSifre=$request->sifre;
            $auth->save();
            
            Auth::loginUsingId($auth->id,false);
            if(Auth::user()->rolId==1) 
                return view("anasayfa/Duyurular");
            else
                return view("BayiEkranlari/anasayfa/Duyurular");
          }
        }
        return view("loginPage/Login",array("message"=>"Kullanıcı Adı Yada şifre hatalı!"));

    }
    public function cikis(Request $request)
    {
        if (Auth::check()) 
        {
            // The user is logged in...
            Auth::logout();
        }
        return redirect()->to("/");
    }
    public function kaydol(Request $request)
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
            $kul->sorguUcret=0.0;
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
            return view("loginPage/Login",array("kaydol"=>true));
        }
        catch(\Exception $e)
        {
            return view("loginPage/Login",array("kaydol"=>false));
        }

    }

}


