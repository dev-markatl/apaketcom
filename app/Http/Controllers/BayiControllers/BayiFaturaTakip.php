<?php

namespace App\Http\Controllers\BayiControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Istekfatura  ;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Models\Robothesaphareketleri;
use Log;
use DateTime;





class BayiFaturaTakip 
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
        $robotOzellikleri=DB::select("SELECT * FROM robot WHERE id=?",array($robot));
        
        foreach ($idler as $id ) 
        {
            Log::info("Manuel Fatura istekid:$id, durum:$durum ");
            switch ($durum) {
                case '0'://işleme al   
                    $istekOzellikleri=DB::select("SELECT robotId , durum FROM istekfatura WHERE id=? LIMIT 1",array($id));
                    if( $istekOzellikleri[0]->durum==1)
                        $robotMesgul=DB::update("UPDATE robot SET mesgul=0 WHERE id=? ",array($istekOzellikleri[0]->robotId));

                    $req=Istekfatura::where("id",$id)->first();
                    $req->denemeSayisi=0;
                    $req->durum=0;
                    $req->robotId=1;
                    $req->robotDondu=0;
                    $req->robotAldi=0;
                    $req->sonDegisiklikYapan=Auth::user()->takmaAd;
                    $req->save();
                break;
                case '2'://onayla
                    $istekOzellikleri=DB::select("SELECT robotId , durum FROM istekfatura WHERE id=? LIMIT 1",array($id));
                    try
                    {
                        DB::beginTransaction();
                        $istek=DB::update("UPDATE istekfatura 
                                    SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                                    WHERE id=? ",array(0,2,1,1,1,Auth::user()->takmaAd,$id));
                         
                         if( $istekOzellikleri[0]->durum==1)
                             $robotMesgul=DB::update("UPDATE robot SET mesgul=0 WHERE id=? ",array($istekOzellikleri[0]->robotId));

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
                    $istek=DB::select("SELECT i.id,i.tel ,i.tutar  ,i.kullaniciId , i.faturaNo
                                FROM istekfatura i 
                                WHERE  i.id=? LIMIT 1",array($id));
                    $kullanici=DB::select("SELECT bakiye , sorguUcret,takmaAd FROM kullanici WHERE id=?",array($istek[0]->kullaniciId));

                    $istekOzellikleri=DB::select("SELECT robotId , durum FROM istekfatura WHERE id=? LIMIT 1",array($id));
                    try
                    {
                        DB::beginTransaction();
                        
                        if( $istekOzellikleri[0]->durum==1)
                            $robotMesgul=DB::update("UPDATE robot SET mesgul=0 WHERE id=? ",array($istekOzellikleri[0]->robotId));
                            $istekUpdate=DB::update("UPDATE istekfatura 
                            SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                            WHERE id=? ",array(0,3,1,1,1,Auth::user()->takmaAd,$id));
                        //iade
                        $islem=$cf->ucretIadeFatura($kullanici,$istek,$aciklama);
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
                    $istek=DB::select("SELECT i.robotId , i.durum, i.id,i.tel, i.tutar, i.kullaniciId , i.faturaNo
                                FROM istekfatura i 
                                WHERE  i.id=? LIMIT 1",array($id));
                    try
                    {
                        DB::beginTransaction();
                        if( $istek[0]->durum==1)
                            $robotMesgul=DB::update("UPDATE robot SET mesgul=0 WHERE id=? ",array($istek[0]->robotId));
                        $istekUpdate=DB::update("UPDATE istekfatura 
                                SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                                WHERE id=? ",array(0,2,$robot,1,1,Auth::user()->takmaAd,$id));
                        $robotHesap= new Robothesaphareketleri;
                        $robotHesap->islemTuruId=3;
                        $robotHesap->robotId=$robot;
                        $robotHesap->aciklama=$aciklama;
                        $robotHesap->paket="(".$istek[0]->tel.") f.No:".$istek[0]->faturaNo;
                        $robotHesap->tarih=date('Y-m-d H:i:s', time());
                        $robotHesap->oncekiBakiyeSistem=$robotOzellikleri[0]->sistemBakiye;
                        $robotHesap->sonrakiBakiyeSistem=$robotOzellikleri[0]->sistemBakiye-$istek[0]->tutar;
                        $robotHesap->posBakiye=$robotOzellikleri[0]->posBakiye;
                        $robotHesap->sonDegisiklikYapan=Auth::user()->takmaAd;
                        $robotHesap->save();
                        $robotGuncelle=DB::update("UPDATE robot SET sistemBakiye=? WHERE id=?",array($robotOzellikleri[0]->sistemBakiye-$istek[0]->tutar,$robot));
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
                    
                    $istek=DB::select("SELECT i.robotId , i.durum, i.id,i.tel, i.tutar, i.kullaniciId ,i.faturaNo
                    FROM istekfatura i 
                    WHERE  i.id=? LIMIT 1",array($id));
                    $kullanici=DB::select("SELECT bakiye , sorguUcret,takmaAd FROM kullanici WHERE id=?",array($istek[0]->kullaniciId));
                    try
                    {
                        DB::beginTransaction();
                        if( $istek[0]->durum==1)
                            $robotMesgul=DB::update("UPDATE robot SET mesgul=0 WHERE id=? ",array($istek[0]->robotId));
                            $istekUpdate=DB::update("UPDATE istekfatura 
                            SET denemeSayisi=? , durum=? , robotId=? , robotDondu=? , robotAldi=? , sonDegisiklikYapan=? 
                            WHERE id=? ",array(0,3,$robot,1,1,Auth::user()->takmaAd,$id));
                        $robotHesap= new Robothesaphareketleri;
                        $robotHesap->islemTuruId=4;
                        $robotHesap->robotId=$robot;
                        $robotHesap->aciklama=$aciklama;
                        $robotHesap->paket="(".$istek[0]->tel.") f.No:".$istek[0]->faturaNo;
                        $robotHesap->tarih=date('Y-m-d H:i:s', time());
                        $robotHesap->oncekiBakiyeSistem=$robotOzellikleri[0]->sistemBakiye;
                        $robotHesap->sonrakiBakiyeSistem=$robotOzellikleri[0]->sistemBakiye+$istek[0]->tutar;
                        $robotHesap->posBakiye=$robotOzellikleri[0]->posBakiye;
                        $robotHesap->sonDegisiklikYapan=Auth::user()->takmaAd;
                        $robotHesap->save();
                        $robotGuncelle=DB::update("UPDATE robot SET sistemBakiye=? WHERE id=?",array($robotOzellikleri[0]->sistemBakiye+$istek[0]->tutar,$robot));
                        //iade
                        $islem=$cf->ucretIadeFatura($kullanici,$istek,$aciklama);
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
        $cf= new CommonFunctions;
        

        $sorguArr=array($tar1);
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
                    i.durum!=3 AND
                    i.created_at >= ? 
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
                    i.robotId=r.id AND
                    i.created_at >= ? 
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
        if($tel!=null &&  $tel!="")
        {
            $sorguFiltre=$sorguFiltre."AND i.tesisatNo=? ";
            array_push($sorguArr,$tel);
            $durum=null;
        }
        if($durum!=null && $durum!=-1)
        {
            if($durum==0)
            {
                $sorguFiltre=$sorguFiltre."AND ( i.durum=? OR i.durum=? OR i.durum=? ) ";
                array_push($sorguArr,1);
                array_push($sorguArr,0);
                array_push($sorguArr,4);
            }
            else
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
       

        return view("BayiEkranlari/fatura/FaturaTakip",array("satisToplam"=>$satisToplam,
                                                "takipler"=>$takipler,
                                                "tar1"=>$tar1,
                                                "tar2"=>$tar2,
                                                "filtreler"=>$filtreler,
                                                "suankiSayfa"=>$suankiSayfa,
                                                "sayfaSayisi"=>$cf->SayfaSayisi,
                                                "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi));
    }
  
   

}



