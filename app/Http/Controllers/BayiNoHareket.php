<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Istek;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Classes\HesapIslemleri;
use App\Models\Robothesaphareketleri;
use Log;
use App\Classes\DropDown;
use DateTime;
use App\Models\Bayibilgi;




class BayiNoHareket
{
    public function DisBayiTemizle(Request $request)
    {
        $manager            =   new SessionManager;
        $manager->PageName  =   "DisBayiler";
        $manager->Bayiler    =   null;
        $manager->SetAllData();
        return redirect("bayinohareket-bayiler");
    }



    public function Temizle(Request $request)
    {
        $manager            =   new SessionManager;
        $manager->PageName  =   "BayiNoHareket";
        $manager->Tarih1    =   null;
        $manager->Tarih2    =   null;
        $manager->SetAllData();
        return redirect("bayinohareket-bayihareket");
    }

    public function KullaniciHareketTemizle(Request $request)
    {

        $manager            =   new SessionManager;
        $manager->PageName  =   "KullaniciHareket";
        $manager->Tarih1    =   null;
        $manager->Tarih2    =   null;
        $manager->Bayiler   =   null;
        $manager->SetAllData();
        return redirect("bayinohareket-kullanicihareket");

    }

    public function SitelerTemizle(Request $request)
    {

        $manager            =   new SessionManager;
        $manager->PageName  =   "Siteler";
        $manager->Tarih1    =   null;
        $manager->Tarih2    =   null;
        $manager->Bayiler   =   null;
        $manager->SetAllData();
        return redirect("bayinohareket-siteler");

    }

