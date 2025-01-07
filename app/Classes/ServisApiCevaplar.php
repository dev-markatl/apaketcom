<?php

namespace App\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Istek ;
use App\Models\Robot;
use App\Models\Paket;
use App\Models\IstekCevap;
use App\Models\Tip;
use App\Models\Operator;
use App\Classes\CommonFunctions;
use App\Classes\RobotFunctions;

class ServisApiCevaplar 
{
    public function EksikParametre()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Uygun Parametreleri Giriniz!",
            "responseCode"=>"404",
        ]);
        
    }
    public function IpGecersiz()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Ip gecersiz ",
            "responseCode"=>"404",
        ]);
    }
    public function UygunPaketBulunamadi()
    {
        return response()->json([
            "status"=>"true",
            "message"=>"Hattiniza uygun firsat paketi bulunamadi.",
            "responseCode"=>"3",
            "amount"=>null,
            "desc"=>null,
            "avaible"=>null
        ]);
    }
  
    public function UPaketler($arr)
    {
        return response()->json([
            "status"=>"true",
            "message"=>"Iptal",
            "responseCode"=>"3",
            "amount"=>null,
            "desc"=>null,
            "avaible"=>$arr
        ]);
    }
    public function GirisBasarisiz()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Giris basarisiz",
            "responseCode"=>"404"
        ]);
    }
    public function YetkiYetersiz()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Yetki yetersiz",
            "responseCode"=>"404"
        ]);
    }
    public function IdCakismasi()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Id Cakismasi!",
            "responseCode"=>"404"
        ]);
    }
    public function IdYok()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Id Yok!",
            "responseCode"=>"404"
        ]);
    }
    public function ErrorCevap($hata)
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Hata $hata",
            "responseCode"=>"404"
        ]);
    }
    public function BasariliCevap($tutar)//sisteme kayıt kabul edildi
    {
        return response()->json([
            "status"=>"true",
            "message"=>"Islem Basarili",
            "amount" =>$tutar,
            "responseCode"=>"200"
        ]);
    }
    public function Bakiye($tutar)
    {
        return response()->json([
            "status"=>"true",
            "message"=>"Islem Basarili",
            "amount" =>$tutar,
            "responseCode"=>"200"
        ]);
    }
    public function KontorAktifDegil()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Aktif Kontor Miktari Bulunamadı.",
            "responseCode"=>"404"
        ]);
    }
    public function KurumAktifDegil()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Aktif Kurum Bulunamadı.",
            "responseCode"=>"404"
        ]);
    }
    public function BakiyeYetersiz()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Bakiyeniz Yeterli Degil.",
            "responseCode"=>"404"
        ]);
    }
    public function Islemde()
    {
        return response()->json([
            "status"=>"true",
            "message"=>"islemde",
            "responseCode"=>"2",
            "amount"=>null,
            "desc"=>null,
            "avaible"=>null
        ]);
    }
    public function Iptal()
    {
        return response()->json([
            "status"=>"true",
            "message"=>"iptal",
            "responseCode"=>"3",
            "amount"=>null,
            "desc"=>null,
            "avaible"=>null
        ]);
    }
    public function Yuklendi($aciklama,$tutar)
    {
        return response()->json([
            "status"=>"true",
            "message"=>"Talep Basarili!",
            "responseCode"=>"1",
            "amount"=>$tutar,
            "desc"=>$aciklama,
            "avaible"=>null
        ]);
    }
   
}


