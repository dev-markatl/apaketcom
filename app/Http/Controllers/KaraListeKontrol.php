<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Karaliste;


class KaraListeKontrol
{
    public function YeniNumaraEkle(Request $request)
    {
        try
        {

            $yeniNumara = new Karaliste;
            $yeniNumara->telefon = $request->telefonNo;
            $yeniNumara->sorgu_blok = $request->sorguBlok;
            $yeniNumara->yukleme_blok = $request->yuklemeBlok;
            $yeniNumara->save();

           
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

    public function KaraListeView(Request $request)
    {
        try
        {
           
            return view("ayarlar/YeniKaraListeEkle");           
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
        
    }
    public function Numaralar(Request $request)
    {
        $numaralar = Karaliste::all();
        
        return view("ayarlar/KaraListe",array("numaralar"=>$numaralar));
    }


    public function DeleteSw(Request $request)
    {
        try
        {
            $id = $request->id;
            
            $numara=Karaliste::where("id",$id)->delete();

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


    public function UpdateSw(Request $request)
    {
        try
        {
            $id=$request->id;
            $status=$request->status;
            
            $numara=Karaliste::where("id",$id)->first();
            $numara->sorgu_blok=$status;
            $numara->save();

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

    public function UpdateYuklemeSw(Request $request)
    {
        try
        {
            $id=$request->id;
            $status=$request->status;
            
            $numara = Karaliste::where("id",$id)->first();
            $numara->yukleme_blok=$status;
            $numara->save();

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



