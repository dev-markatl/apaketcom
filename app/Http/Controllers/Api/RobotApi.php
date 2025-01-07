<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Istek ;
use App\Models\Robot;
use App\Models\Paket;
use App\Models\IstekCevap;
use App\Models\Tip;
use App\Models\Operator;
use App\Models\Genelayarlar;
use App\Classes\CommonFunctions;
use App\Classes\RobotFunctions;
use App\Classes\ApiRobotCevaplar;
use App\Classes\HesapIslemleri;
use Validator;
use Log;

class RobotApi
{
    public function PaketAyar(Request $request)
    {
        $packets="907,940,483,484,549,548";

        // $aa=DB::update("UPDATE genelayarlar SET olumsuzaTavsiyeDon =0 WHERE id>0 ");
        // echo $aa;
        // $paketUpdates=Paket::get();
        // foreach($paketUpdates as $paketUpdate)
        // {
        //     $paketUpdate->sistemPaketKodu=$paketUpdate->id;
        //     $paketUpdate->save();
        // }

    }
    public function getNumbers(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "robotName"     => "required|max:50|min:1",
        "password"      => "required|max:30|min:1"
        ));
        $cevaplar   = new ApiRobotCevaplar;

        if($kontrol->fails())
            $cevaplar->HataliBilgi();
        $cf         = new CommonFunctions();
        $rf         = new RobotFunctions();
        $robotAdi   = $request->input("robotName");
        $robotSifre = $request->input("password");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        Log::info("GELEN getNumbers-RobotAdi:$robotAdi sifre:$robotSifre");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        Log::info("getNumbers-Robot:$robotAdi Giriş Başarılı");

        if($robot[0]->yetkiSorgu==0 && $robot[0]->yetkiYukle==0 && $robot[0]->yetkiFatura==0)
        {
            Log::info("Robot:$robotAdi Yetki Yetersiz");
            return $cevaplar->YetkiYetersiz();
        }

        $bekleyenVarMi=$rf->BekleyenVarmi();
        //dd($bekleyenVarMi);
        $bekleyenVarMiFatura=$rf->BekleyenVarmiFatura();
        if(count($bekleyenVarMi)==0 && count($bekleyenVarMiFatura)==0)
        {
            Log::info("getNumbers-Robot API Sistemde  Hiç Bekleyen Kayıt yok");
            return $cevaplar->BekleyenKayitYok();
        }
        /////
        if($robot[0]->mesgul!=0)
        {
            $busyRobot=$rf->BusyRobot($robot);
            if(count($busyRobot)==0)
            {
                $busyRobotFatura=$rf->BusyRobotFatura($robot);
                if(count($busyRobotFatura)==0)
                {
                    DB::update("UPDATE robot SET mesgul=0 WHERE id=?",array($robot[0]->id));
                    Log::info("getNumbers-Robot:$robotAdi Mesgul Düzeltildi!");
                }
                else
                {
                    Log::info("getNumbers-Robot:$robotAdi Mesgul Geldi Fatura! tel:".$busyRobotFatura[0]->tel);
                    return $cevaplar->BekleyenVarFatura($busyRobotFatura,$robot);
                }

            }
            else
            {
                Log::info("getNumbers-Robot:$robotAdi Mesgul Geldi! tel:".$busyRobot[0]->tel);
                return $cevaplar->BekleyenVar($busyRobot,$robot);
            }
        }
        $bekleyenVarMiFatura=array();
        $bekleyenVarMi=$rf->YetkiKontrol($robot);
        if(!($bekleyenVarMi))
        {
            Log::info("getNumbers-Robot:$robotAdi yetkiKontorl Bekleyen YOK Kontor");
            if($robot[0]->yetkiFatura==0)
            {
                return $cevaplar->BekleyenKayitYok();
            }
            else
            {
                $bekleyenVarMiFatura=$cf->BekleyenVarmiFatura($robot[0]->kullaniciId);
                if(count($bekleyenVarMiFatura)==0)
                {
                    Log::info("getNumbers-Robot:$robotAdi yetkiKontorl Bekleyen YOK Fatura");
                    return $cevaplar->BekleyenKayitYok();
                }
            }

        }
        //ilgili id li Kayit icin select for update baslat
        if(count($bekleyenVarMiFatura)!=0)//fatura
        {
            try
            {
                $now=date('Y-m-d H:i:s', time());
                DB::beginTransaction();

                $KilitNoktasi=DB::select("SELECT * FROM istekfatura WHERE id=? AND robotAldi=0 AND robotDondu=0 AND robotId=1 LIMIT 1 for UPDATE",array($bekleyenVarMiFatura[0]->id));

                $robotUpdate=DB::update("UPDATE robot SET mesgul=1 , sonDegisiklikYapan=? WHERE id=?",array("RobotAPi",$robot[0]->id));
                $istekUpdate=DB::update("UPDATE
                                            istekfatura
                                        SET
                                            robotAldi=1 ,
                                            durum=1 ,
                                            robotId=? ,
                                            almaZamani=? ,
                                            sonDegisiklikYapan=?
                                        WHERE id=? AND
                                            robotAldi=0 AND
                                            robotDondu=0 AND
                                            durum=0 AND
                                            robotId=1",
                                            array($robot[0]->id , $now , $robotAdi , $bekleyenVarMiFatura[0]->id));
                if($istekUpdate!=1 || $robotUpdate!=1)
                {
                    DB::rollBack();
                    Log::info("getNumbers-Robot:$robotAdi Fatura-fail updated row sayilari yanlis istek:$istekUpdate robotupdate:$robotUpdate");
                    return $cevaplar->BekleyenKayitYok();
                }
                DB::commit();
                Log::info("getNumbers-Robot:$robotAdi Fatura-success Kayit Cekme Basarili tel:".$bekleyenVarMiFatura[0]->tel);
                return $cevaplar->BekleyenVarFatura($bekleyenVarMiFatura,$robot);
            }
            catch(\Exception $e)
            {
                DB::rollBack();
                Log::info("getNumbers-Robot:$robotAdi Fatura-fail Hata Olustu hata:".$e->getMessage());
                return $cevaplar->BekleyenKayitYok();
            }
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
                Log::info("getNumbers-Robot:$robotAdi updated row sayilari yanlis istek:$istekUpdate robotupdate:$robotUpdate");
                return $cevaplar->BekleyenKayitYok();
            }
            DB::commit();
            Log::info("getNumbers-Robot:$robotAdi Kayit Cekme Basarili tel:".$bekleyenVarMi[0]->tel);
            return $cevaplar->BekleyenVar($bekleyenVarMi,$robot);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            Log::info("getNumbers-Robot:$robotAdi Hata Olustu hata:".$e->getMessage());
            return $cevaplar->BekleyenKayitYok();
        }
    }
    //http://localhost/RobotikSorgu/public/api/Response?robotName=Robot1&password=123&id=2&response=11,5001&status=2
    public function cevapRobot(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "robotName"     => "required|max:50|min:1",
        "password"      => "required|max:30|min:1",
        "id"            => "required|max:30|min:1",
        "status"        => "required|max:2|min:1"
        ));
        $cevaplar   = new ApiRobotCevaplar;

        if($kontrol->fails())
            $cevaplar->HataliBilgi();

        $rf         = new RobotFunctions();
        $cf         = new CommonFunctions();
        $hesap      = new HesapIslemleri();
        $responses  = $request->input('response');
        $isBill     = $request->input('isBill');
        $id         = $request->input('id');
        $aciklama   = $request->input('aciklama');
        $status     = $request->input('status');
        $robotAdi   = $request->input("robotName");
        $robotSifre = $request->input("password");
        $bakiye     = $request->input("bakiye");
        $yukleyici  = $request->input("yukleyici");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        $now        = date('Y-m-d H:i:s', time());

        // ***************************
        // BAKİYE KAR MARJI DÜZELTMESİ
        // %2.5 ORAN
        
        // * IYILESTIRME 05.10.2019
        //$bakiye = str_replace(".","",$bakiye);
        
        
        /*
        if(is_numeric($bakiye))
        {
            $bakiye = $bakiye * 0.975;
        }
        else
        {
            $bakiye = 0;
        }
        */
        // İPTAL EDİLDİ 11.05.2019
        // *GERİ AÇILDI* 26.05.2019
        // ***************************

        if($responses=="5000")
            $responses=null;
        if($isBill==null)
            $isBill=0;

        Log::info("GELEN cevapRobot-RobotAdi:$robotAdi sifre:$robotSifre id:$id status:$status responses:$responses bakiye:$bakiye aciklama:$aciklama");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        Log::info("cevapRobot-Robot:$robotAdi Giriş Başarılı");
        if($bakiye==null)
            $bakiye=0;
        #region fatura
        if($isBill==1)//fatura
        {
            $istekFatura=DB::select("SELECT i.id ,
                                        i.tel ,
                                        i.tutar ,
                                        i.tutar as maliyetFiyati,
                                        i.sonOdemeTarihi ,
                                        i.aboneAdi ,
                                        i.kurumKodu ,
                                        i.faturaNo ,
                                        i.tesisatNo ,
                                        k.adi ,
                                        i.robotDondu ,
                                        i.robotAldi ,
                                        i.kullaniciId,
                                        i.olumsuzSayisi
                                    FROM
                                        istekfatura i ,
                                        kurum k
                                    WHERE
                                        i.kurumId=k.id AND
                                        i.id=? AND
                                        i.robotId=?
                                        LIMIT 1",array($id,$robot[0]->id));
            if(count($istekFatura)==0)
            {
                Log::info("cevapRobot-Robot:$robotAdi  id:$id YANLİS CEVAP");
                return $cevaplar->HataliCevap();
            }

            if($istekFatura[0]->robotDondu!=0)
            {
                Log::info("cevapRobot-Robot:$robotAdi Fatura-fail Hata id:$id Bu kayita Daha önce cevap verilmis");
                return $cevaplar->BasariliCevap();
            }

            try
            {
                $kullaniciFaturaIadeMi=1;
                $robotHareketi=1;
                $islemKullaniciFatura=1;
                $islemFaturaDurdur=1;

                if($status==3 )
                    $kullaniciFaturaIadeMi=1;
                else
                    $kullaniciFaturaIadeMi=0;

                $kullanici=DB::select("SELECT * FROM kullanici WHERE id=? LIMIT 1",array($robot[0]->kullaniciId));
                DB::beginTransaction();


                $dsf100 = strpos($aciklama,"-DS100-");

                $dsf101 = strpos($aciklama,"-DS101-");

                    
                if($dsf100 == true || $dsf101 == true)
                {
                    Log::info("cevapRobot-Robot:$robotAdi DURDURULDU *LİMİT BİTTİ*");
                    $islemFaturaDurdur=$rf->robotFaturaDurdur($robot[0]->id);
                }
      


                if($kullaniciFaturaIadeMi==1)
                    $islemKullaniciFatura=$hesap->KullaniciFaturaIade($kullanici,$istekFatura[0]->tel,$istekFatura[0]->tutar,$istekFatura[0]->faturaNo,$robot[0]->adi);


                if($status==2)
                    $robotHareketi=$hesap->RobotFaturaDus($robot,$istekFatura[0]->tel,$istekFatura[0]->tutar,$istekFatura[0]->faturaNo,$robot[0]->adi);


                // Fatura eslestirilemedi
                $fcp601 = strpos($aciklama,"-CP601-");
                // Odeme tipi dogrulanamadi
                $fcp500 = strpos($aciklama,"-CP500-");
                // Fatura listesi dogrulanamadi
                $fcp999 = strpos($aciklama,"-CP999-");
                // Turkcell ile iletisim kurulamadi
                $fcp998 = strpos($aciklama,"-CP998-");
                // Paket listesi doğrulanamadı
                $cp765 = strpos($aciklama,"-CP765-");

                    
                if($fcp601 == true)
                {
                    $ozelAciklama = "# SİSTEM OTOMATİK İŞLEME ALMIŞTIR # Ödenmek istenen fatura eşleştirilemedi.";
                }
                else if($fcp500 == true)
                {
                    $ozelAciklama = "# SİSTEM OTOMATİK İŞLEME ALMIŞTIR # Ödeme tipi doğrulanamadı.";
                }
                else if($fcp999 == true)
                {
                    $ozelAciklama = "# SİSTEM OTOMATİK İŞLEME ALMIŞTIR # Fatura listesi doğrulanamadı.";
                }
                else if($fcp998 == true)
                {
                    $ozelAciklama = "# SİSTEM OTOMATİK İŞLEME ALMIŞTIR # Turkcell ile iletişim kurulamadı.";
                }
                

                if(($fcp601 == true || $fcp500 == true || $fcp999 == true || $fcp998 == true || $cp765 == true ) && (!($istekFatura[0]->olumsuzSayisi >= 3)))
                {              
                    $islemIstek=DB::update("UPDATE istekfatura SET robotDondu=0,robotAldi=0,durum=0,robotId=1,olumsuzSayisi=?,aciklama=? WHERE id=? "
                    ,array($istekFatura[0]->olumsuzSayisi + 1,$ozelAciklama,$id));
                }
                else
                {
                    $islemIstek=DB::update("UPDATE istekfatura SET robotDondu=1  , durum=? , donmeZamani=? , sonDegisiklikYapan=? , aciklama=? WHERE id=? ",
                    array($status,$now,$robotAdi,$aciklama,$id));
                }


                if($islemIstek==1 && $islemKullaniciFatura==1 && $islemFaturaDurdur==1)
                {
                    DB::commit();
                    Log::info("cevapRobot-Robot:$robotAdi Basarili islem Sonucu");
                    return $cevaplar->BasariliCevap();
                }
                else
                {
                    DB::rollBack();
                    Log::info("cevapRobot-Robot:$robotAdi updated row sayilari yanlis islemIstek:$islemIstek islemKullaniciFatura:$islemKullaniciFatura ");
                    return $cevaplar->ErrorCevap("Hata Olustu");
                }
            }
            catch(\Exception $e)
            {
                return $cevaplar->ErrorCevap($e->getMessage());
            }

        }
        #endregion
        #region kontor
        // **************************************
        // Maliyet fiyatı için ek sorgu yazılacak.
        // Özel fiyat grubu bağlanacak.
        // 27.08.2019
        // **************************************
        $istek=DB::select("SELECT
                            i.id ,
                            i.ozelfiyatId,
                            p.kod ,
                            p.maliyetFiyati ,
                            p.resmiSatisFiyati ,
                            i.kullaniciId ,
                            i.tel ,
                            i.paketId ,
                            i.robotDondu ,
                            i.robotAldi ,
                            i.denemeSayisi,
                            i.olumsuzSayisi,
                            p.adi
                        FROM istek i ,
                             paket p
                        WHERE p.id=i.paketId AND
                              i.id=? AND
                              i.robotId=? AND
                              i.robotDondu=0
                        LIMIT 1",array($id,$robot[0]->id));



        if(count($istek)==0)
        {
            Log::info("cevapRobot-Robot:$robotAdi  id:$id YANLİS CEVAP");
            return $cevaplar->BasariliCevap();//return $cevaplar->HataliCevap();
        }
        
        $gelenPaketler=$cf->GelenPaketler($responses,$robot[0]->operatorId);

        $alternatifKodlar = "";

        foreach($gelenPaketler as $gelenPaket)
        {
            if($gelenPaket->alternatifKodlar != "")
            {
                $alternatifKodlar = $alternatifKodlar . $gelenPaket->alternatifKodlar.",";
            }
        }

        $responses = $alternatifKodlar . $responses;

        
        $gelenPaketler = $cf->GelenPaketler($responses,$robot[0]->operatorId);
        
        if($istek[0]->robotDondu!=0)
        {
            Log::info("cevapRobot-Robot:$robotAdi Hata id:$id Bu kayita Daha önce cevap verilmis");
            DB::update("UPDATE istek SET denemeSayisi=? WHERE id=?",array($istek[0]->denemeSayisi + 1 , $id));
            return $cevaplar->BasariliCevap();
        }
        //cf sorgu hazırla
        $kullaniciPaketIadeVarMi=$rf->kullaniciPaketIadeVarMi($istek[0]->kullaniciId,$istek[0]->kod,$status);
        $robotBakiyeGuncellesinMi=$rf->robotBakiyeGuncellesinMi($istek[0]->kod,$status);
        $islemIade=1;
        $islemHesap=1;
        $islemIstek=1;
        $islemDurdur=1;
        try
        {
            DB::beginTransaction();

            // ROBOT FIYAT GRUBU DEGISIKLIGI //
            if ($robot[0]->fiyatgrubuId != 0 && $istek[0]->ozelfiyatId != 0)
            {
                $ozelFiyat = DB::select("SELECT *
                FROM ozelfiyat
                WHERE id=? LIMIT 1",array($istek[0]->ozelfiyatId));

                if (count($ozelFiyat) != 0)
                {
                  $istek[0]->maliyetFiyati = $ozelFiyat[0]->maliyet_fiyat;
                  $istek[0]->resmiSatisFiyati = $ozelFiyat[0]->resmi_fiyat;
                }
            }
            // ***** ***** *****

            if(count($kullaniciPaketIadeVarMi)!=0)
                $islemIade=$hesap->KullaniciPaketIade($kullaniciPaketIadeVarMi,$istek[0]->tel,$istek[0]->maliyetFiyati,$istek[0]->adi,$robot[0]->adi);

            if($robotBakiyeGuncellesinMi)
            {
                // *******************************
                // BAKIYE KAYMA HATASI DÜZELTİLDİ
                // 03.05.2019
                // *******************************
                $RobotPosBakiyeGuncelle=$hesap->RobotPosBakiyeGuncelle($robot[0]->id,$bakiye);

                $robot      = $rf->GetRobot($robotAdi,$robotSifre); // Kayma hatası için eklendi

                $islemHesap=$hesap->RobotPaketDus($robot,$istek[0]->tel,$istek[0]->maliyetFiyati,$istek[0]->adi,$robot[0]->adi);

            }
            
            $robotDursunMu=$rf->robotDursunMu($robot[0]->id,$status);
            $robotLimitKontrol=$rf->robotAltLimitKontrol($robot[0]->id,$status);

            
            $ds100 = strpos($aciklama,"-DS100-");

            $ds101 = strpos($aciklama,"-DS101-");
            
            if($ds100 == true || $ds101 == true)
            {
                Log::info("cevapRobot-Robot:$robotAdi DURDURULDU *LİMİT BİTTİ*");
                $robotDursunMu = true;
            }

            if($robotDursunMu && $robot[0]->yetkiYukle==1)
            {
                Log::info("cevapRobot-Robot:$robotAdi DURDURULDU hatali islem sayısı 3 e ulastı");
                $islemDurdur=$rf->robotYuklemeDurdur($robot[0]->id);
            }

           
            //Sonuç alınamadı. (General Error Page) -CP100-
            $cp100 = strpos($aciklama,"-CP100-");
            
            // Satış tutarı eşleşmiyor.
            $cp101 = strpos($aciklama,"-CP101-");
            
            // Satış kontrol tekrar yükleme.
            $cp102 = strpos($aciklama,"-CP102-");
            
              $cp999 = strpos($aciklama,"-CP999-");
            
            if($cp100 == true)
            {
                $ozelAciklama = "# SİSTEM OTOMATİK İŞLEME ALMIŞTIR # Sonuç alınamadı. (General Error Page)";
            }
            else if($cp101 == true)
            {
                $ozelAciklama = "# SİSTEM OTOMATİK İŞLEME ALMIŞTIR # Satış tutarı eşleşmiyor.";
            }
            else if($cp102 == true)
            {
                $ozelAciklama = "# SİSTEM OTOMATİK İŞLEME ALMIŞTIR # Satış kontrolü sonucu tekrar işleme alınmıştır.";
            }
            else if($cp999 == true)
                {
                    $ozelAciklama = "# SİSTEM OTOMATİK İŞLEME ALMIŞTIR # Paket listesi doğrulanamadı.";
                }
                
                

            if(($cp100 == true || $cp101 == true || $cp102 == true || $cp999 == true) && (!($istek[0]->olumsuzSayisi >= 3)))
            {
                $islemIstek=DB::update("UPDATE istek SET robotDondu=0,robotAldi=0,durum=0,robotId=1,olumsuzSayisi=?,aciklama=? WHERE id=? "
                ,array($istek[0]->olumsuzSayisi + 1,$ozelAciklama,$id));
            }
            else if(($cp100 == true) && $istek[0]->olumsuzSayisi >= 3 && (5000 <= $istek[0]->kod && $istek[0]->kod <=6000))
            {
            
                $apiAciklama = "Sistemsel hata oluştu. Daha sonra tekrar deneyiniz. GNC001";
                
                $islemIstek=DB::update("UPDATE istek SET robotDondu=1,durum=6,aciklama=?, donmeZamani=? WHERE id=? "
                ,array($apiAciklama,$now,$id));
                   
            }
            
            else
            {
                $islemIstek=DB::update("UPDATE istek SET robotDondu=1 , cevap=? , durum=? , donmeZamani=? , sonDegisiklikYapan=? , aciklama=? WHERE id=? ",
                array($responses,$status,$now,$robotAdi,$aciklama,$id));
            }
            

            
            /* YUKLEYICIDE EXIPTAL MEKANIZMASI CALISMIYORDU. CALISACAK 11.10.2019 */
            // SADECE 3 DURUMUNDA ÇALIŞMAYACAK 14.10.2019
            /*
            if($yukleyici == 1 && $status == 3)
                $exIptalDevreDisi=DB::update("UPDATE istek SET exiptal=1 WHERE id=?",array($id));
            */
            
            
            // AVEA ÖZEL DURUM DÜZELTMESİ - EXIPTAL DEVRE DIŞI 
            if($status==6)
                $exIptalDevreDisi=DB::update("UPDATE istek SET exiptal=1 WHERE id=?",array($id));
            // ********* --------------- ************
        

            // ******* OLUMSUZ SORGU TEKRARI --
            $genelAyar=Genelayarlar::where("id","1")->first();

            if(count($gelenPaketler)!=0)
            {
                foreach($gelenPaketler as $gelen)
                {
                    DB::insert("INSERT INTO istekcevap (istekId , paketId , sonDegisiklikYapan ) VALUES (?,?,?)",array($istek[0]->id , $gelen->id , $robotAdi));
                }
            }
            else
           {
            if ( 5000 <= $istek[0]->kod && $istek[0]->kod <=6000)
            {
             if ($genelAyar->olumsuzSorguTekrar == 1)
             {

               if (!($istek[0]->olumsuzSayisi >= 1))
               {

               $islemIstek=DB::update("UPDATE istek SET robotDondu=0,robotAldi=0,durum=0,robotId=1,olumsuzSayisi=? WHERE id=? ",array($istek[0]->olumsuzSayisi + 1,$id));

               }

              }
            }

           }

            if($islemIstek==1 && $islemIade==1 && $islemHesap==1 && $islemDurdur==1)
            {
                DB::commit();
                Log::info("cevapRobot-Robot:$robotAdi Basarili islem Sonucu");
                return $cevaplar->BasariliCevap();
            }
            else
            {
                DB::rollBack();
                Log::info("cevapRobot-Robot:$robotAdi updated row sayilari yanlis islemIstek:$islemIstek islemIade:$islemIade islemHesap:$islemHesap");
                return $cevaplar->ErrorCevap("Hata Olustu");
            }
        }
        catch(\Exception $e)
        {
            return $cevaplar->ErrorCevap($e->getMessage());
        }
        #endregion

       //gelen cevap dogrumu kontrol et
       //dogru degil ise yanlis cevap de
       //dogru ise cevabı kaydet transaction kullan
       //robot hesap hareketleri guncellenecek
       //eger yukleme talebi ise  ve olumsuz(3-4) dönüldü ise iade edilecek
       //eger sorgu talebi ise ve Hatali(4) dönüldü ise iade edilecek
    }

    public function PacketList(Request $request)
    {
        //http://localhost/SorguProject/public/api/Packets?operator=Turkcell
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
       $cf         = new CommonFunctions();
       $packets    = $cf->GetAvaiblePackets($request->input("operator"));
       $arr2       = array();
       foreach($packets as $packet)
       {
         $result=array(
             "id"=>$packet->id,
             "name"=>$packet->adi,
             "code"=>$packet->kod,
             "type"=>$packet->tipAdi,
             "amount"=>$packet->resmiSatisFiyati,
             "kategoriAdi"=>$packet->kategoriAdi,
             "kategoriNo"=>$packet->kategoriNo,
             "siraNo"=>$packet->siraNo,
			 "dakika"=>$packet->dakika,
             "internet"=>$packet->internet,
             "fiyat"=>$packet->fiyat,
             "paketMaliyet"=>$packet->paketMaliyet,
             "gun"=>$packet->gun

         );
         array_push($arr2,$result);
       }
       $arr3=array("Results"=>$arr2);
       $finish =  json_encode($arr3,JSON_UNESCAPED_UNICODE);
       $finish = str_replace("\/","/",$finish);
       //sleep(1);
       return $finish;


    }

    public function NewPacket(Request $request)
    {
        //http://localhost/RobotikSorgu/public/api/NewPacket?robotName=Robot1&password=123123&packetName=deneme2243&operator=Turkcell&type=ses&fiyat=11.22&no=5325446303
        $kontrol = Validator::make($request->all(),array(
        "robotName"     => "required|max:50|min:1",
        "password"      => "required|max:30|min:1"
        ));
        $cevaplar   = new ApiRobotCevaplar;

        if($kontrol->fails())
            $cevaplar->HataliBilgi();

        $rf         = new RobotFunctions();
        $robotAdi   = $request->input("robotName");
        $robotSifre = $request->input("password");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        $fiyat      = $request->input("fiyat");
        Log::info("GELEN NewPacket-RobotAdi:$robotAdi sifre:$robotSifre");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        Log::info("NewPacket-Robot:$robotAdi Giriş Başarılı");

        try
        {
            if($fiyat==null)
                $fiyat=0;

            $packets = new Paket;
            $packets->yeni=1;
            $packets->adi=$request->input("packetName");
            $packets->kod=9999;
            $packets->tipId=Tip::where("adi",$request->input("type"))->first()->id;
            $packets->operatorId=Operator::where("adi",$request->input("operator"))->first()->id;
            $packets->sistemPaketKodu = Paket::find(DB::table('paket')->max('id'))->id+1;
            $packets->sonDegisiklikYapan=$request->input("robotName")." / ".$request->input("no");
            $packets->resmiSatisFiyati=$request->input("fiyat");

            $packets->save();
            return $cevaplar->BasariliCevap();

        }
        catch(\Exception $e)
        {
            $message=$e->getMessage();
            return $cevaplar->ErrorCevap($message);
        }

    }
    public function KayitBosalt(Request $request)
    {
        $cf = new CommonFunctions;
        $cf->RequestKayitBosalt($request->input("id"));
        return response()->json([
            "status"=> "true"
        ]);
    }


}
