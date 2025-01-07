<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Paket;
use App\Models\Ozelfiyat;
use App\Models\Fiyatgrup;
use App\Models\Ilce;
use App\Models\Kullanici;
use App\Models\Rol;
use App\Models\Robot;
use App\Classes\SessionManager;
use App\Classes\DdTools;


class KontorFiyatGruplari
{

    public function FiyatGruplari(Request $request)
    {
        try
        {
            $manager = new SessionManager;
            $manager->PageName="FiyatGruplari";
            $manager->SetAllData();

            $gruplar = Fiyatgrup::where("aktif",1)->get();

            foreach ($gruplar as $grup)
            {
                $kontrol = Robot::where("fiyatgrubuId",$grup->id)->first();
                if($kontrol)
                {
                    $grup->setAttribute('kullanimda',"1");
                }
                else
                {
                    $grup->setAttribute('kullanimda',"0");
                }

              

            }

        

            return view("/kontor/FiyatGruplari")->with('gruplar',$gruplar);
        }
        catch(\Exception $e)
        {

        }
    }

    public function GrupSil(Request $request)
    {
        try
        {
            $grupId = $request->input('id');

            $grupSil = Fiyatgrup::where("id",$grupId)->update(["aktif"=>"0"]);


            return response()->json([
                "status"=>"true",
                "message"=>"İşlem Başarılı!"
            ]);

        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İşlem Başarısız!"
            ]);
        }
    }

    public function OtoFiyatDuzenle(Request $request)
    {

      $fiyatCarpani = $request->fiyatCarpani;
      $grupNo = $request->grupNo;

      if ($fiyatCarpani == 0 || $fiyatCarpani < 0)
      {
        return response()->json([
            "status"=>"false",
            "message"=>"İşlem Başarısız! Fiyat çarpanı 0 veya negatif olamaz!"
        ]);
      }

      try
      {
        DB::beginTransaction();

        $otoFiyat = DB::update("UPDATE ozelfiyat SET maliyet_fiyat = maliyet_fiyat * $fiyatCarpani WHERE fiyatgrup_id=?",array($grupNo));

        DB::commit();
        return response()->json([
            "status"=>"true",
            "message"=>"İslem Başarılı!"

        ]);
      }
      catch(\Exception $e)
      {
          DB::rollBack();
          return response()->json([
              "status"=>"false",
              "message"=>"İslem Başarısız! (".$e->getMessage().")"
          ]);
      }


    }

    public function GrupEkle(Request $request)
    {
      return view("/kontor/YeniGrupEkle")->with("operator","");
    }

    public function GrupEklePOST(Request $request)
    {
      try
      {

          $grupAdi = $request->adi;
          $grupOperator = $request->operator;

          DB::beginTransaction();

          $yeniGrup = new Fiyatgrup;
          $yeniGrup->grup_ad = $grupAdi;
          $yeniGrup->operator_id = $grupOperator;
          $yeniGrup->aktif = 1;
          $yeniGrup->save();

          $transferPaketler = DB::select("SELECT * FROM paket WHERE operatorId=? AND silindi=0 AND aktif=1",array($grupOperator));

          foreach ($transferPaketler as $transferPaket)
          {
         

        

            $ozelFiyat = new Ozelfiyat;
            $ozelFiyat->fiyatgrup_id = $yeniGrup->id;
            $ozelFiyat->paket_id = $transferPaket->id;
            $ozelFiyat->aktif = $transferPaket->aktif;
            $ozelFiyat->sorguya_ekle = $transferPaket->sorguyaEkle;
            $ozelFiyat->maliyet_fiyat = $transferPaket->maliyetFiyati;
            $ozelFiyat->sistem_paket_kod = Ozelfiyat::find(DB::table('ozelfiyat')->max('id'))->id+1;
            $ozelFiyat->resmi_fiyat = $transferPaket->resmiSatisFiyati;
            $ozelFiyat->save();
          }


          DB::commit();
          return response()->json([
              "status"=>"true",
              "message"=>"İslem Başarılı!"

          ]);
      }
      catch(\Exception $e)
      {
          DB::rollBack();
          return response()->json([
              "status"=>"false",
              "message"=>"İslem Başarısız! (".$e->getMessage().")"

          ]);
      }
    }

    public function GrupDuzenle(Request $request)
    {

        $operator   = $request->operator;
        $tip        = $request->tip;
        $durum      = $request->durum;
        $session    = $request->session;
        $manager    = new SessionManager;
        $sorguArr   = array();


        $grupNo = $request->i;

        $fiyatGrubu = DB::select("SELECT * FROM fiyatgrup WHERE id=? LIMIT 1",array($grupNo));

        /*
        $paketler = DB::select("SELECT ozelfiyat.*,paket.*,ozelfiyat.aktif as ozelAktif,
            ozelfiyat.sorguya_ekle as ozelSorgu,ozelfiyat.id as ozelPaketNo,
            paket.sorguyaEkle as anaSorgu,paket.aktif as anaAktif,paket.adi as paketAdi,
            operator.adi as operatorAdi, tip.adi as tipAdi,
            (SELECT MAX(created_at) FROM istekcevap where paket.id=paketId ) as sonGorulme
            FROM ozelfiyat
            INNER JOIN paket ON ozelfiyat.paket_id = paket.id
            INNER JOIN operator ON paket.operatorId = operator.id
            INNER JOIN tip ON paket.tipId = tip.id
            WHERE ozelfiyat.fiyatgrup_id=? AND ozelfiyat.silindi=0 ORDER BY paket.tipId ASC, paket.kod ASC",array($grupNo));
            */

        $sorgu="SELECT ozelfiyat.*,paket.*,ozelfiyat.aktif as ozelAktif,
                ozelfiyat.sorguya_ekle as ozelSorgu,ozelfiyat.id as ozelPaketNo,
                paket.sorguyaEkle as anaSorgu,paket.aktif as anaAktif,paket.adi as paketAdi,
                operator.adi as operatorAdi, tip.adi as tipAdi,
                (SELECT MAX(created_at) FROM istekcevap where paket.id=paketId ) as sonGorulme
                FROM ozelfiyat
                INNER JOIN paket ON ozelfiyat.paket_id = paket.id
                INNER JOIN operator ON paket.operatorId = operator.id
                INNER JOIN tip ON paket.tipId = tip.id
                WHERE ozelfiyat.fiyatgrup_id=? AND ozelfiyat.silindi=0";

        $manager->PageName="OzelPaketListesi";
    
        if($session==null)
        {
            $manager->GetDataO();
            $manager->GetDataT();
            $manager->GetDataD();
            $operator=$manager->Operator;
            $tip=$manager->Tip;
            $durum=$manager->Durum;
            if($durum==null )
            {
                $durum=1;
            }
        }
       
        array_push($sorguArr,$grupNo);
       
        if($operator!=-1 && $operator!=null )
        {
            array_push($sorguArr,$operator);
            $sorgu=$sorgu." AND paket.operatorId=?";
        }
        if($tip!=-1 && $tip!=null)
        {
            array_push($sorguArr,$tip);
            $sorgu=$sorgu." AND tip.id=?";
        }
        if( $durum!=-1 && $durum!=null)
        {
            array_push($sorguArr,$durum);
            $sorgu=$sorgu." AND paket.aktif=?";
        }

        $manager->Durum=$durum;
        $manager->SetDataD();
        $manager->Tip=$tip;
        $manager->SetDataT();
        $manager->Operator=$operator;
        $manager->SetDataO();

        $paketler=DB::select($sorgu." ORDER BY paket.operatorId , paket.tipId ,paket.kod",$sorguArr);

        //dd($fiyatGrubu);

        return view("/kontor/OzelPaketListesi")->with('paketler',$paketler)->with('grupdetay',$fiyatGrubu);
    }

    public function UpdateAktif(Request $request)
    {
      try
      {
          $id = $request->id;

          $aktif = $request->aktif;

          $paket = Ozelfiyat::where("id",$id)->first();

          $paket->aktif = $aktif;

          $paket->silindi = 0;

          $paket->save();

          return response()->json([
              "status"=>"true",
              "message"=>"İslem Başarılı!"

          ]);
      }catch(\Exception $e)
      {
          return response()->json([
              "status"=>"false",
              "message"=>"İslem Başarısız! (".$e->getMessage().")"

          ]);
      }
    }

    public function UpdateSorgu(Request $request)
    {
      try
      {
          $id = $request->id;

          $sorguyaEkle = $request->sorguya_ekle;

          $paket = Ozelfiyat::where("id",$id)->first();

          $paket->sorguya_ekle = $sorguyaEkle;

          $paket->save();

          return response()->json([
              "status"=>"true",
              "message"=>"İslem Başarılı!"

          ]);
      }catch(\Exception $e)
      {
          return response()->json([
              "status"=>"false",
              "message"=>"İslem Başarısız! (".$e->getMessage().")"

          ]);
      }
    }

    public function UpdateFiyat(Request $request)
    {
      try
      {
          $id = $request->id;
          $resmiSatisFiyati = $request->resmiSatisFiyati;
          $maliyetFiyati = $request->maliyetFiyati;


          $paket = Ozelfiyat::where("id",$id)->first();

          $paketSil = DB::update("UPDATE ozelfiyat SET aktif = 0, silindi=1 WHERE sistem_paket_kod=?",array($paket->sistem_paket_kod));

          $yeniPaket = new Ozelfiyat;
          $yeniPaket->fiyatgrup_id = $paket->fiyatgrup_id;
          $yeniPaket->sistem_paket_kod = $paket->sistem_paket_kod;
          $yeniPaket->paket_id = $paket->paket_id;
          $yeniPaket->maliyet_fiyat = $maliyetFiyati;
          $yeniPaket->resmi_fiyat = $resmiSatisFiyati;
          $yeniPaket->aktif = $paket->aktif;
          $yeniPaket->sorguya_ekle = $paket->sorguya_ekle;
          $yeniPaket->aktif = 1;
          $yeniPaket->silindi = 0;
          $yeniPaket->save();

          return response()->json([
              "status"=>"true",
              "message"=>"İslem Başarılı!"

          ]);
      }
      catch(\Exception $e)
      {
          return response()->json([
              "status"=>"false",
              "message"=>"İslem Başarısız! (".$e->getMessage().")"
          ]);
      }

    }

    public function UpdateTopluDurum(Request $request)
    {
      try
      {
          $updatePaketler = $request->Cb;
          $yeniDurum = $request->Durum;

          foreach ($updatePaketler as $updatePaket)
          {
            if ($yeniDurum == 0) // Pasif
            {
              $islem = DB::update("UPDATE ozelfiyat SET aktif = 0 WHERE id=?",array($updatePaket));
            }
            else if($yeniDurum == 2) // Sil
            {
              $islem = DB::update("UPDATE ozelfiyat SET aktif = 0, silindi = 1 WHERE id=?",array($updatePaket));
            }
            else if($yeniDurum == 1) // Aktif
            {
              $islem = DB::update("UPDATE ozelfiyat SET aktif = 1, silindi = 0 WHERE id=?",array($updatePaket));
            }
          }
          return response()->json([
              "status"=>"true",
              "message"=>"İslem Başarılı!"

          ]);

      }
      catch(\Exception $e)
      {
          return response()->json([
              "status"=>"false",
              "message"=>"İslem Başarısız! (".$e->getMessage().")"

          ]);
      }

    }

    public function PaketOzellikleriGuncelle(Request $request)
    {

        try
        {

            $hys=$request->hys;
            $hyk=$request->hyk;
            $sik=$request->sik;
            $sis=$request->sis;
            $int=$request->int;
            $gun=$request->gun;
            $adi=$request->adi;
            $kodu=$request->kodu;
            $resmi=$request->rsf;
            $maliyet=$request->mf;
            $resmi=str_replace(",",".",$resmi);
            $maliyet=str_replace(",",".",$maliyet);
            $sorgu=$request->sorgu;
            $aktif=$request->aktif;
            $tip=$request->tip;
            $operator=$request->operator;
            $kategoriNo=$request->kategoriNo;
            $kategoriAdi=$request->kategoriAdi;
            $id=$request->id;
            $siraNo=$request->siraNo;
            $kontrol = DB::select("SELECT count(id) as toplam FROM paket WHERE tipId=? AND operatorId=? AND kod=? AND silindi=0",array($tip,$operator,$kodu));
            if($resmi==null ||$maliyet==null)
            {
                return response()->json([
                    "status"=>"false",
                    "message"=>"Lütfen nokta yerine virgül kullanınız!"

                ]);
            }
            if($kontrol[0]->toplam >1)
                return response()->json([
                    "status"=>"false",
                    "message"=>"İslem Başarısız! (Ayni Tip ,Operator ve Kodda 1 den fazla paket olamaz! )"

                ]);
            DB::beginTransaction();
            $mevcutPaket=Paket::where("id",$id)->first();
            DB::update("UPDATE paket SET silindi=1 Where sistemPaketKodu=?",array($mevcutPaket->sistemPaketKodu));
            $mevcutPaket->silindi=1;
            $mevcutPaket->aktif=0;
            $mevcutPaket->save();

            $yeniPaket= new Paket;
            $yeniPaket->adi=$adi;
            if($aktif=="true")
                $aktif=1;
            else
                $aktif=0;

            if($sorgu=="true")
                $sorgu=1;
            else
                $sorgu=0;

            $yeniPaket->aktif=$aktif;
            $yeniPaket->kod=$kodu;
            $yeniPaket->operatorId=$operator;
            $yeniPaket->tipId=$tip;
            $yeniPaket->maliyetFiyati=$maliyet;
            $yeniPaket->resmiSatisFiyati=$resmi;
            $yeniPaket->sistemPaketKodu=Paket::find(DB::table('paket')->max('id'))->id+1;
            $yeniPaket->sonDegisiklikYapan=Auth::user()->takmaAd;
            $yeniPaket->sorguyaEkle=$sorgu;
            $yeniPaket->gun=$gun;
            $yeniPaket->herYoneKonusma=$hyk;
            $yeniPaket->sebekeIciKonusma=$sik;
            $yeniPaket->herYoneSms=$hys;
            $yeniPaket->sebekeIciSms=$sis;
            $yeniPaket->internet=$int;
            $yeniPaket->kategoriNo=$kategoriNo;
            $yeniPaket->kategoriAdi=$kategoriAdi;
            $yeniPaket->sistemPaketKodu=$mevcutPaket->sistemPaketKodu;
            $yeniPaket->siraNo=$siraNo;
            $yeniPaket->save();

            DB::commit();
            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı!"

            ]);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"

            ]);
        }

    }
    public function PaketOzellikleri(Request $request)
    {

        $id=$request->id;
        if($id>0)//update icina cılan modal
        {
            $paket=Paket::where("id",$request->id)->first();

            return view("kontor/YeniPaketEkle",array(
                "id"=>$id,
                "gun"=>$paket->gun,
                "hys"=>$paket->herYoneSms,
                "hyk"=>$paket->herYoneKonusma,
                "int"=>$paket->internet,
                "sis"=>$paket->sebekeIciSms,
                "sik"=>$paket->sebekeIciKonusma,
                "operator"=>$paket->operatorId,
                "tip"=>$paket->tipId,
                "adi"=>$paket->adi,
                "kod"=>$paket->kod,
                "aktif"=>$paket->aktif,
                "sorgu"=>$paket->sorguyaEkle,
                "rsf"=>$paket->resmiSatisFiyati,
                "mf"=>$paket->maliyetFiyati,
                "update"=>true,
                "kategoriAdi"=>$paket->kategoriAdi,
                "kategoriNo"=>$paket->kategoriNo,
                "siraNo"=>$paket->siraNo,
                "status"=>"true",
                "message"=>"İslem Başarılı!"

            ));
        }
        else//insert icin acılan modal
        {
            return view("kontor/YeniPaketEkle",array(
                "id"=>"",
                "gun"=>"",
                "hys"=>"",
                "hyk"=>"",
                "int"=>"",
                "sis"=>"",
                "sik"=>"",
                "operator"=>"",
                "tip"=>"",
                "adi"=>"",
                "kod"=>"",
                "aktif"=>"",
                "sorgu"=>"",
                "rsf"=>"",
                "mf"=>"",
                "update"=>false,
                "kategoriAdi"=>"",
                "kategoriNo"=>"",
                "siraNo"=>"",
                "status"=>"",
                "message"=>"",

            ));
        }




    }
    public function YeniPaketEkle(Request $request)
    {

        try
        {
            $hys=$request->hys;
            $hyk=$request->hyk;
            $sik=$request->sik;
            $sis=$request->sis;
            $int=$request->int;
            $gun=$request->gun;
            $adi=$request->adi;
            $kodu=$request->kodu;
            $resmi=$request->rsf;
            $maliyet=$request->mf;
            $resmi=str_replace(",",".",$resmi);
            $maliyet=str_replace(",",".",$maliyet);
            $sorgu=$request->sorgu;
            $aktif=$request->aktif;
            $tip=$request->tip;
            $kategoriNo=$request->kategoriNo;
            $kategoriAdi=$request->kategoriAdi;
            $operator=$request->operator;
            $siraNo=$request->siraNo;

            if($aktif=="true")
                $aktif=1;
            else
                $aktif=0;

            if($sorgu=="true")
                $sorgu=1;
            else
                $sorgu=0;

            if($resmi==null ||$maliyet==null)
            {
                return response()->json([
                    "status"=>"false",
                    "message"=>"Lütfen nokta yerine virgül kullanınız!"

                ]);
            }
            $kontrol = DB::select("SELECT count(id) as toplam FROM paket WHERE tipId=? AND operatorId=? AND kod=? AND silindi=0",array($tip,$operator,$kodu));
            $toplam=$kontrol[0]->toplam;
            if($kontrol[0]->toplam >0)
                return response()->json([
                    "status"=>"false",
                    "message"=>"İslem Başarısız! (Ayni Tip ,Operator ve Kodda 1 den fazla paket olamaz! )"

                ]);
            $yeniPaket= new Paket;
            $yeniPaket->adi=$adi;
            $yeniPaket->aktif=$aktif;
            $yeniPaket->kod=$kodu;
            $yeniPaket->operatorId=$operator;
            $yeniPaket->tipId=$tip;
            $yeniPaket->maliyetFiyati=$maliyet;
            $yeniPaket->resmiSatisFiyati=$resmi;
            $yeniPaket->sistemPaketKodu=Paket::find(DB::table('paket')->max('id'))->id+1;
            $yeniPaket->sonDegisiklikYapan=Auth::user()->takmaAd;
            $yeniPaket->sorguyaEkle=$sorgu;
            $yeniPaket->gun=$gun;
            $yeniPaket->herYoneKonusma=$hyk;
            $yeniPaket->sebekeIciKonusma=$sik;
            $yeniPaket->herYoneSms=$hys;
            $yeniPaket->sebekeIciSms=$sis;
            $yeniPaket->internet=$int;
            $yeniPaket->kategoriNo=$kategoriNo;
            $yeniPaket->kategoriAdi=$kategoriAdi;
            $yeniPaket->siraNo=$siraNo;
            $yeniPaket->save();
            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı!"

            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"

            ]);
        }



    }
    public function UpdateProperty(Request $request)
    {
        try
        {
            $id=$request->id;
            $aktif=$request->aktif;
            $sorguyaEkle=$request->sorguyaEkle;

            $paket=Paket::where("id",$id)->first();
            if($aktif==null)
                $paket->sorguyaEkle=$sorguyaEkle;
            else
                $paket->aktif=$aktif;


            $paket->save();

            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı!"

            ]);
        }catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"

            ]);
        }
    }
    public function UpdateStatusAll(Request $request)
    {
        try
        {
            $idler = $request->Cb;
            $durum = $request->Durum;//sil eklencek
            $sorgu = "UPDATE paket SET aktif=? Where id IN(";
            if($durum==2)
            {
                $sorgu="UPDATE paket SET silindi=? Where id IN(";
                $durum=1;
            }

            foreach($idler as $id)
            {
                //toplu update sql i calıstırılabilir
                //UPDATE paket SET sorguyaEkle=1 Where id IN(1,2,3)

                $sorgu=$sorgu.$id.",";

            }
            $sorgu=substr($sorgu,0,strlen($sorgu)-1);
            $update=DB::update($sorgu." )",array($durum));

            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı!"

            ]);
        }catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"

            ]);
        }
    }
    public function Temizle(Request $request)
    {
        $manager    = new SessionManager;
        $manager->PageName="OzelPaketListesi";
        $manager->Durum=-1;
        $manager->SetDataD();
        $manager->Tip=-1;
        $manager->SetDataT();
        $manager->Operator=-1;
        $manager->SetDataO();
        return redirect("kontor-grupduzenle?i=".$request->i);
    }
    public function exiptal(Request $request)
    {
        try
        {
            $now = date("Y-m-d H:i:s");
            $sorgu=DB::update("UPDATE
                                    istek
                               SET
                                    exIptal=1
                               WHERE
                                    created_at >= DATE_SUB(?, INTERVAL 2 HOUR) AND
                                    robotAldi=1 AND
                                    robotDondu = 1 AND
                                    exIptal=0
                                     ",array($now));

            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı!"

            ]);
        }catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"

            ]);
        }
    }
    public function PaketListesi(Request $request)
    {

       $operator   = $request->operator;
       $tip        = $request->tip;
       $durum      = $request->durum;
       $session    = $request->session;
       $manager    = new SessionManager;
       $sorguArr   = array();
       $sorgu      ="SELECT p.kategoriAdi,p.kategoriNo,p.aktif,p.maliyetFiyati,p.resmiSatisFiyati,p.sorguyaEkle,p.kod,p.adi,p.id,o.adi as operatorAdi,t.adi as tipAdi ,
       (SELECT MAX(created_at) FROM istekcevap where p.id=paketId ) as sonGorulme
       FROM operator o,tip t,paket p
        WHERE p.operatorId=o.id AND
                p.tipId=t.id AND
                p.silindi=0  AND
                p.yeni=0 ";

       $manager->PageName="PaketListesi";

       if($session==null)
       {
           $manager->GetDataO();
           $manager->GetDataT();
           $manager->GetDataD();
           $operator=$manager->Operator;
           $tip=$manager->Tip;
           $durum=$manager->Durum;
           if($durum==null )
           {
               $durum=1;
           }
       }


       if($operator!=-1 && $operator!=null )
       {
        array_push($sorguArr,$operator);
        $sorgu=$sorgu." AND p.operatorId=?";
       }
       if($tip!=-1 && $tip!=null)
       {
        array_push($sorguArr,$tip);
        $sorgu=$sorgu." AND t.id=?";
       }
       if( $durum!=-1 && $durum!=null)
       {
        array_push($sorguArr,$durum);
        $sorgu=$sorgu." AND p.aktif=?";
       }
       $manager->Durum=$durum;
       $manager->SetDataD();
       $manager->Tip=$tip;
       $manager->SetDataT();
       $manager->Operator=$operator;
       $manager->SetDataO();

       $paketler=DB::select($sorgu." ORDER BY p.operatorId , p.tipId ,p.kod",$sorguArr);



       return view("/kontor/PaketListesi",array("paketler"=>$paketler));


    }


}
