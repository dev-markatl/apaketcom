<?php 
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Models\Istek  ;
use App\Models\Kullanici  ;
use App\Models\Kullanicihesaphareketleri  ;
use Illuminate\Support\Facades\Hash;
use App\Classes\ApiCevaplar;
use App\Classes\HesapIslemleri;
use App\Models\Genelayarlar;
use App\Models\Bayibilgi;
use App\Models\Karaliste;

class IstekKabul
{
    public function exIptal($operatorId,$tipAdi,$tel,$kulId,$paketId,$tekilNo,$kulAdi,$kod)
    {
        try
        {
            //banlı numaraların ex iptali
            $now = date("Y-m-d H:i:s");

            $karaListeNumara = Karaliste::where('telefon',$tel)->first();

            if($karaListeNumara)
            {
                if(($karaListeNumara->sorgu_blok == 1 && (5000<=intval($kod) && intval($kod)<=6000)))
                {
                    $karaListe= new Istek;
                    $karaListe->tel=$tel;
                    $karaListe->robotId=1;
                    $karaListe->paketId=$paketId;
                    $karaListe->kullaniciId=$kulId;
                    $karaListe->durum="6";
                    $karaListe->cevap="";
                    $karaListe->aciklama = "Sistemsel hata. Daha sonra tekrar deneyiniz.";
                    $karaListe->robotAldi=1;
                    $karaListe->robotDondu=1;
                    $karaListe->almaZamani=$now;
                    $karaListe->donmeZamani=$now;
                    $karaListe->tekilNumara=$tekilNo;
                    $karaListe->exIptal=0;
                    $karaListe->sonDegisiklikYapan="KARALISTE_BLOK_SORGU";
                    $karaListe->save();
                    return true;
                }
                else if($karaListeNumara->yukleme_blok == 1 && (!(5000<=intval($kod) && intval($kod)<=6000)))
                {
                    $karaListe= new Istek;
                    $karaListe->tel=$tel;
                    $karaListe->robotId=1;
                    $karaListe->paketId=$paketId;
                    $karaListe->kullaniciId=$kulId;
                    $karaListe->durum="6";
                    $karaListe->cevap="";
                    $karaListe->aciklama = "Sistemsel hata. Daha sonra tekrar deneyiniz.";
                    $karaListe->robotAldi=1;
                    $karaListe->robotDondu=1;
                    $karaListe->almaZamani=$now;
                    $karaListe->donmeZamani=$now;
                    $karaListe->tekilNumara=$tekilNo;
                    $karaListe->exIptal=0;
                    $karaListe->sonDegisiklikYapan="KARALISTE_BLOK_YUKLEME";
                    $karaListe->save();
                    return true;
                }
          
            }


            //sorgu yap eger durumu 5 olan 2 saat onceki kayıtlar listesinde varsa direk iptali bas! (robotun durumu 5 dondugu varsayılıyor!!)
            $yasakliNumaraVarMi=DB::select("SELECT id,durum,cevap 
                        FROM istek 
                        WHERE (durum=5 OR durum=7) AND 
                            created_at >= DATE_SUB(?, INTERVAL 2 HOUR) AND  
                            robotAldi=1 AND
                            robotDondu = 1 AND
                            robotId !=1 AND 
                            tel=? AND
                            exiptal=0 LIMIT 1",array($now,$tel));
            if(count($yasakliNumaraVarMi)!=0)
            {
                $iptal= new Istek;
                $iptal->tel=$tel;
                $iptal->robotId=1;
                $iptal->paketId=$paketId;
                $iptal->kullaniciId=$kulId;
                $iptal->durum=$yasakliNumaraVarMi[0]->durum;
                $iptal->cevap=$yasakliNumaraVarMi[0]->cevap;
                $iptal->robotAldi=1;
                $iptal->robotDondu=1;
                $iptal->almaZamani=$now;
                $iptal->donmeZamani=$now;
                $iptal->tekilNumara=$tekilNo;
                $iptal->exIptal=1;
                $iptal->sonDegisiklikYapan=$kulAdi;
                $iptal->save();
                return true;
            }
            
            
            if((5000<=intval($kod) && intval($kod)<=6000) || $tipAdi=="firsat")//normal exiptal
            {
                Log::info("EXIPTAL gsmno:$tel, kod:$kod, tipAdi=$tipAdi zaman:".date("Y-m-d H:i:s"));
                
                $sorgu=DB::select("SELECT  
                                    i.id ,
                                    i.cevap,
                                    i.durum
                            FROM 
                                    istek i,
                                    paket p
                            WHERE 
                                    i.paketId=p.id AND
                                    ( (p.kod BETWEEN 4999 AND 6001) OR i.durum=3 ) AND
                                    i.created_at >= DATE_SUB(?, INTERVAL 2 HOUR) AND
                                    i.robotAldi=1 AND
                                    i.robotDondu = 1 AND
                                    i.robotId !=1 AND
                                    i.exIptal=0 AND
                                    i.durum != 4 AND
                                    i.durum != 6 AND
                                    i.tel=?  ORDER BY id ASC LIMIT 1
                                    ",array($now,$tel));
                if(count($sorgu)==0)
                    return false;

                Log::info("EXIPTAL UYGUN  gsmno:$tel, zaman:".date("Y-m-d H:i:s"));
                $cevapPaketler=DB::select("SELECT p.id ,p.adi,p.kod FROM istekcevap ic, paket p WHERE ic.istekId=? AND ic.paketId=p.id AND p.id=? AND !(p.kod BETWEEN 4999 AND 6001) ",
                array($sorgu[0]->id,$paketId));
                if(count($cevapPaketler)!=0)
                    return false;
                Log::info("EXIPTAL Yapılıyor  gsmno:$tel, zaman:".date("Y-m-d H:i:s"));
                $iptal= new Istek;
                $iptal->tel=$tel;
                $iptal->robotId=1;
                $iptal->paketId=$paketId;
                $iptal->kullaniciId=$kulId;
                $iptal->durum=$sorgu[0]->durum;
                $iptal->cevap=$sorgu[0]->cevap;
                $iptal->robotAldi=1;
                $iptal->robotDondu=1;
                $iptal->almaZamani=$now;
                $iptal->donmeZamani=$now;
                $iptal->tekilNumara=$tekilNo;
                $iptal->exIptal=1;
                $iptal->sonDegisiklikYapan=$kulAdi;
                $iptal->save();
                return true;
            }
            else
                return false;
            
        }
        catch(\Exception $e)
        {
            $message="";
            if($e->getCode()==40001)
            {
                Log::info("Error -- 40001 -- $message");
                return null;
            }
            else
            {
                Log::info("Error -- Exiptal -- $message");
                return null;
            }

        }
        
    }
    public function kaydet($tel,$paket,$kullanici,$tekilNo,$altbayiNo,$siteAdres)
    {
        try
        {
            Log::info("KAYDET gsmno:$tel, paketId:".$paket[0]->id.", tekilNo=$tekilNo ");
            DB::beginTransaction();
            $genelAyar=Genelayarlar::where("id","1")->first();
            $islem=true;
            $kaydet= new Istek;
            $kaydet->tel=$tel;
            $kaydet->robotId=1;
            $kaydet->paketId=$paket[0]->id;
            $kaydet->kullaniciId=$kullanici[0]->id;
            $kaydet->tekilNumara=$tekilNo;
            $kaydet->sonDegisiklikYapan=$kullanici[0]->adi;
            $kaydet->altbayiNo = $altbayiNo;
            $kaydet->save();
            
            $bayiBlokaj = Bayibilgi::where("bayi_id",$altbayiNo)
                                    ->where("takma_ad",$kullanici[0]->takmaAd)
                                    ->where("site_adres",$siteAdres)
                                    ->first();            
            if(!($bayiBlokaj))
            {
                $bSorguBlokaj = 0;
                $bYuklemeBlokaj = 0;
            }
            else
            {
                Log::info("KAYDET BAYI BLOKAJ KONTROL SORGU BLOKAJ: $bayiBlokaj->sorgu_blokaj"." YUKLEME BLOKAJ: $bayiBlokaj->yukleme_blokaj");
                $bSorguBlokaj = $bayiBlokaj->sorgu_blokaj;
                $bYuklemeBlokaj = $bayiBlokaj->yukleme_blokaj;
            }
            
            $hesap=new HesapIslemleri;
            if(5000<=intval($paket[0]->kod) && intval($paket[0]->kod)<=6000)
            {
                Log::info("KAYDET Sorgu paketi gsmno:$tel, paketId:".$paket[0]->id.",paketKod:".$paket[0]->kod.", tekilNo=$tekilNo ");
                if($genelAyar->sistemiKapat == 1)
                {
                    $islem = true;
                }
                else if ($bSorguBlokaj == 1)
                {
                    $islem = true;
                }
                else
                {
                $islem=$hesap->KullaniciSorguDus($kullanici,$tel,$kullanici[0]->adi);
                }
            }
            else
            {
                Log::info("KAYDET Normal paket gsmno:$tel, paketId:".$paket[0]->id.",paketKod:".$paket[0]->kod.", tekilNo=$tekilNo ");
                 $genelAyar=Genelayarlar::where("id","1")->first();
                if($genelAyar->sistemiKapatYukleme == 1)
                {
                    $islem = true;
                }
                else if ($bYuklemeBlokaj == 1)
                {
                    $islem = true;
                }
                else
                {
                 $islem=$hesap->KullaniciPaketDus($kullanici,$tel,$paket[0]->tutar,$paket[0]->adi,$kullanici[0]->adi,'PAKETDUS',0);
                }
                
            }
            if($islem)
                DB::commit();
            else
            {
                Log::info("Error -- Kaydet -- ROLLBACK ".$islem);
                DB::rollBack();
                return null;
            }
            
            //!!!!!!! SİSTEMİ KAPAT !!!!!!!        
            if(5000<=intval($paket[0]->kod) && intval($paket[0]->kod)<=6000)
            {
              if($genelAyar->sistemiKapat == 1)
              {
                  $iptalAciklama = "Türk Telekom operatörü yüklemelerinde GENEL sorun vardır. Lütfen daha sonra tekrar deneyiniz.";
                  
                $istekUpdate=DB::update("UPDATE istek
                       SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? ,exIptal=?,aciklama=?
                       WHERE id=? ",array(0,6,1,1,1,"SYSTEM",0,$iptalAciklama,$kaydet->id));
                       
              }
              else if($genelAyar->sistemiKapatGNC == 1)
              {
                  $iptalAciklama = "Türk Telekom operatörü yüklemelerinde GENEL sorun vardır. Lütfen daha sonra tekrar deneyiniz. GNC001";
                  
                $istekUpdate=DB::update("UPDATE istek
                       SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? ,exIptal=?,aciklama=?
                       WHERE id=? ",array(0,6,1,1,1,"SYSTEM",0,$iptalAciklama,$kaydet->id));
                       
              }
              else if ($bSorguBlokaj == 1)
              {

                $blokAciklama = "";

                    $istekUpdate=DB::update("UPDATE istek
                       SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? ,exIptal=?,aciklama=?
                       WHERE id=? ",array(0,3,1,1,1,"SYSTEM BAYI BLOKAJ",1,$blokAciklama,$kaydet->id));
              }
              else
              {
                $this->BayiHareketKaydi($paket[0]->operatorNo,$kullanici[0]->takmaAd,$altbayiNo,$siteAdres);
              }
            }
            else
            {
              if($genelAyar->sistemiKapatYukleme == 1)
              {
                  $iptalAciklama = "Türk Telekom operatörü yüklemelerinde GENEL sorun vardır. Lütfen daha sonra tekrar deneyiniz.";
                  
                $istekUpdate=DB::update("UPDATE istek
                       SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? ,exIptal=?,aciklama=?
                       WHERE id=? ",array(0,6,1,1,1,"SYSTEM",0,$iptalAciklama,$kaydet->id));
              }
              else if($genelAyar->sistemiKapatYuklemeGNC == 1)
              {
                 $iptalAciklama = "Türk Telekom operatörü yüklemelerinde GENEL sorun vardır. Lütfen daha sonra tekrar deneyiniz. GNC001";
                  
                $istekUpdate=DB::update("UPDATE istek
                       SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? ,exIptal=?,aciklama=?
                       WHERE id=? ",array(0,6,1,1,1,"SYSTEM",0,$iptalAciklama,$kaydet->id));
              }
              else if ($bYuklemeBlokaj == 1)
              {
                    $blokAciklama = "Sistemsel hata. Daha sonra tekrar deneyiniz.";

                 $istekUpdate=DB::update("UPDATE istek
                       SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? ,exIptal=?,aciklama=?
                       WHERE id=? ",array(0,6,1,1,1,"SYSTEM BAYI BLOKAJ",1,$blokAciklama,$kaydet->id));
              }
            }
            //*************************
            
            
            return true;
                
        }
        catch(\Exception $e)
        {
            Log::info("Error -- Kaydet -- CATCH ".$e);
            DB::rollBack();
            $message="";
            if($e->getCode()==40001)
            {
                Log::info("Error -- 40001 -- $message");
                return null;
            }
            else
            {
                Log::info("Error -- Kaydet -- $message");
                return null;
            }
        }
       
        
    }

    
    public function BayiHareketKaydi($operator,$kullanici,$altbayiNo,$siteAdres)
    {

        $bayiHareket = new BayiNoHareket();

        $optInt = $operator;

        $bayiHareket->bayiHareketKaydi($altbayiNo,$kullanici,$siteAdres,$optInt);
        
        return true;

    }



    public function operatorKontrol($operatorAdi)
    {
        $kontrol=DB::select("SELECT COUNT(id) as toplam ,id FROM operator WHERE adi=? ",array($operatorAdi));
        if($kontrol[0]->toplam==0)
            return 0;
        return $kontrol[0]->id;
    }
    public function tipKontrol($tipAdi)
    {
        $kontrol=DB::select("SELECT COUNT(id) as toplam ,id FROM tip WHERE adi=? ",array($tipAdi));
        if($kontrol[0]->toplam==0)
            return 0;
        return $kontrol[0]->id;
    }
    
    public function bayiLogin($tel,$sifre,$ip,$api="znet",$hash="sha1")
    {
        
        $loginKontrol = DB::select("SELECT  id, firmaAdi as adi, bakiye, sifre , sonSifre , yetkiYukle ,yetkiSorgu , yetkiFatura , aktif ,sorguUcret ,takmaAd, rolId FROM kullanici WHERE takmaAd=?  LIMIT 1 ",array($tel));
        $apiCevap= new ApiCevaplar($api);
        
        if(count($loginKontrol)!=1)
        {
            Log::info("GirisBasarisiz= tel:$tel ,sifre:$sifre ,api:$api ,ip:$ip");
            $apiCevap->login("sifre");
            return null;
        }
        
        if(!Hash::check($sifre, $loginKontrol[0]->sifre))
        {
            Log::info("GirisBasarisiz Sifre= tel:$tel ,sifre:$sifre ,api:$api ,ip:$ip");
            $sifreHash=hash($hash, $loginKontrol[0]->sonSifre);
            if($sifre!=$sifreHash)
            {
                Log::info("Gİris Basarisizi ");
                $apiCevap->login("sifre");
                
                return null;
            }
            
        }
        if($loginKontrol[0]->rolId==1)
        {
            Log::info("GirisBasarisiz Admin= tel:$tel ,sifre:$sifre ,api:$api ,ip:$ip");
            $apiCevap->login("sifre");//admin kullanıcısı giris yapamaz
            return null;
        }
        $ipler=DB::select("SELECT id FROM ip WHERE kullaniciId=? AND isyeri=0",array($loginKontrol[0]->id));
        if(count($ipler)>0)
        {
            Log::info("GirisBasarisiz ip1= tel:$tel ,sifre:$sifre ,api:$api ,ip:$ip");
            $ipKontrol=DB::select("SELECT id FROM ip WHERE kullaniciId=? AND ipAdres=? AND isyeri=0",array($loginKontrol[0]->id,$ip));
            if(count($ipKontrol)==0)
            {
                Log::info("GirisBasarisiz ip1= tel:$tel ,sifre:$sifre ,api:$api ,ip:$ip");
                $apiCevap->login("ip");
                return null;
            }
        }
       
        return $loginKontrol;
    }
    public function kontrolCevap($cevap,$api="znet",$operator=1)
    {
        if(strlen($cevap)<1)
            $cevap="5000";
        $sorguyaEkle=DB::select("SELECT kod FROM paket WHERE sorguyaEkle=1 AND aktif=1 AND silindi=0 AND operatorId=?",array($operator));
        $apiCevap= new ApiCevaplar($api);

        foreach ($sorguyaEkle as $sorgu) 
        {
            $cevap=$cevap.",".$sorgu->kod;
            //Log::info("SORGU EKLE".$sorgu->kod."API:".$api);
        }
        if($api=="ServisApi")
        {
            return $apiCevap->sonuc("upaketler",$this->uPaketCozumle($cevap));
            
        }
        
        /* ZNET CEVABI DEĞİŞTİRİLDİ */
        /* *** 01.11.2019 *** */
        if($api == "znet")
        {
            $uPaketler="";
            $uPaketler=$uPaketler.implode(",",$this->uPaketCozumle($cevap) );
            $uPaketler=$uPaketler.",";
            return $apiCevap->sonuc("upaketler",$uPaketler);
        }
        
        $uPaketler="[";
        $uPaketler=$uPaketler.implode("|",$this->uPaketCozumle($cevap) );
        $uPaketler=$uPaketler."]";

        return $apiCevap->sonuc("upaketler",$uPaketler);
        

        
    }
    
      public function sadeceRobotCevap($cevap,$api="znet",$operator=1)
    {
        if(strlen($cevap)<1)
            $cevap="5000";
      
        $apiCevap= new ApiCevaplar($api);

     
        if($api=="ServisApi")
        {
            return $apiCevap->sonuc("upaketler",$this->uPaketCozumle($cevap));
            
        }
        
        $uPaketler="[";
        $uPaketler=$uPaketler.implode("|",$this->uPaketCozumle($cevap) );
        $uPaketler=$uPaketler."]";

        return $apiCevap->sonuc("upaketler",$uPaketler);
        

        
    }
    
    private function uPaketCozumle($str_paket)
    {

        $parcala=explode(",", $str_paket);
        return $parcala;
    }

    
}
?>