<?php
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Models\Istek  ;
use App\Models\Kullanici  ;
use App\Models\Kullanicihesaphareketleri  ;
use Illuminate\Support\Facades\Hash;
use App\Classes\CommonFunctions;
use App\Classes\RobotFunctions;
use App\Classes\ApiRobotCevaplar;
use App\Classes\HesapIslemleri;
use Carbon\Carbon;

class YukleyiciIslemleri
{
    public function KontorBekleyenSayisi($kullaniciId)
    {
        //robot yetkilerine bakmaksızın sayıyı dönüyor
        $counter= DB::select("SELECT count(id) as bekleyenSayisi FROM istek WHERE durum=0 AND robotAldi=0 AND robotDondu=0 AND robotId=1 AND kullaniciId=? ",array($kullaniciId));
        $count=$counter[0]->bekleyenSayisi;
        return $count;
    }
     public function YetkiyeGoreBekleyenSayisi($robot)
    {
        $cf= new CommonFunctions();
        $sayi=0;
        /*
        if($robot->yetkiSorgu==1)
            $sayi=$sayi+$cf->BekleyenSorguSayisi($robot->operatorId);
        */
      // dd($robot);
        
        if($robot[0]->yetkiSorgu==1)
            $sayi=$sayi+$cf->BekleyenSorguSayisi($robot[0]->operatorId);
        if($robot[0]->yetkiYukle==1)
            $sayi=$sayi+$cf->KontorBekleyenSayisi($robot[0]->operatorId,$robot[0]->kullaniciId,$robot[0]->sure_siniri,$robot[0]->fiyatgrubuId);


        return $sayi;
    }

    public function YukleyiciCevapsizKontrol($robot)
    {
        $cevapsizIslem = DB::select("SELECT * FROM istek WHERE robotId=? AND robotAldi=1 AND robotDondu=0 AND durum=1 LIMIT 1",array($robot->id));

        if (count($cevapsizIslem)<=0)
            return false;
        
        return true;
    }

    public function KontorBekleyenCek($robotAdi,$sifre)
    {
        $rf=new RobotFunctions();
        $cf=new CommonFunctions();
        //performansı gözden gecirilebilir direk sessiondaki veriler kullanılabilir
        //loglar yazılacak
        $robot=$rf->GetRobot($robotAdi,$sifre);
        
        //$count=$this->KontorBekleyenSayisi($robot[0]->kullaniciId);
        $count=$this->YetkiyeGoreBekleyenSayisi($robot);

        
        if($count<1)
            return array();
        if($robot[0]->mesgul!=0)
        {
            $busyRobot=$rf->BusyRobot($robot);
            if(count($busyRobot)==0)
            {
                Log::info("YUKLEYİCİ-Robot:$robotAdi Mesgul Düzeltildi!");
                DB::update("UPDATE robot SET mesgul=0 WHERE id=?",array($robot[0]->id));
            }
            else
            {
                Log::info("YUKLEYİCİ-Robot:$robotAdi Mesgul Geldi! tel:".$busyRobot[0]->tel);
                return $busyRobot;
            }
        }
        if($robot[0]->yetkiYukle==1)
            $bekleyenVarMi=$cf->BekleyenVarmiYukle($robot[0]->operatorId,$robot[0]->kullaniciId,$robot[0]->fiyatgrubuId);
        else
            $bekleyenVarMi=array();

        if(count($bekleyenVarMi)==0)
        {
            Log::info("YUKLEYİCİ-Robot:$robotAdi yetkiKontorl Bekleyen YOK Kontor");
            return array();
        }

        $simdikiZaman = Carbon::now();
        $sistemZaman = $bekleyenVarMi[0]->sistemTarihi;
        $sureFarki = $simdikiZaman->diffInSeconds($sistemZaman);

        if ($robot[0]->sure_siniri > $sureFarki)
        {
          Log::info("YUKLEYICI-Robot:$robotAdi SURESINIR_KONTROL Bekleyen YOK Kontor");
          return array();
        }


        try//kontor
        {
            $now=date('Y-m-d H:i:s', time());
            DB::beginTransaction();

            $KilitNoktasi=DB::select("SELECT * FROM istek WHERE id=? AND robotAldi=0 AND robotDondu=0 AND robotId=1 LIMIT 1 for UPDATE",array($bekleyenVarMi[0]->id));

            $robotUpdate=DB::update("UPDATE robot SET mesgul=1 , sonDegisiklikYapan=? WHERE id=?",array("RobotAPi",$robot[0]->id));
            $istekUpdate=DB::update("UPDATE
                                        istek
                                    SET
                                        robotAldi=1 ,
                                        durum=1 ,
                                        robotId=? ,
                                        almaZamani=? ,
                                        sonDegisiklikYapan=?,
                                        ozelfiyatId=?
                                    WHERE id=? AND
                                        robotAldi=0 AND
                                        robotDondu=0 AND
                                        durum=0 AND
                                        robotId=1",
                                        array($robot[0]->id , $now , $robotAdi ,$bekleyenVarMi[0]->ozelfiyatId , $bekleyenVarMi[0]->id));
            if($istekUpdate!=1 || $robotUpdate!=1)
            {
                DB::rollBack();
                Log::info("YUKLEYİCİ-Robot:$robotAdi updated row sayilari yanlis istek:$istekUpdate robotupdate:$robotUpdate");
                return array();
            }
            DB::commit();
            Log::info("YUKLEYİCİ-Robot:$robotAdi Kayit Cekme Basarili tel:".$bekleyenVarMi[0]->tel);
            return $bekleyenVarMi;
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            Log::info("YUKLEYİCİ-Robot:$robotAdi Hata Olustu hata:".$e->getMessage());
            return array();
        }
    }


}
?>
