<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Istekfatura  ;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Models\Robothesaphareketleri;
use App\Classes\HesapIslemleri;
use Log;
use DateTime;
use App\Classes\DropDown;



class FaturaTakip 
{
    public function Temizle(Request $request)
    {
        $manager = new SessionManager;
        $manager->PageName="FaturaTakip";
        $manager->Kurum=-1;
        $manager->Bayiler=-1;
        $manager->Tip=-1;
        $manager->Durum=0;
        $manager->Robotlar=-1;
        $manager->Tarih1=null;
        $manager->Tarih2=null;
        $manager->Tel=null;
        $manager->IslemTuru=-1;
        $manager->SetAllData();
        return redirect("fatura-faturatakip");
    }
    public function DurumGuncelle(Request $request)
    {
        $idler = $request->Cb;
        $durum = $request->Durum;
        $robot = $request->Robot;
        $aciklama = $request->Aciklama;
        $cf = new CommonFunctions;
        $hesap=new HesapIslemleri;
        
        
        foreach ($idler as $id ) 
        {
            
            Log::info("Manuel Fatura istekid:$id, durum:$durum ");
            $robotOzellikleri=DB::select("SELECT * FROM robot WHERE id=?",array($robot));
            $istek=DB::select("SELECT kullaniciId ,tutar , id, tel ,faturaNo ,aboneAdi , robotId , durum FROM istekfatura WHERE id=? LIMIT 1",array($id));

            $kullanici = DB::select("SELECT bakiye, id , sorguUcret,takmaAd  FROM kullanici WHERE id=?",array($istek[0]->kullaniciId));

            $tel=$istek[0]->tel;
            $aboneAdi=$istek[0]->aboneAdi;
            $tutar=$istek[0]->tutar;
            $takmaAd=Auth::user()->takmaAd;
            $faturaNo=$istek[0]->faturaNo;
            $istekDurum=$istek[0]->durum;
            $aciklama="İslem Id:".$istek[0]->id.$request->Aciklama;
            switch ($durum) {
                case '0'://işleme al   
                DB::beginTransaction();
                $islem=true;
                if($istekDurum==3)
                {
                    $islem=$hesap->KullaniciFaturaDus($kullanici,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                }
                if($istekDurum==2)
                {
                    $mevcutRobot=DB::select("SELECT * FROM robot WHERE id=?",array($istek[0]->robotId));
                    $islem=$hesap->RobotFaturaIade($mevcutRobot,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                }

                $istekUpdate=DB::update("UPDATE istekfatura 
                        SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                        WHERE id=? ",array(0,0,1,0,0,Auth::user()->takmaAd,$id));
                
                if(!$islem )
                {
                    DB::rollBack();
                    return response()->json([
                        "status"=> "false",
                        "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--"
                    ]);
                }
                DB::commit();

                break;
                case '2'://onayla
                    try
                    {
                        DB::beginTransaction();
                        $islem=true;
                        $islemR=true;
                        $istekUpdate=DB::update("UPDATE istekfatura 
                                    SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                                    WHERE id=? ",array(0,2,1,1,1,Auth::user()->takmaAd,$id));
                        if($istekDurum==3)
                        {
                            $islem=$hesap->KullaniciFaturaDus($kullanici,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                        }
                        if($istekDurum==2)
                        {
                            $mevcutRobot=DB::select("SELECT * FROM robot WHERE id=?",array($istek[0]->robotId));
                            $islemR=$hesap->RobotFaturaIade($mevcutRobot,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                        }

                        if(!$islem && !$islemR)
                        {
                            DB::rollBack();
                            return response()->json([
                                "status"=> "false",
                                "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--"
                            ]);
                        }
                        DB::commit();
                    }
                    catch(\Exception $e)
                    {
                        DB::rollBack();
                        return response()->json([
                            "status"=> "false",
                            "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--".$e->getMessage()
                        ]);
                    }
                break;
                case '3'://iptal et 
                   
                    try
                    {
                        DB::beginTransaction();
                        $istekUpdate=DB::update("UPDATE istekfatura 
                            SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                            WHERE id=? ",array(0,3,1,1,1,Auth::user()->takmaAd,$id));
                        $islem=true;
                        if($istekDurum==2 || $istekDurum==1 || $istekDurum==0 || $istekDurum==4)
                        {
                            $islem=$hesap->KullaniciFaturaIade($kullanici,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                        }

                        if(!$islem)
                        {
                            DB::rollBack();
                            return response()->json([
                                "status"=> "false",
                                "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--"
                            ]);
                        }
                        DB::commit();
                    }
                    catch(\Exception $e)
                    {
                        DB::rollBack();
                        return response()->json([
                            "status"=> "false",
                            "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--".$e->getMessage()
                        ]);
                    }
                   
                    //iade
                break;
                case '4'://robot sec onayla
                    if($robot==-1)
                    {
                        return response()->json([
                            "status"=> "false",
                            "message"=>"Lütfen Robot Seçiniz!"
                        ]);
                    }
                    $mevcutRobot=DB::select("SELECT * FROM robot WHERE id=?",array($istek[0]->robotId));

                    try
                    {
                        DB::beginTransaction();
                        $islemK=true;
                        $islemR=true;
                        $islemR1=true;
                     
                        $istekUpdate=DB::update("UPDATE istekfatura 
                                SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                                WHERE id=? ",array(0,2,$robot,1,1,Auth::user()->takmaAd,$id));
                       if($istekDurum==3)
                       {                    
                            $islemR=$hesap->RobotFaturaDus($robotOzellikleri,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                            $islemK=$hesap->KullaniciFaturaDus($kullanici,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                       }
                       if($istekDurum==0 || $istekDurum==1 || $istekDurum==4)
                       {                                       
                            $islemR=$hesap->RobotFaturaDus($robotOzellikleri,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                       }
                       if($istekDurum==2 && ($robotOzellikleri[0]->id!=$mevcutRobot[0]->id))
                       { 
                            $islemR1=$hesap->RobotFaturaIade($mevcutRobot,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                            $islemR=$hesap->RobotFaturaDus($robotOzellikleri,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                       }
                       if(!$islemR1 && !$islemR && !$islemK)
                       {
                           DB::rollBack();
                           return response()->json([
                               "status"=> "false",
                               "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--"
                           ]);
                       }
                       DB::commit();
                    }
                    catch(\Exception $e)
                    {
                        DB::rollBack();
                        return response()->json([
                            "status"=> "false",
                            "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--".$e->getMessage()
                        ]);
                    }
                    
                    //robot hesap hareketi
                break;
                case '5'://robot sec iptalet
                    if($robot==-1)
                    {
                        return response()->json([
                            "status"=> "false",
                            "message"=>"Lütfen Robot Seçiniz!"
                        ]);
                    }
                    $mevcutRobot=DB::select("SELECT * FROM robot WHERE id=?",array($istek[0]->robotId));
                    try
                    {
                        DB::beginTransaction();
                        $islem=true;
                        $islemR=true;
                      
                        $istekUpdate=DB::update("UPDATE istekfatura 
                            SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                            WHERE id=? ",array(0,3,$robot,1,1,Auth::user()->takmaAd,$id));
                        if($istekDurum==2 || $istekDurum==1 || $istekDurum==0 || $istekDurum==4)
                        {

                            $islem=$hesap->KullaniciFaturaIade($kullanici,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);

                            if($istekDurum==2)
                            {
                                $islemR=$hesap->RobotFaturaIade($mevcutRobot,$tel,$tutar,$faturaNo,$takmaAd,$aciklama);
                            }
                        }
                        
                        if(!$islem && !$islemR)
                        {
                            DB::rollBack();
                            return response()->json([
                                "status"=> "false",
                                "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--"
                            ]);
                        }
                        DB::commit();
                    }
                    catch(\Exception $e)
                    {
                        DB::rollBack();
                        return response()->json([
                            "status"=> "false",
                            "message"=>"İşlem Yarıda Kaldı lütfen Sayfayi yenileyip Tekrar Deneyin!--".$e->getMessage()
                        ]);
                    }
                    
                    //robot hesap hareketi
                break;
                default:
                
                    break;
            }
        }
        return response()->json([
            "status"=> "true",
            "message"=>"İşlem Başarılı!"
        ]);
    }
    public function GetData(Request $request)
    { 
        $kurum   = $request->kurum;
        $durum     = $request->durum;
        $session    = $request->session;
        $manager    = new SessionManager;
        $tar1       = $request->tarih1;
        $tar2       = $request->tarih2;
        $bayi       = $request->bayiler;
        $robotId    = $request->robotlar;
        $tel        = $request->tel;
        $dd         = new DropDown ;
        $cf= new CommonFunctions;
        $tarihHata  = $cf->TarihSiniri($tar1,$tar2);
        $manager->PageName="FaturaTakip";
        
        if($session==null)
        {
            $manager->GetAllData();
            $operator=$manager->Operator;
            $tip=$manager->Tip;
            $durum=$manager->Durum;
            $bayi=$manager->Bayiler;
            $tar1=$manager->Tarih1;
            $tar2=$manager->Tarih2;
            $robotId=$manager->Robotlar;
            $tel=$manager->Tel;
        }
        
        if($tar1==null)
            $tar1=date('Y-m-d', time());
        if($durum==null)
            $durum="0";


        $filtreler  ="&kurum=$kurum&durum=$durum&session=$session&tarih1=$tar1&tarih2=$tar2&bayiler=$bayi&robotlar=$robotId&tel=$tel";
        
        $sayfadaGosterilecekKayitSayisi=20;
        $suankiSayfa=$request->sayfa;
        if($suankiSayfa==null)
            $suankiSayfa=1;
        
        

        $sorguArr=array();
        $sorguFiltre=" ";
        $satisSorgu="
                SELECT
                    SUM(i.tutar) as toplamTutar
                    FROM 
                    istekfatura i ,
                    kullanici k,
                    kurum kur,
                    robot r
                WHERE
                    i.kullaniciId=k.id AND
                    kur.id=i.kurumId AND
                    i.robotId=r.id AND
                    i.durum!=3 
                ";
        $sorgu="
                SELECT
                    i.id,
                    r.adi as robotAdi,
                    k.firmaAdi ,
                    i.durum,
                    i.created_at,
                    i.robotAldi,
                    i.robotDondu,
                    i.almaZamani,
                    i.donmeZamani,
                    i.aboneAdi, 
                    i.aciklama,
                    i.faturaNo, 
                    i.tekilNumara, 
                    i.sonOdemeTarihi, 
                    i.tutar ,
                    kur.adi as kurumAdi,
                    i.tesisatNo as tel, 
                    k.ad as kullaniciAdi
                FROM 
                    istekfatura i ,
                    kullanici k,
                    kurum kur,
                    robot r
                WHERE
                    i.kullaniciId=k.id AND
                    kur.id=i.kurumId AND
                    i.robotId=r.id 
                ";
                
        if($tar2!=null)
        {
            $sorguFiltre=$sorguFiltre."AND i.created_at <= ? + INTERVAL 1 DAY ";
            array_push($sorguArr,$tar2);
        }
        if($kurum!=null && $kurum!=-1)
        {
            $sorguFiltre=$sorguFiltre."AND kur.id=? ";
            array_push($sorguArr,$kurum);
        }
      
        if($durum!=null && $durum!=-1)
        {
            if($durum==0)//bekleyen butonunda
            {
                $sorguFiltre=$sorguFiltre."AND ( i.durum=? OR i.durum=? OR i.durum=?  ) ";
                array_push($sorguArr,1);//robot aldı
                array_push($sorguArr,0);//robot almadı
                array_push($sorguArr,4);//sorunlu
      
            }
            if($durum==3)//iptal ve kesin iptaller icin
            {
                $sorguFiltre=$sorguFiltre."AND ( i.durum=? OR i.durum=? ) ";
                array_push($sorguArr,3);//iptal
                array_push($sorguArr,5);//kesin iptal
            }
            if($durum!=3 && $durum!=0)//diger islemler icin
            {
                $sorguFiltre=$sorguFiltre."AND i.durum=? ";
                array_push($sorguArr,$durum);
            }
            
        }
        if($durum==1)
        {
            $sorguFiltre=$sorguFiltre." AND i.robotId != ? ";
            array_push($sorguArr,1);
        }
        if($bayi!=null && $bayi!=-1)
        {
            $sorguFiltre=$sorguFiltre."AND k.id=? ";
            array_push($sorguArr,$bayi);
        }
        if($robotId!=null && $robotId!=-1)
        {
            $sorguFiltre=$sorguFiltre."AND r.id=? ";
            array_push($sorguArr,$robotId);
        }
        if($tel!=null &&  $tel!="")
        {
            $sorguFiltre=" AND i.tesisatNo=? ";
            $sorguArr=array($tel);
            // array_push($sorguArr,$tel);
            // $durum=null;
        }
        else
        {
            //telefon numarası girilmemisse tarih filtersi aktif
            $sorguFiltre=$sorguFiltre." AND i.created_at >= ? ";
            array_push($sorguArr,$tar1);
        }
        $count=$cf->GetCount($sorgu.$sorguFiltre,$sorguArr);
        

        
        $manager->Durum=$durum;
        $manager->Kurum=$kurum;
        $manager->Bayiler=$bayi;
        $manager->Robotlar=$robotId;
        $manager->Tel=$tel;
        $manager->Tarih1=$tar1;
        $manager->Tarih2=$tar2;
        $manager->SetAllData();
        $satisToplam=DB::select($satisSorgu.$sorguFiltre,$sorguArr);
        $sorguFiltre=$sorguFiltre."Order By i.created_at DESC ";
        
        $takipler=$cf->Paginate($sorgu.$sorguFiltre,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);
        //echo (new DateTime('now'))->format('d-m-Y H:i:s');
        $ddRobotlar=$dd->DdRobotlar();//performans için burda yapılıyor sorgu;
        $ddAktifRobotlar=DB::select("SELECT * FROM robot WHERE aktif=1");

        return view("fatura/FaturaTakip",array("satisToplam"=>$satisToplam,
                                                "takipler"=>$takipler,
                                                "tarihHata"=>$tarihHata,
                                                "tar1"=>$tar1,
                                                "tar2"=>$tar2,
                                                "ddRobotlar"=>$ddRobotlar,
                                                "ddAktifRobotlar"=>$ddAktifRobotlar,
                                                "filtreler"=>$filtreler,
                                                "suankiSayfa"=>$suankiSayfa,
                                                "sayfaSayisi"=>$cf->SayfaSayisi,
                                                "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi));
    }
  
   

}



