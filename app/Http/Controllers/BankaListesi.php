<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Bankalar  ;






class BankaListesi 
{
    public function YeniBanka(Request $request)
    {
        try
        {

            $banka=new Bankalar;
            $banka->id=$request->id;
            $banka->bankaAdi=$request->bankaAdi;
            $banka->subeAdi=$request->subeAdi;
            $banka->subeKodu=$request->subeKodu;
            $banka->hesapNo=$request->hesapNo;
            $banka->ibanNo=$request->ibanNo;
            $banka->hesapSahibi=$request->hesapSahibi;
            $banka->sonDegisiklikYapan=Auth::user()->takmaAd;
            $banka->aktif=1;
            $banka->save();

           
            return response()->json([ 
              
                "status"=>"true",
                "message"=>"İslem Başarılı! "
                
            ]);
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
    }
    public function BankaGuncelle(Request $request)
    {
        try
        {
           
            $banka=Bankalar::where("id",$request->id)->first();
            $banka->id=$request->id;
            $banka->bankaAdi=$request->bankaAdi;
            $banka->subeAdi=$request->subeAdi;
            $banka->subeKodu=$request->subeKodu;
            $banka->hesapNo=$request->hesapNo;
            $banka->ibanNo=$request->ibanNo;
            $banka->hesapSahibi=$request->hesapSahibi;
            $banka->sonDegisiklikYapan=Auth::user()->takmaAd;

            $banka->save();
            return response()->json([ 
              
                "status"=>"true",
                "message"=>"İslem Başarılı! "
                
            ]);
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
        
    }
    public function BankaOzellikleri(Request $request)
    {
        try
        {
            $id=$request->id;
            if($id>0)
            {
                $banka=Bankalar::where("id",$id)->first();
                return view("ayarlar/YeniBankaEkle",array(
                "id"=>$banka->id,
                "bankaAdi"=>$banka->bankaAdi,
                "subeAdi"=>$banka->subeAdi,
                "subeKodu"=>$banka->subeKodu,
                "hesapNo"=>$banka->hesapNo,
                "ibanNo"=>$banka->ibanNo,
                "hesapSahibi"=>$banka->hesapSahibi,
                "aktif"=>$banka->aktif,
                "update"=>"true",
                "status"=>"true",
                "message"=>"İslem Başarılı! "
                
                ));
            }
            else
            {
                return view("ayarlar/YeniBankaEkle",array(
                "id"=>"",
                "bankaAdi"=>"",
                "subeAdi"=>"",
                "subeKodu"=>"",
                "hesapNo"=>"",
                "ibanNo"=>"",
                "hesapSahibi"=>"",
                "aktif"=>"",
                "update"=>"false",
                "status"=>"true",
                "message"=>"İslem Başarılı! "
                
                ));
            }
            
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
        
    }
    public function Bankalar(Request $request)
    {
        $bankalar=Bankalar::all();
        
        return view("ayarlar/Bankalar",array("bankalar"=>$bankalar));
    }
    public function UpdateSw(Request $request)
    {
        try
        {
            $id=$request->id;
            $status=$request->status;
            
            $banka=Bankalar::where("id",$id)->first();
            $banka->aktif=$status;
            $banka->save();

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



