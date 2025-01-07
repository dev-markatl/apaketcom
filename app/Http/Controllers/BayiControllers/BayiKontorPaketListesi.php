<?php

namespace App\Http\Controllers\BayiControllers;

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


class BayiKontorPaketListesi 
{
    
    
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
                "status"=>"",
                "message"=>"",
            
            ));
        }
            
           
        
        
    }
   
    public function Temizle(Request $request)
    {
        $manager    = new SessionManager;
        $manager->PageName="BayiPaketListesi";
        $manager->Durum=-1;
        $manager->SetDataD();
        $manager->Tip=-1;
        $manager->SetDataT();
        $manager->Operator=-1;
        $manager->SetDataO();
        return redirect("bayi-kontor-paketlistesi");
    }
   
    public function PaketListesi(Request $request)
    {
      
       $operator   = $request->operator;
       $tip        = $request->tip;
       $durum      = $request->durum;
       $session    = $request->session;
       $manager    = new SessionManager;
       $sorguArr   = array();
    $sorgu         ="SELECT p.resmiSatisFiyati ,
                            p.maliyetFiyati , 
                            p.kod , 
                            p.adi , 
                            p.id , 
                            o.adi as operatorAdi , 
                            t.adi as tipAdi ,
                            p.gun ,
                            p.herYoneKonusma as hyk, 
                            p.sebekeIciKonusma as sik, 
                            p.herYoneSms as hys, 
                            p.sebekeIciSms as sis, 
                            p.internet 
                        FROM operator o,tip t,paket p 
                            WHERE p.operatorId=o.id AND  
                                    p.tipId=t.id AND
                                    p.silindi=0  AND p.aktif=1 AND 
                                    p.yeni=0 ";

       $manager->PageName="BayiPaketListesi";
       
       if($session==null)
       {
           $manager->GetDataO();
           $manager->GetDataT();
           $operator=$manager->Operator;
           $tip=$manager->Tip;

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
       $manager->Tip=$tip;
       $manager->SetDataT();
       $manager->Operator=$operator;
       $manager->SetDataO();

       $paketler=DB::select($sorgu." ORDER BY p.operatorId , p.tipId ,p.kod",$sorguArr);


       
       return view("BayiEkranlari/kontor/PaketListesi",array("paketler"=>$paketler));
       
      
    }
   

}


