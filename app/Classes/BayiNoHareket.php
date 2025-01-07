<?php
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Models\Bayibilgi;
use App\Models\Bayihareket;
use Illuminate\Support\Facades\Hash;

class BayiNoHareket
{

    // bayi_id Hareket Kaydı
    public function bayiHareketKaydi($bayi_id,$takma_ad,$site_adres,$operator)
    {
        //try
        //{

          // bayibilgi tablosunda daha önce bayi_id kaydı yapılmıs mi?
          $bayiKaydiVarMi= DB::select("SELECT id FROM bayibilgi WHERE bayi_id=? AND takma_ad=? AND site_adres=? LIMIT 1",array($bayi_id,$takma_ad,$site_adres));

        //  dd($bayiKaydiVarMi);
          if(count($bayiKaydiVarMi)!=0)
          {
            $bayiHareketYaz = new Bayihareket;
            $bayiHareketYaz->bayi_id = $bayiKaydiVarMi[0]->id;
            $bayiHareketYaz->operator = $operator;
            $bayiHareketYaz->islem_tarih=date('Y-m-d H:i:s', time());
            $bayiHareketYaz->save();
          }
          else
          {
            $bayiBilgiYaz = new Bayibilgi;
            $bayiBilgiYaz->takma_ad = $takma_ad;
            $bayiBilgiYaz->site_adres = $site_adres;
            $bayiBilgiYaz->bayi_id = $bayi_id;
            $bayiBilgiYaz->bayi_ad = "TANIMLANMADI";
            $yeniBayiId = $bayiBilgiYaz->save();


            $bayiHareketYaz = new Bayihareket;
            $bayiHareketYaz->bayi_id = $bayiBilgiYaz->id;
            $bayiHareketYaz->operator = $operator;
            $bayiHareketYaz->islem_tarih=date('Y-m-d H:i:s', time());
            $bayiHareketYaz->save();

          }



            /*
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
            $updateBakiye=DB::update("UPDATE kullanici SET bakiye=bakiye+? WHERE id=?  AND sorguUcret=?",
            array($maliyetFiyati,$kullanici[0]->id,$kullanici[0]->sorguUcret));
            if($maliyetFiyati==0)
                return true;

            if($updateBakiye==1)
                return true;
            else
                return false;
            */


        //}
        //catch(\Exception $e)
        //{
            /*
            Log::info("ucretIadePaket kulid:".$kullanici[0]->id." tel:".$tel." HATA:$e");
            return false;
            */
        //}
    }
}
?>
