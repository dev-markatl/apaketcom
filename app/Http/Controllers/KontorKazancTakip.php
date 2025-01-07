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



class KontorKazancTakip
{

  public function Temizle(Request $request)
  {
      $manager = new SessionManager;
      $manager->PageName="KazancTakip";
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
      return redirect("kontor-kazanctakip");
  }

  public function VeriCek(Request $request)
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
    $manager->PageName="KazancTakip";




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

    $satisToplam = "SELECT
                        SUM(p.maliyetFiyati) as maliyetTutar,
                        SUM(p.maliyetFiyati) as resmiTutar
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
                        i.durum = 2 AND !(p.kod BETWEEN 4999 AND 6001)
                        AND i.ozelfiyatId = 0";


    $satisOzelToplam = "SELECT
                        SUM(f.maliyet_fiyat) as maliyetOzelTutar,
                        SUM(p.maliyetFiyati) as resmiOzelTutar
                    FROM
                        istek i ,
                        kullanici k,
                        paket p,
                        operator o,
                        tip t,
                        robot r,
                        ozelfiyat f
                    WHERE
                        i.kullaniciId=k.id AND
                        i.paketId = p.id AND
                        i.ozelfiyatId = f.id AND
                        p.operatorId=o.id AND
                        p.tipId=t.id AND
                        i.robotId=r.id AND
                        i.durum = 2 AND !(p.kod BETWEEN 4999 AND 6001)
                        AND i.ozelfiyatId != 0";




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
    $sorguOzelFiyat="
            SELECT
                i.aciklama,
                i.id,
                i.tel,
                r.adi as robotAdi,
                p.adi as paketAdi,
                p.kod as paketKodu,
                p.maliyetFiyati as tutar,
                f.resmi_fiyat,
                f.maliyet_fiyat,
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
                robot r,
                ozelfiyat f
            WHERE
                i.kullaniciId=k.id AND
                p.operatorId=o.id AND
                p.tipId=t.id AND
                i.robotId=r.id AND
                i.paketId = p.id AND
                f.id = (CASE WHEN i.ozelfiyatId != 0 THEN i.ozelfiyatId
                ELSE
                0
                END)
            ";


      $sorguAnaFiyat="SELECT
                  i.aciklama,
                  i.id,
                  i.tel,
                  i.ozelfiyatId,
                  r.adi as robotAdi,
                  p.adi as paketAdi,
                  p.kod as paketKodu,
                  p.maliyetFiyati as maliyetTutar,
                  p.resmiSatisFiyati as resmiTutar,
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
                  p.operatorId=o.id AND
                  p.tipId=t.id AND
                  i.robotId=r.id AND
                  i.paketId = p.id AND
                  i.durum = 2 AND !(p.kod BETWEEN 4999 AND 6001)";

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

/*
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
    */
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

    $count=$cf->GetCount($sorguAnaFiyat.$sorguFiltre,$sorguArr);



    $manager->Durum=$durum;
    $manager->Tip=$tip;
    $manager->Operator=$operator;
    $manager->Bayiler=$bayi;
    $manager->Robotlar=$robotId;
    $manager->Tel=$tel;
    $manager->Tarih1=$tar1;
    $manager->Tarih2=$tar2;
    $manager->SetAllData();
    $satisToplamOnay=DB::select($satisToplam.$sorguFiltre,$sorguArr);
    $satisOzelToplamOnay=DB::select($satisOzelToplam.$sorguFiltre,$sorguArr);

    $maliyetToplam = $satisToplamOnay[0]->maliyetTutar + $satisOzelToplamOnay[0]->maliyetOzelTutar;
    $resmiToplam = $satisToplamOnay[0]->resmiTutar + $satisOzelToplamOnay[0]->resmiOzelTutar;

    $sorguFiltre=$sorguFiltre."Order By i.created_at DESC  ";

    $takipler=$cf->Paginate($sorguAnaFiyat.$sorguFiltre,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);
    //echo (new DateTime('now'))->format('d-m-Y H:i:s');

    foreach ($takipler as $takip)
    {
      if($takip->ozelfiyatId != 0)
      {
        $ozelFiyat = DB::select("SELECT *
        FROM ozelfiyat
        WHERE id=? LIMIT 1",array($takip->ozelfiyatId));
        if (count($ozelFiyat)!=0)
        {
          $takip->resmiTutar = $takip->maliyetTutar;
          $takip->maliyetTutar = $ozelFiyat[0]->maliyet_fiyat;

        }
        
      }
      else {
          $takip->resmiTutar = $takip->maliyetTutar;
        }
    }

    //dd($takipler);

    $ddRobotlar=$dd->DdRobotlar();//performans için burda yapılıyor sorgu;
    $ddAktifRobotlar=DB::select("SELECT * FROM robot WHERE aktif=1");



    return view("kontor/KazancTakip",array("resmiToplam"=>$resmiToplam,
                                            "maliyetToplam"=>$maliyetToplam,
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
