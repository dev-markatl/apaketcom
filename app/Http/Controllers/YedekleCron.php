<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Paket  ;
use App\Models\Robot  ;
use App\Models\Kullanici  ;






class YedekleCron 
{
    public function Yedekle(Request $request)
    {
        $this->PaketYedekle();
        $this->RobotYedekle();
        $this->KullaniciYedekle();
    }
    private function PaketYedekle()
    {
        try
        {
            $counter=0;
            $paketler=DB::select("SELECT * FROM paket WHERE silindi=0");
            foreach($paketler as $paket)
            {
               $YedekPaket=DB::connection('mysql2')->select("SELECT * FROM paket WHERE kod=? AND operatorId=? AND tipId=? AND adi=? 
                 AND silindi=0 LIMIT 1",
                array($paket->kod,$paket->operatorId,$paket->tipId,$paket->adi));
               if(count($YedekPaket)>=1)
               {
                   
                    DB::connection('mysql2')->update("UPDATE paket SET resmisatisFiyati=? , maliyetFiyati=? WHERE id=?",
                    array($paket->resmiSatisFiyati,$paket->maliyetFiyati,$YedekPaket[0]->id));
                    
                    continue;
               }
                
              
               $counter++;
               $yedek=new Paket;
               $yedek->setConnection('mysql2');
               $yedek->aktif=$paket->aktif;
               $yedek->adi=$paket->adi;
               $yedek->kod=$paket->kod;
               $yedek->operatorId=$paket->operatorId;
               $yedek->tipId=$paket->tipId;
               $yedek->maliyetFiyati=$paket->maliyetFiyati;
               $yedek->resmiSatisFiyati=$paket->resmiSatisFiyati;
               $yedek->sistemPaketKodu=$paket->sistemPaketKodu;
               $yedek->silindi=$paket->silindi;
               $yedek->sorguyaEkle=$paket->sorguyaEkle;

               $yedek->yeni=$paket->yeni;
               $yedek->gun=$paket->gun;
               $yedek->herYoneKonusma=$paket->herYoneKonusma;
               $yedek->sebekeIciKonusma=$paket->sebekeIciKonusma;
               $yedek->herYoneSms=$paket->herYoneSms;
               $yedek->internet=$paket->internet;
               $yedek->save();
            }
            
            echo "<br>Paket Yedekleme Basarili".$counter;
        }
        catch(\Exception $e)
        {
           echo "<br>Paket Yedekleme Sorun:".$e->getMessage();
        }
    }
    private function RobotYedekle()
    {
        try
        {
            $robotlar=DB::select("SELECT * FROM robot ");
            foreach($robotlar as $robot)
            {
                $yedekRobotlar=DB::connection('mysql2')->select("SELECT * FROM robot WHERE adi=? LIMIT 1",array($robot->adi));
                if(count($yedekRobotlar)==0)
                {
                    //insert robot
                    $yeniRobot=new Robot;
                    $yeniRobot->setConnection('mysql2');
                    $yeniRobot->adi=$robot->adi;
                    $yeniRobot->sifre=$robot->sifre;
                    $yeniRobot->aktif=$robot->aktif;
                    $yeniRobot->operatorId=$robot->operatorId;
                    $yeniRobot->yetkiYukle=$robot->yetkiYukle;
                    $yeniRobot->yetkiSorgu=$robot->yetkiSorgu;
                    $yeniRobot->yetkiFatura=$robot->yetkiFatura;
                    $yeniRobot->mesgul=0;
                    $yeniRobot->sistemBakiye=0;
                    $yeniRobot->posBakiye=0;
                    $yeniRobot->silindi=$robot->silindi;
                    $yeniRobot->kullaniciId=$robot->kullaniciId;
                    $yeniRobot->robotTipId=$robot->robotTipId;
                    $yeniRobot->sonDegisiklikYapan=$robot->sonDegisiklikYapan;
                    $yeniRobot->save();
                }
                else
                {
                  
                   $robotGuncelle=DB::connection('mysql2')->update("UPDATE robot SET adi=? , sifre=? , aktif=? , operatorId=? , yetkiYukle=? ,yetkiSorgu=? , yetkiFatura=? ,
                    mesgul=?,  silindi=?, kullaniciId=? , robotTipId=? , sonDegisiklikYapan=? WHERE adi=?"
                    ,array($robot->adi,$robot->sifre,$robot->aktif,$robot->operatorId,$robot->yetkiYukle,$robot->yetkiSorgu,$robot->yetkiFatura,$robot->mesgul,
                    $robot->silindi,$robot->kullaniciId,$robot->robotTipId,$robot->sonDegisiklikYapan,$robot->adi));
                    
                }
            }
            echo "<br>Robotlar Yedeklendi";
        }
        catch(\Exception $e)
        {
            echo "<br>Robot Yedeklenirken Hata Oluştu:".$e->getMessage();
        }
        

    }

