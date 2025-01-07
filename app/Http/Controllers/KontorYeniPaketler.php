<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Operator ;
use App\Models\Tip;
use App\Models\Kullanici;
use App\Models\Rol;
use Log;




class KontorYeniPaketler 
{
    
    public function YeniPaketListesi(Request $request)
    {
        $sorgu      ="SELECT p.sonDegisiklikYapan, p.created_at  as tarih ,p.adi,p.id,o.adi as operatorAdi,t.adi as tipAdi , p.resmiSatisFiyati 
                        FROM operator o,tip t,paket p
                        WHERE p.operatorId=o.id AND  
                            p.tipId=t.id AND
                            p.silindi=0  AND
                            p.yeni=1 AND
                            p.kod =9999 AND
                            p.aktif=1 ORDER BY id DESC"; 
        $yeniPaketler=DB::select($sorgu);
        
        return view("kontor/YeniPaketler",array("paketler"=>$yeniPaketler));
    }
    public function DurumGuncelle(Request $request)
    {
        try
        {
            $idler = $request->Cb;
            $durum = $request->Durum;
            $sorgu = "UPDATE paket SET aktif=? , silindi=?  WHERE id IN(";
            foreach($idler as $id)
            {
                //toplu update sql i calıstırılabilir
                //UPDATE paket SET sorguyaEkle=1 Where id IN(1,2,3)
                
                $sorgu=$sorgu.$id.",";

            }
            $sorgu=substr($sorgu,0,strlen($sorgu)-1);
            $sorgu=$sorgu." )";
            Log::info("Yeni paketler sil islemi sorgu:$sorgu");
            $update=DB::update($sorgu,array(0,1));
            
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



