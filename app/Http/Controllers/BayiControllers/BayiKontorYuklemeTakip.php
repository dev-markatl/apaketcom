<?php

namespace App\Http\Controllers\BayiControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Istek  ;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Models\Robothesaphareketleri;
use Log;
use DateTime;





class BayiKontorYuklemeTakip 
{
    public function Temizle(Request $request)
    {
        $manager = new SessionManager;
        $manager->PageName="YuklemeTakip";
        $manager->ClearAllData();
        return redirect("bayi-kontor-yuklemetakip");
    }
   
    public function GetData(Request $request)
    { 
        $operator   = $request->operator;
        $tip        = $request->tip;
        $durum      = $request->durum;
        $session    = $request->session;
        $manager    = new SessionManager;
        $tar1       = $request->tarih1;
        $tar2       = $request->tarih2;
        $bayi       = $request->bayiler;
        $robotId    = $request->robotlar;
        $tel        = $request->tel;
        $manager->PageName="YuklemeTakip";
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
            $tel=$durum=$manager->Tel;
        }
        $filtreler  ="&operator=$operator&tip=$tip&durum=$durum&session=$session&tarih1=$tar1&tarih2=$tar2&bayiler=$bayi&robotlar=$robotId&tel=$tel";
        if($tar1==null)
            $tar1=date('Y-m-d', time());
        $sayfadaGosterilecekKayitSayisi=20;
        $suankiSayfa=$request->sayfa;
        if($suankiSayfa==null)
            $suankiSayfa=1;
        $cf= new CommonFunctions;
        

        $sorguArr=array(Auth::user()->id,$tar1);
        $sorgu="
                SELECT
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
                    i.robotId=r.id AND k.id=? AND
                    i.created_at >= ? 
                ";
                
        if($tar2!=null)
        {
            $sorgu=$sorgu."AND i.created_at <= ? + INTERVAL 1 DAY ";
            array_push($sorguArr,$tar2);
        }
        if($operator!=null && $operator!=-1)
        {
            $sorgu=$sorgu."AND o.id=? ";
            array_push($sorguArr,$operator);
        }
        if($tip!=null && $tip!=-1)
        {
            $sorgu=$sorgu."AND t.id=? ";
            array_push($sorguArr,$tip);
        }
        if($durum!=null && $durum!=-1)
        {
            $sorgu=$sorgu."AND i.durum=? ";
            array_push($sorguArr,$durum);
        }
        if($bayi!=null && $bayi!=-1)
        {
            $sorgu=$sorgu."AND k.id=? ";
            array_push($sorguArr,$bayi);
        }
        if($robotId!=null && $robotId!=-1)
        {
            $sorgu=$sorgu."AND r.id=? ";
            array_push($sorguArr,$robotId);
        }
        if($tel!=null &&  $tel!="")
        {
            $sorgu=$sorgu."AND i.tel=? ";
            array_push($sorguArr,$tel);
        }
            
        
        $count=$cf->GetCount($sorgu,$sorguArr);
        

        
        $manager->Durum=$durum;
        $manager->Tip=$tip;
        $manager->Operator=$operator;
        $manager->Bayiler=$bayi;
        $manager->Robotlar=$robotId;
        $manager->Tel=$tel;
        $manager->Tarih1=$tar1;
        $manager->Tarih2=$tar2;
        $manager->SetAllData();

        $sorgu=$sorgu."Order By i.created_at DESC ";
        $takipler=$cf->Paginate($sorgu,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);
        //echo (new DateTime('now'))->format('d-m-Y H:i:s');
       

        return view("BayiEkranlari/kontor/YuklemeTakip",array("takipler"=>$takipler,
                                                "tar1"=>$tar1,
                                                "tar2"=>$tar2,
                                                "filtreler"=>$filtreler,
                                                "suankiSayfa"=>$suankiSayfa,
                                                "sayfaSayisi"=>$cf->SayfaSayisi,
                                                "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi));
    }
  
   

}