    public function Siteler(Request $request)
    {
        $manager = new SessionManager;
        $manager->PageName = "Siteler";
        $session = $request->session;

        $sayfadaGosterilecekKayitSayisi=25;
        $suankiSayfa=$request->sayfa;
        $kullaniciNo = $request->bayiler;
        $siteAdres = $request->siteadres;

        if($suankiSayfa==null)
          $suankiSayfa=1;

        $whereSart = " ";

        $sorguFiltre = " ";
        $sorguArr = array();

        if($session==null)
        {
            $manager->GetAllData();
            $kullaniciNo=$manager->Bayiler;
            $siteAdres=$manager->SiteAdres;
        }

        if($kullaniciNo != null && $kullaniciNo !=-1)
        {
            $kullaniciId = collect(DB::select("SELECT * FROM kullanici WHERE takmaAd = $kullaniciNo"))->first();
            $whereSart=$whereSart."AND k.id=? ";
            array_push($sorguArr,$kullaniciNo);
        }

        if($siteAdres!=null && $siteAdres!=-1)
        {
            $whereSart=$whereSart."AND b.site_adres=? ";
            array_push($sorguArr,$siteAdres);
            
        }

        if($kullaniciNo != null && $kullaniciNo !=-1)
        {
            $kullaniciId = collect(DB::select("SELECT * FROM kullanici WHERE takmaAd = $kullaniciNo"))->first();
            $whereSart=$whereSart."AND k.id=? ";
            array_push($sorguArr,$kullaniciNo);
        }

        

        $sayisorguCount=DB::select("SELECT
        b.bayi_ad,
        b.id,
        b.bayi_id,
        k.ad,
        k.soyAd,
        k.takmaAd,
        b.site_adres
        FROM bayibilgi b
        INNER JOIN kullanici k
        ON b.takma_ad = k.takmaad
        $whereSart
        GROUP BY k.ad,k.soyAd,k.takmaAd,b.site_adres ORDER BY b.site_adres ASC",$sorguArr);


        //dd($sayisorguCount);


        $sayisorgu = "SELECT
        b.bayi_ad,
        b.id,
        b.bayi_id,
        k.ad,
        k.soyAd,
        k.takmaAd,
        b.site_adres
        FROM bayibilgi b
        INNER JOIN kullanici k
        ON b.takma_ad = k.takmaad
        $whereSart
        GROUP BY k.ad,k.soyAd,k.takmaAd,b.site_adres ORDER BY b.site_adres ASC";

        //bayihareket.bayi_id,b.bayi_ad,b.id,b.bayi_id,

        $cf= new CommonFunctions;

        $count=count($sayisorguCount);
        
        $bayiler=$cf->Paginate($sayisorgu,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);

        foreach($bayiler as $bayi)
        {
            $toplamAltBayi = Bayibilgi::where('takma_ad',$bayi->takmaAd)->where('site_adres',$bayi->site_adres)->count();
            $sorguBlokeliAltBayi = Bayibilgi::where('takma_ad',$bayi->takmaAd)->where('site_adres',$bayi->site_adres)->where('sorgu_blokaj','1')->count();
            $yuklemeBlokeliAltBayi = Bayibilgi::where('takma_ad',$bayi->takmaAd)->where('site_adres',$bayi->site_adres)->where('yukleme_blokaj','1')->count();
            $bayi->toplam_alt_bayi = $toplamAltBayi;
            $bayi->sorgu_blokeli_alt_bayi = $sorguBlokeliAltBayi;
            $bayi->yukleme_blokeli_alt_bayi = $yuklemeBlokeliAltBayi;
        }
        
        return view("bayilistesi/Siteler",array("bayiler"=>$bayiler,
        "suankiSayfa"=>$suankiSayfa,
        "sayfaSayisi"=>$cf->SayfaSayisi,
        "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi
        ));

    }

    public function Bayiler(Request $request)
    {
      $manager = new SessionManager;
      $manager->PageName="DisBayiler"; 
      $session = $request->session;

      $sayfadaGosterilecekKayitSayisi=25;
      $suankiSayfa=$request->sayfa;
      $bayi       = $request->bayiler;
      $altBayiNo = $request->tel;
      $siteAdres = $request->siteadres;

      if($suankiSayfa==null)
          $suankiSayfa=1;

      if($session==null)
      {
          $manager->GetAllData();
          $bayi=$manager->Bayiler;
          $altBayiNo=$manager->Tel;
          $siteAdres=$manager->SiteAdres;
      }

   

      $filtreler  ="&bayiler=$bayi&tel=$altBayiNo&siteadres=$siteAdres";


      

      
      

      $cf= new CommonFunctions;

      $bayiler = "SELECT b.bayi_ad,
      b.sorgu_blokaj,
      b.yukleme_blokaj,
      b.id,
      b.bayi_id,
      b.site_adres,
      k.ad,
      k.soyAd,
      b.kategori,
      k.takmaAd FROM bayibilgi b INNER JOIN kullanici k
      ON b.takma_ad = k.takmaad";
      $sorgu = $bayiler;
      $sorguArr=array();
      $sorguFiltre=" ";

    
      if($bayi!=null && $bayi!=-1)
      {
          $sorguFiltre=$sorguFiltre."WHERE k.id=? ";
          array_push($sorguArr,$bayi);
          
      }

      if($altBayiNo!=null && $altBayiNo!=-1)
      {
          $sorguFiltre=$sorguFiltre."AND b.bayi_id=? ";
          array_push($sorguArr,$altBayiNo);
          
      }

      if($siteAdres!=null && $siteAdres!=-1)
      {
          $sorguFiltre=$sorguFiltre."AND b.site_adres=? ";
          array_push($sorguArr,$siteAdres);
          
      }

      //dd($sorguFiltre);

      $sorguFiltre =$sorguFiltre." ORDER BY bayi_id ASC";

    

      $count=$cf->GetCount($sorgu.$sorguFiltre,$sorguArr);
      //$sorgu=$sorgu." Order By k.aktif DESC ,k.firmaAdi asc ";
      $bayiler=$cf->Paginate($sorgu.$sorguFiltre,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);
      //$bakiyeToplami=DB::select("SELECT SUM(bakiye) toplam FROM kullanici WHERE rolId!=1 AND aktif=1");

      //$manager->Tel=$kullaniciAdi;
     

      $manager->Bayiler = $bayi;
      $manager->Tel = $altBayiNo;
      $manager->SiteAdres = $siteAdres;
      $manager->SetAllData();

      //dd($manager);

      foreach ($bayiler as $bayi)
      {
        if($bayi->sorgu_blokaj == 1)
          $bayi->sorgu_blokaj = "checked";
        if($bayi->yukleme_blokaj == 1)
          $bayi->yukleme_blokaj = "checked";
      }





      return view("bayilistesi/DisBayiler",array("bayiler"=>$bayiler,
                                          "suankiSayfa"=>$suankiSayfa,
                                          "sayfaSayisi"=>$cf->SayfaSayisi,
                                          "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi,
                                          "filtreler"=>$filtreler
                                          ));

    }

    public function KullaniciHareketSorgula(Request $request)
    {


        $manager    = new SessionManager;
        $session    = $request->session;
        $tar1       = $request->tarih1;
        $tar2       = $request->tarih2;
        $kullaniciNo = $request->bayiler;
        $siteAdres = $request->siteadres;

        $sayfadaGosterilecekKayitSayisi=25;
        $suankiSayfa=$request->sayfa;

        if($suankiSayfa==null)
          $suankiSayfa=1;

        $manager->PageName = "KullaniciHareket";


        $whereSart = "where bayihareket.islem_tarih >= ? AND bayihareket.islem_tarih <= ? ";

        $sorguFiltre=" ";
        $sorguArr=array();

        if($session==null)
        {
            $manager->GetAllData();
            $tar1=$manager->Tarih1;
            $tar2=$manager->Tarih2;
            $kullaniciNo=$manager->Bayiler;
            $siteAdres=$manager->SiteAdres;
        }


        if($tar1==null)
            $tar1=date('Y-m-d', time());
            array_push($sorguArr,$tar1);
        if($tar2==null)
            $tar2=date('Y-m-d', time());
            array_push($sorguArr,$tar2);


        if($siteAdres!=null && $siteAdres!=-1)
        {
            $whereSart=$whereSart."AND b.site_adres=? ";
            array_push($sorguArr,$siteAdres);
            
        }


        $filtreler  ="&tarih1=$tar1&tarih2=$tar2&bayiler=$kullaniciNo";


        if($kullaniciNo != null && $kullaniciNo !=-1)
        {
            $kullaniciId = collect(DB::select("SELECT * FROM kullanici WHERE takmaAd = $kullaniciNo"))->first();
            $whereSart=$whereSart."AND k.id=? ";
            array_push($sorguArr,$kullaniciNo);
        }


            $sayisorguCount=DB::select("SELECT bayihareket.bayi_id,bayihareket.islem_tarih,
            count(*) total,
            sum(case when operator = '1' then 1 else 0 end) turkcell,
            sum(case when operator = '2' then 1 else 0 end) vodafone,
            sum(case when operator = '3' then 1 else 0 end) turktelekom,
            b.bayi_ad,
            b.id,
            b.bayi_id,
            k.ad,
            k.soyAd,
            k.takmaAd,
            b.site_adres
            from bayihareket
            INNER JOIN bayibilgi b
            ON bayihareket.bayi_id = b.id
            INNER JOIN kullanici k
            ON b.takma_ad = k.takmaad
            $whereSart
            group by k.ad,k.soyAd,k.takmaAd,b.site_adres order by total DESC",$sorguArr);

            /*
            // bayihareket.bayi_id,b.bayi_ad,b.id,b.bayi_id,
            */


            $sayisorgu = "SELECT bayihareket.bayi_id,bayihareket.islem_tarih,
            count(*) total,
            sum(case when operator = '1' or operator = '11' then 1 else 0 end) turkcell,
            sum(case when operator = '2' or operator = '21' then 1 else 0 end) vodafone,
            sum(case when operator = '3' or operator = '31' then 1 else 0 end) turktelekom,
            sum(case when operator = '11' then 1 else 0 end) turkcellExIptal,
            sum(case when operator = '21' then 1 else 0 end) vodafoneExIptal,
            sum(case when operator = '31' then 1 else 0 end) turktelekomExIptal,

            b.bayi_ad,
            b.id,
            b.bayi_id,
            b.site_adres,
            k.ad,
            k.soyAd,
            k.takmaAd
            from bayihareket
            INNER JOIN bayibilgi b
            ON bayihareket.bayi_id = b.id
            INNER JOIN kullanici k
            ON b.takma_ad = k.takmaad
            $whereSart
            group by k.ad,k.soyAd,k.takmaAd,b.site_adres order by total DESC";

//bayihareket.bayi_id,b.bayi_ad,b.id,b.bayi_id,

            $cf= new CommonFunctions;

            $count=count($sayisorguCount);
            
            $hareketT=$cf->Paginate($sayisorgu,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);


            if ($tar2 == date('Y-m-d', time()))
            {
              $tar2 = null;
            }

            $manager->Tarih1=$tar1;
            $manager->Tarih2=$tar2;
            $manager->Bayiler=$kullaniciNo;
            $manager->SiteAdres=$siteAdres;
            $manager->SetAllData();


            return view("bayilistesi/KullaniciHareket",array("tar1"=>$tar1,"tar2"=>$tar2,"hrkT"=>$hareketT,
                                                    "suankiSayfa"=>$suankiSayfa,
                                                    "sayfaSayisi"=>$cf->SayfaSayisi,
                                                    "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi,
                                                    "filtreler"=>$filtreler
                                                    ));


    }

    public function HareketSorgula(Request $request)
    {

        $manager    = new SessionManager;
        $session    = $request->session;
        $tar1       = $request->tarih1;
        $tar2       = $request->tarih2;
        $kullaniciNo = $request->bayiler;
        $altBayiNo = $request->tel;
        $siteAdres = $request->siteadres;

        $sayfadaGosterilecekKayitSayisi=25;
        $suankiSayfa=$request->sayfa;
        if($suankiSayfa==null)
          $suankiSayfa=1;

        $manager->PageName = "BayiNoHareket";


        $whereSart = "where bayihareket.islem_tarih >= ? AND bayihareket.islem_tarih <= ? ";

        $sorguFiltre=" ";
        $sorguArr=array();

        if($session==null)
        {
            $manager->GetAllData();
            $tar1=$manager->Tarih1;
            $tar2=$manager->Tarih2;
            $kullaniciNo=$manager->Bayiler;
            $altBayiNo=$manager->Tel;
            $siteAdres=$manager->SiteAdres;
        }


        if($tar1==null)
            $tar1=date('Y-m-d', time());
            array_push($sorguArr,$tar1);
        if($tar2==null)
            $tar2=date('Y-m-d', time());
            array_push($sorguArr,$tar2);


                    
    

        if($altBayiNo!=null && $altBayiNo!=-1)
        {
            $whereSart=$whereSart."AND b.bayi_id=? ";
            array_push($sorguArr,$altBayiNo);
            
        }

        if($siteAdres!=null && $siteAdres!=-1)
        {
            $whereSart=$whereSart."AND b.site_adres=? ";
            array_push($sorguArr,$siteAdres);
            
        }



        $filtreler  ="&tarih1=$tar1&tarih2=$tar2&bayiler=$kullaniciNo&tel=$altBayiNo&siteadres=$siteAdres";


        //dd($kullaniciNo);

            //$manager->IslemTuru=$islemTuru;



          //$hareketSorgu=DB::select("SELECT COUNT(bayibilgi.id) FROM bayibilgi,bayihareket WHERE bayihareket.bayi_id=bayibilgi.id");
/*
          $hareketSorguT=DB::select("SELECT bayibilgi.*,
          (SELECT *, sum(case when operator = '1' then 1 else 0 end) turkcell,
          sum(case when operator = '2' then 1 else 0 end) vodafone
          FROM bayihareket WHERE bayihareket.bayi_id = bayibilgi.id AND bayihareket.islem_tarih >= ? AND bayihareket.islem_tarih <= ?)
          FROM bayibilgi", array($tar1,$tar2));*/
            //$whereSart = "where bayihareket.islem_tarih >= ? AND bayihareket.islem_tarih <= ? ";

            if($kullaniciNo != null && $kullaniciNo !=-1)
            {
                $kullaniciId = collect(DB::select("SELECT * FROM kullanici WHERE takmaAd = $kullaniciNo"))->first();
                $whereSart=$whereSart."AND k.id=? ";
                array_push($sorguArr,$kullaniciNo);
            }


            /*ORİJİNAL SORGU 
            */
            $sayisorguCount=DB::select("SELECT bayihareket.bayi_id,bayihareket.islem_tarih,
            count(*) total,
            sum(case when operator = '1' then 1 else 0 end) turkcell,
            sum(case when operator = '2' then 1 else 0 end) vodafone,
            sum(case when operator = '3' then 1 else 0 end) turktelekom,
            b.bayi_ad,
            b.id,
            b.bayi_id,
            k.ad,
            k.soyAd,
            k.takmaAd
            from bayihareket
            INNER JOIN bayibilgi b
            ON bayihareket.bayi_id = b.id
            INNER JOIN kullanici k
            ON b.takma_ad = k.takmaad
            $whereSart
            group by bayihareket.bayi_id,b.bayi_ad,b.id,b.bayi_id,k.ad,k.soyAd,k.takmaAd order by total DESC",$sorguArr);

            /*
            */


            $sayisorgu = "SELECT bayihareket.bayi_id,bayihareket.islem_tarih,
            count(*) total,
            sum(case when operator = '1' or operator = '11' then 1 else 0 end) turkcell,
            sum(case when operator = '2' or operator = '21' then 1 else 0 end) vodafone,
            sum(case when operator = '3' or operator = '31' then 1 else 0 end) turktelekom,
            sum(case when operator = '11' then 1 else 0 end) turkcellExIptal,
            sum(case when operator = '21' then 1 else 0 end) vodafoneExIptal,
            sum(case when operator = '31' then 1 else 0 end) turktelekomExIptal,

            b.bayi_ad,
            b.id,
            b.bayi_id,
            b.site_adres,
            k.ad,
            k.soyAd,
            k.takmaAd
            from bayihareket
            INNER JOIN bayibilgi b
            ON bayihareket.bayi_id = b.id
            INNER JOIN kullanici k
            ON b.takma_ad = k.takmaad
            $whereSart
            group by bayihareket.bayi_id,b.bayi_ad,b.id,b.bayi_id,k.ad,k.soyAd,k.takmaAd order by total DESC";


            $cf= new CommonFunctions;

            $count=count($sayisorguCount);
            
            //$sorgu=$sorgu." Order By k.aktif DESC ,k.firmaAdi asc ";
            $hareketT=$cf->Paginate($sayisorgu,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);


            //dd($sayisorgu);
          /*
          group by bayihareket.bayi_id,b.bayi_ad,b.id,b.bayi_id,bayihareket.islem_tarih,k.ad,k.soyAd,k.takmaAd order by total DESC",$sorguArr);

          $hareketSorguV=DB::select("SELECT bayibilgi.*,
          (SELECT COUNT(*) FROM bayihareket WHERE bayihareket.bayi_id = bayibilgi.id AND bayihareket.islem_tarih >= ? AND bayihareket.islem_tarih <= ? AND bayihareket.operator = 2) AS total2
          FROM bayibilgi", array($tar1,$tar2));

          $hareketSorguA=DB::select("SELECT bayibilgi.*,
          (SELECT COUNT(*) FROM bayihareket WHERE bayihareket.bayi_id = bayibilgi.id AND bayihareket.islem_tarih >= ? AND bayihareket.islem_tarih <= ? AND bayihareket.operator = 3) AS total3
          FROM bayibilgi", array($tar1,$tar2));


*/
/*
                COUNT(CASE WHEN `col1` LIKE '%something%' THEN 1 END) AS count1,
                  COUNT(CASE WHEN `col1` LIKE '%another%' THEN 1 END) AS count2,
                  COUNT(CASE WHEN `col1` LIKE '%word%' THEN 1 END) AS count3*/

            if ($tar2 == date('Y-m-d', time()))
            {
              $tar2 = null;
            }

            $manager->Tarih1=$tar1;
            $manager->Tarih2=$tar2;
            $manager->Bayiler=$kullaniciNo;
            $manager->SiteAdres=$siteAdres;
            $manager->Tel = $altBayiNo;
            $manager->SetAllData();


        //dd($hareketSorgu);
        //dd($sayisorgu);


      return view("bayilistesi/BayiNoHareket",array("tar1"=>$tar1,"tar2"=>$tar2,"hrkT"=>$hareketT,
                                                    "suankiSayfa"=>$suankiSayfa,
                                                    "sayfaSayisi"=>$cf->SayfaSayisi,
                                                    "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi,
                                                    "filtreler"=>$filtreler
                                                    ));

    }

    public function BilgiGuncelle(Request $request)
    {
        try
        {
            $id=$request->id;
            $bayiBilgi=$request->bayiBilgi;
            $bayi =bayiBilgi::where("id",$id)->first();
            $bayi->bayi_ad=$bayiBilgi;
            $bayi->save();


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

    public function KategoriGuncelle(Request $request)
    {
        try
        {
            $id=$request->id;
            $bayiKategori=$request->bayiKategori;
            $bayi =bayiBilgi::where("id",$id)->first();
            $bayi->kategori=$bayiKategori;
            $bayi->save();


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


    public function Blokaj(Request $request)
    {
        try
        {
            $id=$request->id;
            $bayiBlokaj=$request->status;
            $bayi =bayiBilgi::where("id",$id)->first();
            $bayi->sorgu_blokaj=$bayiBlokaj;
            $bayi->save();


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

    public function BlokajYukleme(Request $request)
    {
        try
        {
            $id=$request->id;
            $bayiBlokaj=$request->status;
            $bayi =bayiBilgi::where("id",$id)->first();
            $bayi->yukleme_blokaj=$bayiBlokaj;
            $bayi->save();


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

    public function SitelerBlokaj(Request $request)
    {
        try
        {
            $bayiBlokaj=$request->status;
            $takmaAd = $request->takmaad;
            $siteAdres = $request->sitead;

            $bayi = BayiBilgi::where("site_adres",$siteAdres)->where('takma_ad',$takmaAd)
            ->update(['sorgu_blokaj' => $bayiBlokaj]);


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

    public function SitelerBlokajYukleme(Request $request)
    {
        try
        {
            $bayiBlokaj=$request->status;
            $takmaAd = $request->takmaad;
            $siteAdres = $request->sitead;

            $bayi = BayiBilgi::where("site_adres",$siteAdres)->where('takma_ad',$takmaAd)
            ->update(['yukleme_blokaj' => $bayiBlokaj]);

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




}
