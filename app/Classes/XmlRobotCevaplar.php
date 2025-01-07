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

class XmlRobotCevaplar 
{
    public function BekleyenKayitYok($robotBakiye)
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
        <sonuc>
            <hata>2</hata>
            <detay>Bekleyen kayit yok</detay>
            <bakiye>'.$robotBakiye.'</bakiye>
        </sonuc>
    ';
        
    }
    public function GirisBasarisiz()
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
        <sonuc>
            <hata>1</hata>
            <detay>Kullanici adi , sifre hatali veya bot tanımlaması yanlış lütfen kontrol edin...</detay>
        </sonuc>
        ';
    }
    public function HataliBilgi()
    {
        return 'fail';
    }
    public function YetkiYetersiz()
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
        <sonuc>
            <hata>1</hata>
            <detay>Yetki Yetersiz.</detay>
        </sonuc>
        ';
    }
    public function HataliCevap()
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
        <sonuc>
            <hata>1</hata>
            <detay>Aldiğiniz kayidin cevabini veriniz.</detay>
        </sonuc>
        ';
    }
    public function ErrorCevap($hata)
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
        <hata>99</hata>
        <detay>Api Hatası'.$hata.'</detay>';
    }
    public function BasariliCevap($id)
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
        <sonuc>
            <hata>0</hata>
            <onay>'.$id.'</onay>
        </sonuc>
        ';
    }
    public function BekleyenVar($bekleyen,$robot,$operator="Vodafone")
    {
        return '<?xml version="1.0" encoding="utf-8" ?>
        <sonuc>
            <hata>0</hata>
            <id>'.$bekleyen[0]->id.'</id>
            <paket_adi>'.$bekleyen[0]->paketAdi.'</paket_adi>
            <operator>'.$operator.'</operator>
            <tip>'.$bekleyen[0]->tipAdi.'</tip>
            <kontor>'.$bekleyen[0]->kod.'</kontor>
            <gsmno>'.$bekleyen[0]->tel.'</gsmno>
            <bakiye>'.$robot[0]->sistemBakiye.'</bakiye>
            <sayfa_no>'.'1'.'</sayfa_no>
        </sonuc>
        ';
    }

   
   
}


