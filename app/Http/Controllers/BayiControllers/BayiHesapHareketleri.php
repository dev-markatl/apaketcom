<?php

namespace App\Http\Controllers\BayiControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Models\Bankalar;




class BayiHesapHareketleri 
{
    public function bankalar(Request $request)
    {
        $bankalar=Bankalar::all();
        
        return view("BayiEkranlari/odemeHareketleri/BayiBankalar",array("bankalar"=>$bankalar));
    }
    public function index(Request $request)
    {
        $id=Auth::user()->id;
        $tar1       = $request->tarih1;
        $tar2       = $request->tarih2;
        $islem      = $request->islemTuru;
        $session    = $request->session;
        $manager    = new SessionManager;
        $suankiSayfa= $request->sayfa;
        $manager->PageName="BayiHesapHareketleri";
        $sayfadaGosterilecekKayitSayisi=25;
        
        if($session==null)
        {
            $manager->GetAllData();
            $tar1=$manager->Tarih1;
            $tar2=$manager->Tarih2;
            $islem=$manager->IslemTuru;
        }
        if($tar1==null)
            $tar1=date('Y-m-d', time());

        if($suankiSayfa == null)
            $suankiSayfa=1;
        
        $cf= new CommonFunctions;
        $sorguArr =array($id,$tar1);
        $sorgu="SELECT 
                    h.* , 
                    i.adi 
                FROM 
                    kullanicihesaphareketleri h , 
                    islemturu i 
                Where 
                    i.id = h.islemTuruId AND
                    h.kullaniciId=? AND 
                    h.created_at >= ? 
                ";
        
        if($tar2!=null)
        {
            $sorgu=$sorgu."AND h.created_at <= ? + INTERVAL 1 DAY ";
            array_push($sorguArr,$tar2);
        }
        if($islem!=null && $islem!=-1)
        {
            $sorgu=$sorgu."AND i.id=? ";
            array_push($sorguArr,$islem);
        }
        $count=$cf->GetCount($sorgu,$sorguArr);

        $manager->IslemTuru=$islem;
        $manager->Tarih1=$tar1;
        $manager->Tarih2=$tar2;
        $manager->SetAllData();

        $sorgu=$sorgu."Order By tarih desc";
        $hareketler=$cf->Paginate($sorgu,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);
        return view("BayiEkranlari/odemeHareketleri/BayiHesapHareketleri",array("hareketler"=>$hareketler,
                                                        "suankiSayfa"=>$suankiSayfa,
                                                        "sayfaSayisi"=>$cf->SayfaSayisi,
                                                        "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi,
                                                        "id"=>$id));
    }
    public function temizle()
    {
        $manager = new SessionManager;
        $manager->PageName="BayiHesapHareketleri";
        $manager->ClearAllData();
        return redirect("bayi-odemehareketleri-hesaphareketleri");
    }
   

}


