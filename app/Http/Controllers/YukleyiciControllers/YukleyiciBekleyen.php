<?php

namespace App\Http\Controllers\YukleyiciControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use App\Classes\YukleyiciIslemleri;
use App\Models\Kullanici;
use App\Models\Ilce;
use App\Models\Il;
use App\Classes\RobotFunctions;




class YukleyiciBekleyen 
{
  
    public function bekleyenSayisi(Request $request)
    {
        $islemler= new YukleyiciIslemleri;

        $rf=new RobotFunctions();
            
        $robot=$rf->GetRobot(Auth::guard("RobotAuth")->user()->adi,Auth::guard("RobotAuth")->user()->sifre);

        $count=$islemler->YetkiyeGoreBekleyenSayisi($robot);
        return response()->json([
            "count"=>$count
            
        ]);
    }
    
    public function index(Request $request)
    {
        try
        {
            
            $request->session()->forget('cevapsizIslem');


            $islemler= new YukleyiciIslemleri;

            $cevapsizKontrol = $islemler->YukleyiciCevapsizKontrol(Auth::guard("RobotAuth")->user());

         
            if ($cevapsizKontrol)
                return redirect('yukleyici-sonuc')->with(["cevapsizIslem"=>"LÜTFEN İŞLEME CEVAP VERİN!"]);

            //$count=$islemler->YetkiyeGoreBekleyenSayisi(Auth::guard("RobotAuth")->user());
            $rf=new RobotFunctions();
            
            $robot=$rf->GetRobot(Auth::guard("RobotAuth")->user()->adi,Auth::guard("RobotAuth")->user()->sifre);
        
            $count=$islemler->YetkiyeGoreBekleyenSayisi($robot);
            
            $bakiye=Auth::guard("RobotAuth")->user()->sistemBakiye;
            $robotId=Auth::guard("RobotAuth")->user()->id;
            return view("YukleyiciEkranlari/YukleyiciBekleyen",array("bekleyenSayisi"=>$count,"bakiye"=>$bakiye,"id"=>$robotId));
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
    }
   
    public function sonuc(Request $request)
    {
        try
        {
           
            $islemler= new YukleyiciIslemleri;
            $bekleyen=$islemler->KontorBekleyenCek(Auth::guard("RobotAuth")->user()->adi,Auth::guard("RobotAuth")->user()->sifre);
            if(count($bekleyen)<1)
                return redirect()->to("yukleyici-bekleyen")->with("message","Size Uygun Bekleyen Kayit Bulunmamaktadir.");
            else
                return view("YukleyiciEkranlari/YukleyiciSonuc",array(  "tel"=>$bekleyen[0]->tel,
                                                                        "paketAdi"=>$bekleyen[0]->paketAdi,
                                                                        "tutar"=>$bekleyen[0]->maliyetFiyati,
                                                                        "resmiSatisFiyati"=>$bekleyen[0]->resmiSatisFiyati,
                                                                        "kayitId"=>$bekleyen[0]->id,
                                                                        "robotId"=>Auth::guard("RobotAuth")->user()->id,
                                                                        "robotAdi"=>Auth::guard("RobotAuth")->user()->adi,
                                                                        "sifre"=>Auth::guard("RobotAuth")->user()->sifre));
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
    }
    

}


