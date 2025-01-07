<?php 
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Models\Kullanicihesaphareketleri  ;
use App\Models\Robothesaphareketleri  ;
use Illuminate\Support\Facades\Hash;

class HesapIslemleri
{
    //ucretIadeSorgu
    public function KullaniciSorguIade($kullanici,$tel,$islemYapan,$aciklama=null)
    {
        try
        {
            $kullaniciHareketleri=new Kullanicihesaphareketleri;
            $kullaniciHareketleri->kullaniciId=$kullanici[0]->id;
            $kullaniciHareketleri->oncekiBakiye=$kullanici[0]->bakiye;
            $kullaniciHareketleri->sonrakiBakiye=$kullanici[0]->bakiye+$kullanici[0]->sorguUcret;
            $kullaniciHareketleri->paket="(".$tel.") "."Sorgu";
            $kullaniciHareketleri->aciklama=$aciklama;
            $kullaniciHareketleri->tarih=date('Y-m-d H:i:s', time());
            $kullaniciHareketleri->islemTuruId=4;
            $kullaniciHareketleri->sonDegisiklikYapan=$islemYapan;
            $kullaniciHareketleri->save();
            $updateBakiye=DB::update("UPDATE kullanici SET bakiye=bakiye+? WHERE id=?  AND sorguUcret=?",
            array($kullanici[0]->sorguUcret,$kullanici[0]->id,$kullanici[0]->sorguUcret));  
            
            if($kullanici[0]->sorguUcret==0)
                return true;

            if($updateBakiye==1)
                return true;
            else
                return false;
        }
        catch(\Exception $e)
        {
            Log::info("ucretIadeSorgu kulid:".$kullanici[0]->id." tel:".$tel." HATA:$e");
            return false;
        }
        
    }
    //ucretIadePaket
    public function KullaniciPaketIade($kullanici,$tel,$tutar,$paketAdi,$islemYapan,$aciklama=null)
    {
        try
        {      
            $kullaniciHareketleri=new Kullanicihesaphareketleri;
            $kullaniciHareketleri->kullaniciId=$kullanici[0]->id;
            $kullaniciHareketleri->oncekiBakiye=$kullanici[0]->bakiye;
            $kullaniciHareketleri->sonrakiBakiye=$kullanici[0]->bakiye+$tutar;
            $kullaniciHareketleri->paket="(".$tel.") ".$paketAdi;
            $kullaniciHareketleri->aciklama=$aciklama;
            $kullaniciHareketleri->tarih=date('Y-m-d H:i:s', time());
            $kullaniciHareketleri->islemTuruId=4;
            $kullaniciHareketleri->sonDegisiklikYapan=$islemYapan;
            $kullaniciHareketleri->save();
            
            $maliyetFiyati=$tutar;
            //$yeniBakiye=$kullanici[0]->bakiye+$maliyetFiyati;
            
            // KULLANICI BAKIYE KAYDI DURDURULDU
                //$updateBakiye=DB::update("UPDATE kullanici SET bakiye=bakiye+? WHERE id=?  AND sorguUcret=?",
                //array($maliyetFiyati,$kullanici[0]->id,$kullanici[0]->sorguUcret));
            $updateBakiye = 1;
            // --- 11.10.2019 ---
            
            if($maliyetFiyati==0)
                return true;

            if($updateBakiye==1)
                return true;
            else
                return false;
        }
        catch(\Exception $e)
        {
            Log::info("ucretIadePaket kulid:".$kullanici[0]->id." tel:".$tel." HATA:$e");
            return false;
        }
    }
    //ucretIadeFatura
    public function KullaniciFaturaIade($kullanici,$tel,$tutar,$faturaNo,$islemYapan,$aciklama=null)
    {
        try
        {
            $kullaniciHareketleri=new Kullanicihesaphareketleri;
            $kullaniciHareketleri->kullaniciId=$kullanici[0]->id;
            $kullaniciHareketleri->oncekiBakiye=$kullanici[0]->bakiye;
            $kullaniciHareketleri->sonrakiBakiye=$kullanici[0]->bakiye+$tutar;
            $kullaniciHareketleri->paket="(".$tel.") "."f.No:".$faturaNo;
            $kullaniciHareketleri->aciklama=$aciklama;
            $kullaniciHareketleri->tarih=date('Y-m-d H:i:s', time());
            $kullaniciHareketleri->islemTuruId=6;
            $kullaniciHareketleri->sonDegisiklikYapan=$islemYapan;
            $kullaniciHareketleri->save();
            
            // KULLANICI BAKIYE KAYDI DURDURULDU
                //$updateBakiye=DB::update("UPDATE kullanici SET bakiye=bakiye+? WHERE id=? AND bakiye=? AND sorguUcret=?",
                //array($tutar,$kullanici[0]->id,$kullanici[0]->bakiye,$kullanici[0]->sorguUcret));  
            $updateBakiye = 1;
            // --- 11.10.2019 ---
            
            if($tutar==0)
                return true;

            if($updateBakiye==1)
                return true;
            else
                return false;
        }
        catch(\Exception $e)
        {
            Log::info("ucretIadeFatura kulid:".$kullanici[0]->id." tel:".$tel." HATA:$e");
            return false;
        }
    }
    public function KullaniciPaketDus($kullanici,$tel,$tutar,$paketAdi,$islemYapan,$aciklama=null,$denemeSayisiDeadlock)
    {
        try 
        {
            // kapandı 1 satır
            //DB::beginTransaction();
            if ($denemeSayisiDeadlock == 0)
            {
                // KULLANICI BAKIYE KAYDI DURDURULDU
                    //$kullaniciUp= DB::update("UPDATE kullanici SET bakiye=bakiye-?  WHERE id=?",array($tutar,$kullanici[0]->id));//paket
                $kullaniciUp = 1;
                // --- 11.10.2019 ---
            }

            if($tutar==0)
            {
                // kapandı 1 satır
                //DB::commit(); 
                return true;
            }

            if($kullaniciUp==1)
            {
                $hareketler=new Kullanicihesaphareketleri;
                $hareketler->kullaniciId=$kullanici[0]->id;
                $hareketler->oncekiBakiye=$kullanici[0]->bakiye;
                $hareketler->sonrakiBakiye=($kullanici[0]->bakiye) - ($tutar);
                $hareketler->paket="(".$tel.") ".$paketAdi;
                $hareketler->islemTuruId=3;
                $hareketler->tarih=date('Y-m-d H:i:s', time());
                $hareketler->sonDegisiklikYapan=$islemYapan;
                $hareketler->aciklama=$aciklama;
                $hareketOK = $hareketler->save();
                if ($hareketOK)
                {
                    Log::info($tel." HAREKET KAYDI OK");
                }
                Log::info($tel." KULLANICI BAKİYE OK");
                return true;
            }
            else
            {
                Log::info($tel." Error -- HESAP PAKET KULLANICIUP -- " .$kullaniciUp." -- ".$aciklama);
                return false;
            }

            
                
        } 
        catch(\Exception $e) 
        {
            // kapandı 1 satır
            //DB::rollBack();
            $message="";
            if($e->getCode()==40001)
            {
                if ($denemeSayisiDeadlock <=3 )
                {
                    sleep(1);
                    $denemeSayisiDeadlock += 1;
                    return $this->KullaniciPaketDus($kullanici,$tel,$tutar,$paketAdi,$kullanici[0]->adi,"DEADLOCK REPEAT",$denemeSayisiDeadlock);
                }
                else
                {
                    Log::info($tel." 40001 -- PAKET -- DENEME SAYISI: ".$denemeSayisiDeadlock);
                    return false;
                }
            }
            else
            {
                Log::info($tel." Error -- KullaniciPaketDus -- $message");
                return false;
            }
        }
        
    }
    public function KullaniciFaturaDus($kullanici,$tel,$tutar,$faturaNo,$islemYapan,$aciklama=null)
    {
        try
        {
            $hareketler=new Kullanicihesaphareketleri;
            $hareketler->oncekiBakiye=$kullanici[0]->bakiye;
            $hareketler->sonrakiBakiye=($kullanici[0]->bakiye) - ($tutar);
            $hareketler->paket="(".$tel.")F:no ".$faturaNo;
            $hareketler->islemTuruId=5;
            $hareketler->kullaniciId=$kullanici[0]->id;
            $hareketler->tarih=date('Y-m-d H:i:s', time());
            $hareketler->sonDegisiklikYapan=$islemYapan;
            $hareketler->aciklama=$aciklama;
            $hareketler->save();
            $kullaniciUp= DB::update("UPDATE kullanici SET bakiye=bakiye-?  WHERE id=?",array($tutar,$kullanici[0]->id));//paket

            if($tutar==0)
            {
                DB::commit(); 
                return true;
            }

            if($kullaniciUp==1)
            {
                DB::commit();
                return true;
            }
            else
            {
                DB::rollBack();
                return false;
            }
            
        }
        catch(\Exception $e) 
        {
            DB::rollBack();
            $message="";
            if($e->getCode()==40001)
                return false;
            else
            {
                Log::info("Error -- KullaniciPaketDus -- $message");
                return false;
            }
        }
    }
    public function KullaniciSorguDus($kullanici,$tel,$islemYapan,$aciklama=null)
    {
        try
        {
            //DB::beginTransaction();
            $hareketler=new Kullanicihesaphareketleri;
            $hareketler->oncekiBakiye=$kullanici[0]->bakiye;
            $hareketler->sonrakiBakiye=$kullanici[0]->bakiye - $kullanici[0]->sorguUcret;
            $hareketler->paket="(".$tel.") "."Sorgu";
            $hareketler->islemTuruId=3;
            $hareketler->kullaniciId=$kullanici[0]->id;
            $hareketler->tarih=date('Y-m-d H:i:s', time());
            $hareketler->sonDegisiklikYapan=$islemYapan;
            $hareketler->aciklama=$aciklama;
            $hareketler->save();
            
            // KULLANICI BAKIYE KAYDI DURDURULDU
                //$kullaniciUp= DB::update("UPDATE kullanici SET bakiye=bakiye-sorguUcret  WHERE id=?",array($kullanici[0]->id));//sorgu
            $kullaniciUp = 1;
            // --- 11.10.2019 ---

            if($kullanici[0]->sorguUcret==0)
            {
                //DB::commit(); 
                return true;
            }

            if($kullaniciUp==1)
            {
                //DB::commit();
                return true;
            }
            else
            {
                //DB::rollBack();
                Log::info("Error -- Hesap İşlemleri -- FALSE ".$kullaniciUp);

                return false;
            }

        }
        catch(\Exception $e) 
        {
            //DB::rollBack();
            $message="";
            if($e->getCode()==40001)
            {
                Log::info("Error -- 40001 -- FALSE ".$e);
                return false;
            }
                
            else
            {
                Log::info("Error -- KullaniciSorguDus -- $message");
                return false;
            }
        }
        
        
    }
    public function RobotFaturaDus($robot,$tel,$tutar,$faturaNo,$islemYapan,$aciklama=null)
    {
        $robotHesap= new Robothesaphareketleri;
        $robotHesap->islemTuruId=5;
        $robotHesap->robotId=$robot[0]->id;
        $robotHesap->aciklama=$aciklama;
        $robotHesap->paket="(".$tel.") f.No:".$faturaNo;
        $robotHesap->tarih=date('Y-m-d H:i:s', time());
        $robotHesap->oncekiBakiyeSistem=$robot[0]->sistemBakiye;
        $robotHesap->sonrakiBakiyeSistem=$robot[0]->sistemBakiye-$tutar;
        $robotHesap->posBakiye=$robot[0]->posBakiye;
        $robotHesap->sonDegisiklikYapan=$islemYapan;
        $robotHesap->save();
        $robotGuncelle=DB::update("UPDATE robot SET sistemBakiye=sistemBakiye-? WHERE id=?",array($tutar,$robot[0]->id));
        if($tutar==0)
            return true;

        if($robotGuncelle==1)
            return true;
        else
            return false;

        return true;
    }
    public function RobotPaketDus($robot,$tel,$tutar,$paketAdi,$islemYapan,$aciklama=null)
    {
        $robotHesap= new Robothesaphareketleri;
        $robotHesap->islemTuruId=3;
        $robotHesap->robotId=$robot[0]->id;
        $robotHesap->aciklama=$aciklama;
        $robotHesap->paket="(".$tel.") ".$paketAdi;
        $robotHesap->tarih=date('Y-m-d H:i:s', time());
        $robotHesap->oncekiBakiyeSistem=$robot[0]->sistemBakiye;
        $robotHesap->sonrakiBakiyeSistem=$robot[0]->sistemBakiye-$tutar;
        $robotHesap->posBakiye=$robot[0]->posBakiye;
            
        $robotHesap->sonDegisiklikYapan=$islemYapan;
        $robotHesap->save();
        
        $robotGuncelle=DB::update("UPDATE robot SET sistemBakiye=sistemBakiye-?  WHERE id=? AND sistemBakiye=?",array($tutar,$robot[0]->id,$robot[0]->sistemBakiye));
        if($tutar==0)
            return true;

        if($robotGuncelle==1)
            return true;
        else
            return false;

        return true;
    }
    public function RobotFaturaIade($robot,$tel,$tutar,$faturaNo,$islemYapan,$aciklama=null)
    {
        $robotHesap= new Robothesaphareketleri;
        $robotHesap->islemTuruId=6;
        $robotHesap->robotId=$robot[0]->id;
        $robotHesap->aciklama=$aciklama;
        $robotHesap->paket="(".$tel.") F.No:".$faturaNo;
        $robotHesap->tarih=date('Y-m-d H:i:s', time());
        $robotHesap->oncekiBakiyeSistem=$robot[0]->sistemBakiye;
        $robotHesap->sonrakiBakiyeSistem=$robot[0]->sistemBakiye+$tutar;
        $robotHesap->posBakiye=$robot[0]->posBakiye;
        $robotHesap->sonDegisiklikYapan=$islemYapan;
        $robotHesap->save();
        
        $robotGuncelle=DB::update("UPDATE robot SET sistemBakiye=sistemBakiye+?  WHERE id=? AND sistemBakiye=?",array($tutar,$robot[0]->id,$robot[0]->sistemBakiye));
        if($tutar==0)
            return true;

        if($robotGuncelle==1)
            return true;
        else
            return false;

        return true;
    }
    public function RobotPaketIade($robot,$tel,$tutar,$paketAdi,$islemYapan,$aciklama=null)
    {
        $robotHesap= new Robothesaphareketleri;
        $robotHesap->islemTuruId=4;
        $robotHesap->robotId=$robot[0]->id;
        $robotHesap->aciklama=$aciklama;
        $robotHesap->paket="(".$tel.") ".$paketAdi;
        $robotHesap->tarih=date('Y-m-d H:i:s', time());
        $robotHesap->oncekiBakiyeSistem=$robot[0]->sistemBakiye;
        $robotHesap->sonrakiBakiyeSistem=$robot[0]->sistemBakiye+$tutar;
        $robotHesap->posBakiye=$robot[0]->posBakiye;
        $robotHesap->sonDegisiklikYapan=$islemYapan;
        $robotHesap->save();
        $robotGuncelle=DB::update("UPDATE robot SET sistemBakiye=sistemBakiye+? WHERE id=?",array($tutar,$robot[0]->id));
        if($tutar==0)
            return true;

        if($robotGuncelle==1)
            return true;
        else
            return false;

        return true;
    }
    public function RobotPosBakiyeGuncelle($robotId,$posBakiye)
    {
        $robotGuncelle=DB::update("UPDATE robot SET  posBakiye=? WHERE id=?",array($posBakiye,$robotId));

        return true;
    }
}
?>