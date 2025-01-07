<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Istek  ;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Classes\HesapIslemleri;
use App\Models\Robothesaphareketleri;
use Log;
use App\Classes\DropDown;
use DateTime;



class KontorYuklemeTakip
{
    public function Temizle(Request $request)
    {
        $manager = new SessionManager;
        $manager->PageName="YuklemeTakip";
        $manager->Operator=-1;
        $manager->Bayiler=-1;
        $manager->Tip=-1;
        $manager->Durum=0;
        $manager->Robotlar=-1;
        $manager->Tarih1=null;
        $manager->Tarih2=null;
        $manager->Tel=null;
        $manager->IslemTuru=-1;
        $manager->SetAllData();
        return redirect("kontor-yuklemetakip");
    }
    public function DurumGuncelle(Request $request)
    {
        $idler = $request->Cb;
        $durum = $request->Durum;
        $robot = $request->Robot;
        $gncKod = $request->GncKod;
        $aciklama = $request->Aciklama;
        $cf = new CommonFunctions;
        $hesap=new HesapIslemleri;


        foreach ($idler as $id )
        {
            $robotOzellikleri=DB::select("SELECT * FROM robot WHERE id=?",array($robot));
            $istek=DB::select("SELECT i.id,i.tel,i.ozelfiyatId,p.adi  ,p.maliyetFiyati as tutar  ,p.kod ,i.paketId , i.durum , i.robotId , i.kullaniciId
                                FROM istek i , paket p
                                WHERE i.paketId=p.id AND i.id=? LIMIT 1",array($id));
            $kullanici = DB::select("SELECT bakiye, id , sorguUcret,takmaAd  FROM kullanici WHERE id=?",array($istek[0]->kullaniciId));

            $tel=$istek[0]->tel;
            $adi=$istek[0]->adi;
            $tutar=$istek[0]->tutar;
            $takmaAd=Auth::user()->takmaAd;
            $kod=$istek[0]->kod;
            $istekDurum=$istek[0]->durum;
            //$aciklama="İslem Id:".$istek[0]->id.$request->Aciklama;
            // GENCAN TARAFINDA PROBLEM YAPIYOR 02.10.2019
            $aciklama="";
            $robotTutar = $tutar;
            $robotTutarYeni = $tutar;
            // FIYAT GRUBU DEGISIKLIGI //
            $ozelistekRobot=DB::select("SELECT * FROM robot WHERE id=?",array($istek[0]->robotId));

            if($ozelistekRobot[0]->fiyatgrubuId != 0 && $istek[0]->ozelfiyatId != 0)
            {
                $ozelFiyat = DB::select("SELECT *
                FROM ozelfiyat
                WHERE id=? LIMIT 1",array($istek[0]->ozelfiyatId));

                if (count($ozelFiyat) != 0)
                {
                  $robotTutar = $ozelFiyat[0]->maliyet_fiyat;
                }
            }
            else
            {
                $robotTutar = $tutar;
            }


            if(count($robotOzellikleri)!=0)
            {
              if($robotOzellikleri[0]->fiyatgrubuId != 0 && $istek[0]->ozelfiyatId != 0)
              {
                  $ozelFiyatYeni = DB::select("SELECT *
                  FROM ozelfiyat
                  WHERE id=? LIMIT 1",array($istek[0]->ozelfiyatId));

                  if (count($ozelFiyatYeni) != 0)
                  {
                    $robotTutarYeni = $ozelFiyatYeni[0]->maliyet_fiyat;
                  }
                  else {
                    $robotTutarYeni = $tutar;
                  }
              }
            }


            // ***** ***** *****

            Log::info("Manuel istekid:$id, durum:$durum ");

            switch ($durum) {
                case '0'://işleme al
                    if($istekDurum==3 || $istekDurum==5)
                    {
                        if(5000<=intval($kod) && intval($kod)<=6000)
                            $islem=$hesap->KullaniciSorguDus($kullanici,$tel,$takmaAd,$aciklama);
                        else
                            $islem=$hesap->KullaniciPaketDus($kullanici,$tel,$tutar,$adi,$takmaAd,$aciklama,0);
                    }
                    if($istekDurum==2)
                    {
                        $mevcutRobot=DB::select("SELECT * FROM robot WHERE id=?",array($istek[0]->robotId));
                        if(5000<=intval($kod) && intval($kod)<=6000)
                        $islem=true;
                        else
                        $islem=$hesap->RobotPaketIade($mevcutRobot,$tel,$robotTutar,$adi,$takmaAd,$aciklama);
                            //$islem=$hesap->RobotPaketIade($mevcutRobot,$tel,$tutar,$adi,$takmaAd,$aciklama);
                    }

                    $cf->RequestKayitBosalt($id);
                break;
                case '2'://onayla
                    //$istekOzellikleri=DB::select("SELECT robotId , durum FROM istek WHERE id=? LIMIT 1",array($id));
                    try
                    {
                        DB::beginTransaction();
                        $islem=true;
                        $islemR=true;
                        $istekUpdate=DB::update("UPDATE istek
                                    SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=?, aciklama=?
                                    WHERE id=? ",array(0,2,1,1,1,Auth::user()->takmaAd,$aciklama,$id));

                        if($istekDurum==3 || $istekDurum==5)
                        {
                            if(5000<=intval($kod) && intval($kod)<=6000)
                                $islem=$hesap->KullaniciSorguDus($kullanici,$tel,$takmaAd,$aciklama);
                            else
                                $islem=$hesap->KullaniciPaketDus($kullanici,$tel,$tutar,$adi,$takmaAd,$aciklama,0);
                        }
                        if($istekDurum==2)
                        {
                            $mevcutRobot=DB::select("SELECT * FROM robot WHERE id=?",array($istek[0]->robotId));
                            if(5000<=intval($kod) && intval($kod)<=6000)
                                $islemR=true;
                            else
                                $islemR=$hesap->RobotPaketIade($mevcutRobot,$tel,$robotTutar,$adi,$takmaAd,$aciklama);
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
                        $islem=true;
                        
                        // Manuel ve robot seçilmeden iptal edilen işlemlerde aşağıdaki hata kodu dönülecek.
                        // 21.01.2020

                        if($gncKod == "1")
                        {
                            $aciklama = "Lütfên dâhâ sơnra têkrâr dênêyînîz.<!--Girilen numara faturalı müşteridir veya farklı operatörün müşterisidir.--> GNC001";
                        }
                        else
                        {
                            $aciklama = "Lütfen daha sonra tekrar deneyiniz.";
                        }


                        $istekUpdate=DB::update("UPDATE istek
                        SET denemeSayisi=?, cevap=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? ,exIptal=?, aciklama=?
                        WHERE id=? ",array(0,"",6,1,1,1,Auth::user()->takmaAd,0,$aciklama,$id));
                        
                        /*
                        $istekUpdate=DB::update("UPDATE istek
                        SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? ,exIptal=?, aciklama=?
                        WHERE id=? ",array(0,3,1,1,1,Auth::user()->takmaAd,0,$aciklama,$id));
                        */

                        if($istekDurum==2 || $istekDurum==1 || $istekDurum==0 || $istekDurum==4)
                        {
                            if(5000<=intval($kod) && intval($kod)<=6000)
                                $islem=$hesap->KullaniciSorguIade($kullanici,$tel,$takmaAd,$aciklama);
                            else
                                $islem=$hesap->KullaniciPaketIade($kullanici,$tel,$tutar,$adi,$takmaAd,$aciklama);
                        }

                        if($istekDurum==2)
                        {
                            $mevcutRobot=DB::select("SELECT * FROM robot WHERE id=?",array($istek[0]->robotId));
                            if(5000<=intval($kod) && intval($kod)<=6000)
                                $islemR=true;
                            else
                                $islemR=$hesap->RobotPaketIade($mevcutRobot,$tel,$robotTutar,$adi,$takmaAd,$aciklama);
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
                        $istekUpdate=DB::update("UPDATE istek
                                SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=?, aciklama=?
                                WHERE id=? ",array(0,2,$robot,1,1,Auth::user()->takmaAd,$aciklama,$id));

                        if($istekDurum==3 || $istekDurum==5)
                        {
                            if(5000<=intval($kod) && intval($kod)<=6000)
                            {
                                $islemR=true;
                                $islemK=$hesap->KullaniciSorguDus($kullanici,$tel,$takmaAd,$aciklama);
                            }
                            else
                            {
                                $islemR=$hesap->RobotPaketDus($robotOzellikleri,$tel,$robotTutarYeni,$adi,$takmaAd,$aciklama);
                                $islemK=$hesap->KullaniciPaketDus($kullanici,$tel,$tutar,$adi,$takmaAd,$aciklama,0);
                            }

                        }
                        if($istekDurum==0 || $istekDurum==1 || $istekDurum==4)
                        {
                            if(5000<=intval($kod) && intval($kod)<=6000)
                                $islemR=true;
                            else
                                $islemR=$hesap->RobotPaketDus($robotOzellikleri,$tel,$robotTutarYeni,$adi,$takmaAd,$aciklama);


                        }
                        if($istekDurum==2 && ($robotOzellikleri[0]->id!=$mevcutRobot[0]->id))
                        {
                            if(5000<=intval($kod) && intval($kod)<=6000)
                            {
                                $islemR1=true;
                                $islemR=true;
                            }
                            else
                            {
                                $islemR1=$hesap->RobotPaketIade($mevcutRobot,$tel,$robotTutar,$adi,$takmaAd,$aciklama);
                                $islemR=$hesap->RobotPaketDus($robotOzellikleri,$tel,$robotTutarYeni,$adi,$takmaAd,$aciklama);
                            }


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
                        $istekUpdate=DB::update("UPDATE istek
                                SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=?, aciklama=?
                                WHERE id=? ",array(0,3,$robot,1,1,Auth::user()->takmaAd,$aciklama,$id));

                        if($istekDurum==2 || $istekDurum==1 || $istekDurum==0 || $istekDurum==4)
                        {
                            if(5000<=intval($kod) && intval($kod)<=6000)
                                $islem=$hesap->KullaniciSorguIade($kullanici,$tel,$takmaAd,$aciklama);
                            else
                                $islem=$hesap->KullaniciPaketIade($kullanici,$tel,$tutar,$adi,$takmaAd,$aciklama);

                            if($istekDurum==2)
                            {
                                if(5000<=intval($kod) && intval($kod)<=6000)
                                    $islemR=true;
                                else
                                    $islemR=$hesap->RobotPaketIade($mevcutRobot,$tel,$robotTutar,$adi,$takmaAd,$aciklama);
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
        $operator   = $request->operator;
        $tip        = $request->tip;
        $durum     = $request->durum;
        $session    = $request->session;
        $manager    = new SessionManager;
        $cf         = new CommonFunctions;
        $tar1       = $request->tarih1;
        $tar2       = $request->tarih2;
        $bayi       = $request->bayiler;
        $robotId    = $request->robotlar;
        $tel        = $request->tel;
        $dd         = new DropDown ;
        $tarihHata  = $cf->TarihSiniri($tar1,$tar2);
        $manager->PageName="YuklemeTakip";




        if($session==null || !$tarihHata)
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


        $filtreler  ="&operator=$operator&tip=$tip&durum=$durum&session=$session&tarih1=$tar1&tarih2=$tar2&bayiler=$bayi&robotlar=$robotId&tel=$tel";

        $sayfadaGosterilecekKayitSayisi=20;
        $suankiSayfa=$request->sayfa;
        if($suankiSayfa==null)
            $suankiSayfa=1;



        $sorguArr=array();
        $sorguFiltre=" ";
        $satisSorgu="
                SELECT
                    SUM(p.maliyetFiyati) as toplamTutar
                FROM
                    istek i ,
                    kullanici k,
                    paket p,
                    operator o,
                    tip t,
                    robot r
                WHERE
                    i.kullaniciId=k.id AND
                    i.paketId = p.id AND
                    p.operatorId=o.id AND
                    p.tipId=t.id AND
                    i.robotId=r.id AND
                    i.durum != 3 AND !(p.kod BETWEEN 4999 AND 6001)

                ";
        $sorgu="
                SELECT
                    i.aciklama,
                    i.id,
                    i.tel,
                    r.adi as robotAdi,
                    p.adi as paketAdi,
                    p.kod as paketKodu,
                    p.maliyetFiyati as tutar,
                    k.firmaAdi ,
                    i.durum,
                    i.cevap,
                    i.created_at,
                    i.robotAldi,
                    i.robotDondu,
                    i.almaZamani,
                    i.donmeZamani,
                    i.denemeSayisi,
                    i.exIptal,
                    o.adi as operatorAdi,
                    t.adi as tipAdi,
                    k.ad as kullaniciAdi
                FROM
                    istek i ,
                    kullanici k,
                    paket p,
                    operator o,
                    tip t,
                    robot r
                WHERE
                    i.kullaniciId=k.id AND
                    i.paketId = p.id AND
                    p.operatorId=o.id AND
                    p.tipId=t.id AND
                    i.robotId=r.id
                ";

        if($tar2!=null)
        {
            $sorguFiltre=$sorguFiltre."AND i.created_at <= ? + INTERVAL 1 DAY ";
            array_push($sorguArr,$tar2);
        }
        if($operator!=null && $operator!=-1)
        {
            $sorguFiltre=$sorguFiltre."AND o.id=? ";
            array_push($sorguArr,$operator);
        }
        if($tip!=null && $tip!=-1)
        {
            $sorguFiltre=$sorguFiltre."AND t.id=? ";
            array_push($sorguArr,$tip);
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
                $sorguFiltre=$sorguFiltre."AND ( i.durum=? OR i.durum=? OR i.durum=? OR i.durum=?) ";
                array_push($sorguArr,3);//iptal
                array_push($sorguArr,5);//kesin iptal
                array_push($sorguArr,6);//problemli iptaller
                array_push($sorguArr,7);//problemli iptaller
            }
            if($durum!=3 && $durum!=0)//diger islemler icin
            {
                $sorguFiltre=$sorguFiltre."AND i.durum=? ";
                array_push($sorguArr,$durum);
            }

        }
        //daha sonra silinecek gereksizlesti
        // if($durum==1)
        // {
        //     $sorguFiltre=$sorguFiltre." AND i.robotId != ? ";
        //     array_push($sorguArr,1);
        // }
        if($tel!=null &&  $tel!="")
        {
            $sorguFiltre=" AND i.tel=? ";
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
        $manager->Tip=$tip;
        $manager->Operator=$operator;
        $manager->Bayiler=$bayi;
        $manager->Robotlar=$robotId;
        $manager->Tel=$tel;
        $manager->Tarih1=$tar1;
        $manager->Tarih2=$tar2;
        $manager->SetAllData();
        $satisToplam=DB::select($satisSorgu.$sorguFiltre,$sorguArr);
        $sorguFiltre=$sorguFiltre."Order By i.created_at DESC  ";

        $takipler=$cf->Paginate($sorgu.$sorguFiltre,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);
        //echo (new DateTime('now'))->format('d-m-Y H:i:s');

        $ddRobotlar=$dd->DdRobotlar();//performans için burda yapılıyor sorgu;
        $ddAktifRobotlar=DB::select("SELECT * FROM robot WHERE aktif=1 ORDER BY adi ASC");



        return view("kontor/YuklemeTakip",array("satisToplam"=>$satisToplam,
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
