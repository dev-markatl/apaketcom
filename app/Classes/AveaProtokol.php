<?php 
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Classes\CommonFunctions;
use App\Models\Robothesaphareketleri;

class AveaProtokol
{
    public function RobotDogrula($robotAdi,$robotSifre)
    {
        $robotAra=DB::select(" SELECT 
                                r.*,
                                o.adi as operatorAdi ,
                                o.id as operatorId
                            FROM 
                                robot  r,
                                operator o
                            WHERE 
                                o.id=r.operatorId AND
                                r.adi=? AND
                                r.sifre =? AND 
                                r.aktif=1 AND
                                r.silindi=0
                            LIMIT 1 ",array($robotAdi,$robotSifre));
        if(count($robotAra)<1)
        {
            return false;
        }
        return $robotAra;
    }

    public function BekleyenVarmiFatura()
    {
        return DB::select("SELECT id FROM istekfatura WHERE durum=0 AND robotAldi=0 AND robotDondu=0 AND robotId=1 LIMIT 1 ");
    }
    public function BekleyenVarmi()
    {
        return DB::select("SELECT id FROM istek WHERE durum=0 AND robotAldi=0 AND robotDondu=0 AND robotId=1 LIMIT 1 ");
    }
    
    public function BusyRobot($robot)
    {
        $operatorId=$robot[0]->operatorId;
        $robotId=$robot[0]->id;
        $busyResponse=DB::select("SELECT 
                                    i.id ,
                                    p.kod ,
                                    p.adi as paketAdi , 
                                    i.tel ,
                                    t.adi as tipAdi ,
                                    p.resmiSatisFiyati ,
                                    p.maliyetFiyati ,
                                    p.kategoriNo ,
                                    p.kategoriAdi ,
                                    p.siraNo
                                FROM 
                                    istek i , 
                                    paket p , 
                                    tip t 
                                WHERE 
                                    p.tipId=t.id AND
                                    p.operatorId=? AND 
                                    i.paketId=p.id AND 
                                    i.robotAldi=1 AND
                                    i.robotDondu=0 AND
                                    i.robotId=? 
                                LIMIT 1 ",array($operatorId,$robotId));//p.aktif=1 ve p.silindi=0 kaldırıldı

        return $busyResponse;
    }
    public function BusyRobotFatura($robot)
    {

        $robotId=$robot[0]->id;
        $kullaniciId=$robot[0]->kullaniciId;
        $busyResponse=DB::select("SELECT 
                                    i.id ,
                                    i.tel ,
                                    i.tutar ,
                                    i.sonOdemeTarihi ,
                                    i.aboneAdi ,
                                    i.kurumKodu ,
                                    i.faturaNo ,
                                    i.tesisatNo ,
                                    k.adi
                                FROM 
                                    istekfatura i ,
                                    kurum k
                                WHERE 
                                    i.kurumId=k.id AND 
                                    i.robotAldi=1 AND
                                    i.robotDondu=0 AND
                                    i.kullaniciId=? AND
                                    i.robotId=? 
                                LIMIT 1 ",array($kullaniciId,$robotId));

        return $busyResponse;
    }
    public function YetkiKontrol($robot)
    {
        $cf             =   new CommonFunctions();
        $query          =   $robot[0]->yetkiSorgu;
        $load           =   $robot[0]->yetkiYukle;
        $operatorId     =   $robot[0]->operatorId;
        $kullaniciId    =   $robot[0]->kullaniciId;

        if($query==0 && $load==1)
        {
            return $cf->BekleyenVarmiYukle($operatorId,$kullaniciId);
        }
        if($query==1 && $load==0)
        {
           return $cf->BekleyenVarMiSorgu($operatorId);
        }
        if($query==1 && $load==1)
        {
            $bekleyenVarMi=$cf->BekleyenVarmiYukle($operatorId,$kullaniciId);
            if(count($bekleyenVarMi)<1)
            {
                $bekleyenVarMi=$cf->BekleyenVarMiSorgu($operatorId);
                return $bekleyenVarMi;
            }
            else
            {
                return $bekleyenVarMi;
            }

        }
    }
    public function robotDursunMuFatura($robotId,$durum):bool
    {
        if($durum==4)
        {
            $sorgu=DB::select("SELECT id FROM istekfatura WHERE durum=4 AND robotId=? AND robotAldi=1 AND robotDondu=1 LIMIT 2",array($robotId));
            if(count($sorgu)==2)
            {
                //durdur
                return true;
            }
        }
        return false;
           
    }
    private function hesapIslemleriSorgu($kod,$durum,$kullaniciId)
    {
        if(5000<=intval($kod) && intval($kod)<=6000)
        {//sorgu
            DB::select("SELECT sorguUcret , takmaAd  FROM kullanici WHERE id=?",array($kullaniciId));
            if($durum==4)
                $kullaniciSorguIade="1";//kullaniciId  KUL-p.maliyetFiyati PAKET-p.adi-i.tel-k.takmaAd-k.sorguUcret
        }
        else
        {//paket

            if($durum==2)
                $robotHesap="1";//ROBT-bakiye
                DB::select("SELECT sorguUcret , takmaAd  FROM kullanici WHERE id=?",array($kullaniciId));
            if($durum==3 || $durum==4)
                $kullaniciIade="1";//paket adi paket kodu gerek yok
        }
    }
    public function robotBakiyeGuncellesinMi($kod,$durum):bool
    {
        if(5000<=intval($kod) && intval($kod)<=6000)
        {//sorgu
        }
        else
        {//paket
            if($durum==2)
                return true;
        }
        return false;
    }
    public function kullaniciSorguIadeVarMi($kullaniciId,$kod,$durum):array
    {
        if(5000<=intval($kod) && intval($kod)<=6000)
        {//sorgu
            // if($durum==4)
            //     return DB::select("SELECT sorguUcret , takmaAd ,bakiye , id  FROM kullanici WHERE id=?",array($kullaniciId));
        }
        return array();
    }
    public function kullaniciPaketIadeVarMi($kullaniciId,$kod,$durum):array
    {
        if(5000<=intval($kod) && intval($kod)<=6000)
        {//sorgu
        }
        else
        {//paket
            if($durum==3 || $durum==5)
              return  DB::select("SELECT sorguUcret , takmaAd , bakiye , id  FROM kullanici WHERE id=?",array($kullaniciId));
        }
        return array();
    }
    public function robotDursunMu($robotId,$durum):bool
    {
        if($durum==4)
        {
            $sorgu=DB::select("SELECT id FROM istek WHERE durum=4 AND robotId=? AND robotAldi=1 AND robotDondu=1 LIMIT 2",array($robotId));
            if(count($sorgu)==2)
            {
                //durdur
                return true;
            }
        }
        return false;
           
    }
    public function robotDurdur($robotId)
    {
        $islem=DB::update("UPDATE robot SET aktif=0 , yetkiSorgu=0 , yetkiYukle=0 WHERE id=?",array($robotId));
        return $islem;
    }
    public function robotYuklemeDurdur($robotId)
    {
        $islem=DB::update("UPDATE robot SET  yetkiYukle=0 WHERE id=?",array($robotId));
        return $islem;
    }


}
?>