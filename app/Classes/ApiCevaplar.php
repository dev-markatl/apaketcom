<?php 
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Models\Istek  ;
use App\Models\Kullanici  ;
use App\Models\Kullanicihesaphareketleri  ;
use Illuminate\Support\Facades\Hash;
use App\Classes\ServisApiCevaplar;

class ApiCevaplar
{
    private $apiTuru;
    private $cevap;

    public function __construct($apiTuru) 
    {
        $this->apiTuru = $apiTuru;
        $this->cevap =  new ServisApiCevaplar();
    }
    public function login($hataTipi)
    {
        switch ($this->apiTuru) 
        {
            case 'ServisApi':
                return;

            break;
            case 'znet':
                if($hataTipi=="sifre")
                    echo "OK|3|bayikodu yada sifre  geçersiz|0.00";

                if($hataTipi=="ip")
                    echo "OK|3|ip geçersiz|0.00";

            break;
            case 'gencan':
                if($hataTipi=="sifre")
                    echo "101#Kullanıcı Bilgileri Hatalı";

                if($hataTipi=="ip")
                    echo "111#İzinsiz IP Adresi";

            break;
            case 'temizer':
                if($hataTipi=="sifre")
                    echo "2:Kullanıcı Bilgileri Hatalı";

                if($hataTipi=="ip")
                    echo "2:Yetkisiz Ip";

            break;
            case 'colakoglu':
                if($hataTipi=="sifre")
                    echo "02";

                if($hataTipi=="ip")
                    echo "02";

            break;
        }
    }
    public function sonuc($hataTipi,$uPaketler=null)
    {
        switch ($this->apiTuru) 
        {
            case 'ServisApi':
                if($hataTipi=="yok")
                    return $this->cevap->UygunPaketBulunamadi();

                if($hataTipi=="upaketler")
                    return $this->cevap->UPaketler($uPaketler);

            break;
            case 'znet':
                if($hataTipi=="yok")
                    echo "3:Hattiniza Uygun  Firsat Paketi Bulunamadi";

                /* ZNET CEVABI DEĞİŞTİRİLDİ */
                /* *** 01.11.2019 *** */
                if($hataTipi=="upaketler")
                echo "3:"."Ses paketi yok."." Tavsiye= ,".$uPaketler.":";

            break;
            case 'gencan':
                if($hataTipi=="yok")
                    echo "106#Hata:Hattiniza Uygun  Firsat Paketi Bulunamadi";

                if($hataTipi=="upaketler")
                    echo "106#Hata:"." - UPaketler=".$uPaketler.":";

            break;
            case 'temizer':
                if($hataTipi=="yok")
                    echo "3:Hattiniza Uygun  Firsat Paketi Bulunamadi;T=0 TL";

                if($hataTipi=="upaketler")
                    echo "3:Hata"." - UPaketler=".$uPaketler.";T=0 TL";

            break;
            case 'colakoglu':
                if($hataTipi=="yok")
                    echo "OK 3 Abone bulunamadı Hattiniza Uygun  Firsat Paketi Bulunamadi";

                if($hataTipi=="upaketler")
                    echo "OK 3 Abone bulunamadı"."--Hata--".":UPaketler=".$uPaketler;

            break;
        }
    }



    
}
?>