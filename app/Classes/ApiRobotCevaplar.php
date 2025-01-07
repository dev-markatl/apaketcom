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

class ApiRobotCevaplar 
{
    public function BekleyenKayitYok()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"bekleyen kayit yok"
        ]);
        
    }
    public function GirisBasarisiz()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Giriş başarısız"
        ]);
    }
    public function HataliBilgi()
    {
        return response()->json([
            "status"=> "false",
            "message"=>"bilgiler hatali"
        ]);
    }
    public function YetkiYetersiz()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"yetki yetersiz"
        ]);
    }
    public function HataliCevap()
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Aldıgın kaydın cevabını ver!"
        ]);
    }
    public function ErrorCevap($hata)
    {
        return response()->json([
            "status"=>"false",
            "message"=>"Hata $hata"
        ]);
    }
    public function BasariliCevap()
    {
        return response()->json([
            "status"=>"true",
            "message"=>"İslem Basarili"
        ]);
    }
    public function BekleyenVar($bekleyen,$robot)
    {
        return response()->json([
            "id"=> $bekleyen[0]->id,
            "number"=> $bekleyen[0]->tel,
            "type"=> $bekleyen[0]->tipAdi,
            "packet"=> $bekleyen[0]->paketAdi,
            "code"=> $bekleyen[0]->kod,
            "operator"=>$robot[0]->operatorAdi,
            "status"=>"true",
            "kategoriNo"=>$bekleyen[0]->kategoriNo,
            "kategoriAdi"=>$bekleyen[0]->kategoriAdi,
            "siraNo"=> $bekleyen[0]->siraNo
            
        ]);
    }
    public function BekleyenVarFatura($bekleyen,$robot)
    {
        return response()->json([
            "id"=> $bekleyen[0]->id,
            "number"=> $bekleyen[0]->tel,
            "type"=> "fatura",
            "lastDate"=> $bekleyen[0]->sonOdemeTarihi,
            "operator"=>$bekleyen[0]->adi,
            "amount"=>$bekleyen[0]->tutar,
            "status"=>"true"
            
        ]);
    }
   
   
}