    private function KullaniciYedekle()
    {
        try
        {
            $kullanicilar=DB::select("SELECT * FROM kullanici ");
            foreach($kullanicilar as $kullanici)
            {
                $yedekKullanicilar=DB::connection('mysql2')->select("SELECT * FROM kullanici WHERE takmaAd=? LIMIT 1",array($kullanici->takmaAd));
                if(count($yedekKullanicilar)==0)
                {
                    //insert robot
                    $yeniKullanici=new Kullanici;
                    $yeniKullanici->setConnection('mysql2');
                    $yeniKullanici->id=$kullanici->id;
                    $yeniKullanici->ad=$kullanici->ad;
                    $yeniKullanici->soyAd=$kullanici->soyAd;
                    $yeniKullanici->takmaAd=$kullanici->takmaAd;
                    $yeniKullanici->cepTel=$kullanici->cepTel;
                    $yeniKullanici->bakiye=0;
                    $yeniKullanici->sifre=$kullanici->sifre;
                    $yeniKullanici->sonSifre=$kullanici->sonSifre;
                    $yeniKullanici->yetkiYukle=$kullanici->yetkiYukle;
                    $yeniKullanici->yetkiSorgu=$kullanici->yetkiSorgu;
                    $yeniKullanici->yetkiFatura=$kullanici->yetkiFatura;
                    $yeniKullanici->sorguUcret=$kullanici->sorguUcret;
                    $yeniKullanici->rolId=$kullanici->rolId;
                    $yeniKullanici->firmaAdi=$kullanici->firmaAdi;
                    $yeniKullanici->aktif=$kullanici->aktif;
                    $yeniKullanici->sonDegisiklikYapan=$kullanici->sonDegisiklikYapan;
                    $yeniKullanici->ilceId=$kullanici->ilceId;
                    $yeniKullanici->save();
                }
                else
                {
                 /////////////////// 
                   $kullaniciGuncelle=DB::connection('mysql2')->update("UPDATE kullanici SET id=?,ad=? , soyAd=? , takmaAd=? , sifre=? , sonSifre=? ,yetkiYukle=? , yetkiSorgu=? ,
                    yetkiFatura=?, sorguUcret=?, rolId=?, firmaAdi=?, aktif=? , sonDegisiklikYapan=?  WHERE takmaAd=?"
                    ,array($kullanici->id,$kullanici->ad,$kullanici->soyAd,$kullanici->takmaAd,$kullanici->sifre,$kullanici->sonSifre,$kullanici->yetkiYukle,$kullanici->yetkiSorgu,
                    $kullanici->yetkiFatura,$kullanici->sorguUcret,
                    $kullanici->rolId,$kullanici->firmaAdi,$kullanici->aktif,$kullanici->sonDegisiklikYapan,$kullanici->takmaAd));
                    
                }
            }
            echo "<br>Kullanicilar Yedeklendi";
        }
        catch(\Exception $e)
        {
            echo "<br>Kullanicilar Yedeklenirken Hata Oluştu:".$e->getMessage();
        }
        

    }
}


