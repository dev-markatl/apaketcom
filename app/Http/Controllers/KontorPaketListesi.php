<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Paket ;
use App\Models\Ilce;
use App\Models\Kullanici;
use App\Models\Rol;
use App\Classes\SessionManager;
use App\Classes\DdTools;
use App\Models\Ozelfiyat;
use App\Models\Fiyatgrup;

class KontorPaketListesi 
{
    public function PaketUpdate(Request $request)
    {
        try
        {
            $id=$request->id;
            $resmiSatis=$request->resmiSatisFiyati;
            $maliyetFiyati=$request->maliyetFiyati;
            
            $mevcutPaket=Paket::where("id",$id)->first();
           



            $mevcutPaket=DB::select("SELECT * FROM paket WHERE sistemPaketKodu=?  ORDER BY id DESC LIMIT 1 ",array($mevcutPaket->sistemPaketKodu));

            $mevcutPaket=$mevcutPaket[0];
            DB::beginTransaction();
            $silme=DB::update("UPDATE paket set silindi=1  WHERE sistemPaketKodu=?",array($mevcutPaket->sistemPaketKodu));
            $yeniPaket=new Paket;
            //$yeniPaket->silindi=$mevcutPaket->silindi;
            $yeniPaket->aktif=$mevcutPaket->aktif;
            $yeniPaket->kod=$mevcutPaket->kod;
            $yeniPaket->operatorId=$mevcutPaket->operatorId;
            $yeniPaket->tipId=$mevcutPaket->tipId;
            $yeniPaket->maliyetFiyati=$mevcutPaket->maliyetFiyati;
            $yeniPaket->resmiSatisFiyati=$mevcutPaket->resmiSatisFiyati;
            $yeniPaket->sistemPaketKodu=$mevcutPaket->sistemPaketKodu;
            $yeniPaket->sonDegisiklikYapan=Auth::user()->takmaAd;
            $yeniPaket->sorguyaEkle=$mevcutPaket->sorguyaEkle;
            $yeniPaket->gun=$mevcutPaket->gun;
            $yeniPaket->herYoneKonusma=$mevcutPaket->herYoneKonusma;
            $yeniPaket->sebekeIciKonusma=$mevcutPaket->sebekeIciKonusma;
            $yeniPaket->herYoneSms=$mevcutPaket->herYoneSms;
            $yeniPaket->sebekeIciSms=$mevcutPaket->sebekeIciSms;
            $yeniPaket->internet=$mevcutPaket->internet;
            $yeniPaket->sistemPaketKodu=$mevcutPaket->sistemPaketKodu;
            $yeniPaket->adi=$mevcutPaket->adi;
            $yeniPaket->kategoriNo=$mevcutPaket->kategoriNo;
            $yeniPaket->kategoriAdi=$mevcutPaket->kategoriAdi;
            $yeniPaket->siraNo=$mevcutPaket->siraNo;
            $yeniPaket->alternatifKodlar = $mevcutPaket->alternatifKodlar;

            if($maliyetFiyati==null ||$resmiSatis==null)
            {
                return response()->json([
                    "status"=>"false",
                    "message"=>"Lütfen nokta yerine virgül kullanınız!"
                    
                ]);
            }
            $maliyetFiyati=str_replace(",",".",$maliyetFiyati);
            $yeniPaket->maliyetFiyati=$maliyetFiyati;
        
            $resmiSatis=str_replace(",",".",$resmiSatis);
            $yeniPaket->resmiSatisFiyati=$resmiSatis;
            
                
        
            $yeniPaket->save();
           // $update=DB::update("UPDATE paket SET silindi=1 , aktif=0 WHERE id=?",array($mevcutPaket->id));
            //mf: $maliyetFiyati  rf:$resmiSatis  id:$mevcutPaket->id

            // ANA PAKET LISTESINDE GUNCELLEME YAPILDIKTAN SONRA OZEL FIYAT LISTESINDE ID KARSILIGI BULUNAMIYORDU
            // OZEL FIYAT LISTESINDE BULUNAN PAKET ID YENI KAYIT ID ILE GUNCELLENECEK
            $yeniPaketId = $yeniPaket->id;
            
            //$ozelFiyatUpdate = Ozelfiyat::where("paket_id",$mevcutPaket->id)->update(['paket_id' => $yeniPaketId]);
            // --- 08.10.2019

            
            $paketOzelFiyat = Ozelfiyat::where("paket_id",$mevcutPaket->id)->get();

            foreach ($paketOzelFiyat as $paketOzelF)
            {
                $paketSil = DB::update("UPDATE ozelfiyat SET aktif = 0, silindi=1 WHERE sistem_paket_kod=?",array($paketOzelF->sistem_paket_kod));

                $yeniPaket = new Ozelfiyat;
                $yeniPaket->fiyatgrup_id = $paketOzelF->fiyatgrup_id;
                $yeniPaket->sistem_paket_kod = $paketOzelF->sistem_paket_kod;
                $yeniPaket->paket_id = $yeniPaketId;
                $yeniPaket->maliyet_fiyat = $paketOzelF->maliyet_fiyat;
                $yeniPaket->resmi_fiyat = $resmiSatis;
                $yeniPaket->aktif = $paketOzelF->aktif;
                $yeniPaket->sorguya_ekle = $paketOzelF->sorguya_ekle;
                $yeniPaket->aktif = 1;
                $yeniPaket->silindi = 0;
                $yeniPaket->save();

            }


            DB::commit();


            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı!" 
            
            ]);
        }catch(\Exception $e)
        {
            DB::rollBack();
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
            $alternatifKodlar = $request->alternatifKodlar;

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
            $yeniPaket->alternatifKodlar = $alternatifKodlar;

            $yeniPaket->save();
            
            $yeniPaketId = $yeniPaket->id;


            $paketOzelFiyat = Ozelfiyat::where("paket_id",$mevcutPaket->id)->get();


            foreach ($paketOzelFiyat as $paketOzelF)
            {
                $paketSil = DB::update("UPDATE ozelfiyat SET aktif = 0, silindi=1 WHERE sistem_paket_kod=?",array($paketOzelF->sistem_paket_kod));

                $yeniPaket = new Ozelfiyat;
                $yeniPaket->fiyatgrup_id = $paketOzelF->fiyatgrup_id;
                $yeniPaket->sistem_paket_kod = $paketOzelF->sistem_paket_kod;
                $yeniPaket->paket_id = $yeniPaketId;
                $yeniPaket->maliyet_fiyat = $paketOzelF->maliyet_fiyat;
                $yeniPaket->resmi_fiyat = $resmi;
                $yeniPaket->aktif = $paketOzelF->aktif;
                $yeniPaket->sorguya_ekle = $paketOzelF->sorguya_ekle;
                $yeniPaket->aktif = 1;
                $yeniPaket->silindi = 0;
                $yeniPaket->save();

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
                "alternatifKodlar"=>$paket->alternatifKodlar,
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
                "alternatifKodlar"=>"",
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
            $alternatifKodlar = $request->alternatifKodlar;
            
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
            $yeniPaket->alternatifKodlar=$alternatifKodlar;
            $yeniPaket->save();

            // YENİ EKLENEN PAKET OTOMATİK OLARAK FİYAT GRUPLARINA DA EKLENECEK
            $fiyatGruplari = Fiyatgrup::all();
            
            foreach ($fiyatGruplari as $fiyatGrup)
            {
                $yeniOzelPaket = new Ozelfiyat;
                $yeniOzelPaket->fiyatgrup_id = $fiyatGrup->id;
                $yeniOzelPaket->paket_id = $yeniPaket->id;
                $yeniOzelPaket->aktif = $yeniPaket->aktif;
                $yeniOzelPaket->sorguya_ekle = $yeniPaket->sorguyaEkle;
                $yeniOzelPaket->maliyet_fiyat = $yeniPaket->maliyetFiyati;
                $yeniOzelPaket->resmi_fiyat = $yeniPaket->resmiSatisFiyati;
                $yeniOzelPaket->sistem_paket_kod = Ozelfiyat::find(DB::table('ozelfiyat')->max('id'))->id+1;
                $yeniOzelPaket->silindi = 0;
                $yeniOzelPaket->save(); 

            }
           
            // --- 11.10.2019 ---

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
            {
                $paket->aktif=$aktif;
                DB::update("UPDATE ozelfiyat SET aktif=? WHERE paket_id=?",[$aktif,$id]);
            }
                
                
        
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
            $durumOzelFiyat = $request->Durum;
            
            
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
                
                /* ÖZEL FİYAT GÜNCELLEMESİ 2.1.2020*/
                if($durumOzelFiyat==2)
                {
                    $ozelUpdate = DB::update("UPDATE ozelfiyat SET aktif=0, silindi=1 WHERE paket_id=?",array($id));
                }
                /**/
                
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
        $manager->PageName="PaketListesi";
        $manager->Durum=-1;
        $manager->SetDataD();
        $manager->Tip=-1;
        $manager->SetDataT();
        $manager->Operator=-1;
        $manager->SetDataO();
        return redirect("kontor-paketlistesi");
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
       
       /*
       $sorgu      ="SELECT p.kategoriAdi,p.kategoriNo,p.aktif,p.maliyetFiyati,p.resmiSatisFiyati,p.sorguyaEkle,p.kod,p.adi,p.id,o.adi as operatorAdi,t.adi as tipAdi
       FROM operator o,tip t,paket p 
        WHERE p.operatorId=o.id AND  
                p.tipId=t.id AND
                p.silindi=0  AND
                p.yeni=0 ";*/
                
         $sorgu      ="SELECT p.kategoriAdi,p.kategoriNo,p.aktif,p.maliyetFiyati,p.resmiSatisFiyati,p.herYoneKonusma,p.internet,p.gun,p.siraNo,p.sorguyaEkle,p.kod,p.adi,p.id,o.adi as operatorAdi,t.adi as tipAdi,
   (SELECT CONCAT(istekcevap.created_at,'<br>', istek.tel) FROM istekcevap,istek where istekcevap.id = (SELECT MAX(id) FROM istekcevap where paketId = p.id ) and istekcevap.istekId = istek.id) as sonGorulme
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


