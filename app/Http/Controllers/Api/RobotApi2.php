<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Istek ;
use App\Models\Robot;
use App\Models\Paket;
use App\Models\IstekCevap;
use App\Models\Tip;
use App\Models\Operator;
use App\Classes\CommonFunctions;
use App\Classes\RobotFunctions;

class RobotApi2
{
    public function PaketAyar(Request $request)
    {
        // $paketUpdates=Paket::get();
        // foreach($paketUpdates as $paketUpdate)
        // {
        //     $paketUpdate->sistemPaketKodu=$paketUpdate->id;
        //     $paketUpdate->save();
        // }
        
    }
    public function getNumbers(Request $request)
    {
        //http://localhost/SorguProject/public/api/Numbers?robotName=Robot1&password=123
        $rf     = new RobotFunctions();
        $robot  = $rf->GetRobot($request->input("robotName"),$request->input("password"));
        
        //robot giris kontrolu
        if(count($robot)<1)
        {
            return response()->json([
                
                "status"=>"false"
                
            ]);
        }
        //robot dolumu kontrolu yapıldı dolu oldugu için aynı paket tekrar verildi
        if($robot[0]->mesgul!=0)
        {
            $busyRobot=$rf->BusyRobot($robot[0]->operatorAdi,$robot[0]->id);
            return response()->json([
                "id"=> $busyRobot[0]->id,
                "number"=> $busyRobot[0]->tel,
                "type"=> $busyRobot[0]->tipAdi,
                "packet"=> $busyRobot[0]->paketAdi,
                "code"=> $busyRobot[0]->kod,
                "operator"=>$busyRobot[0]->operatorAdi,
                "status"=>"true"
                
            ]);
        }
        if($robot[0]->yetkiSorgu==0 && $robot[0]->yetkiYukle==0)
        {
            return response()->json([
                
                "status"=>"false",
                "message"=>"yetki yetersiz"
            ]);
        }
        //çift kayıt olmaması için transaction baslatıldı
        DB::beginTransaction();//transaction selectten sonra yapılacak select içerisinde degistirlecek veriler update sartina yazılacak

        $bekleyenVarMi=$rf->YetkiKontrol($robot[0]->yetkiSorgu,$robot[0]->yetkiYukle,$robot[0]->operatorAdi,$robot[0]->kullaniciId);
      
        if(count($bekleyenVarMi)>0)
        {
            //robot dolu olarak degistirldi
            $robotUpdate=Robot::where('id',$robot[0]->id)->first();
            $robotUpdate->mesgul=1;
            $robotUpdate->sonDegisiklikYapan="ApiSystem";
            $robotUpdate->save();//etkilenen satır sayısı kontrol edilecek
            //kayıt alındı olarak isaretlendi
            $req = Istek::where('id',$bekleyenVarMi[0]->id)->first();
            $req->robotAldi=1;
            $req->robotId=$robot[0]->id;
            $req->almaZamani=date('Y-m-d H:i:s', time());
            $req->save();
 

            DB::commit();

            return response()->json([
                "id"=> $bekleyenVarMi[0]->id,
                "number"=> $bekleyenVarMi[0]->tel,
                "type"=> $bekleyenVarMi[0]->tipAdi,
                "packet"=> $bekleyenVarMi[0]->adi,
                "code"=> $bekleyenVarMi[0]->kod,
                "operator"=>$bekleyenVarMi[0]->operatorAdi,
                "status"=>"true"
                
            ]);
        }
        else
        {
            DB::rollBack();
            return response()->json([
                
                "status"=>"false",
                "message"=>"bekleyen kayit yok"
                
            ]);
        }

        
       
    }
    public function setResponse(Request $request)
    {
        //http://localhost/SorguProject/public/api/Response?robotName=Robot1&password=123&id=2&response=11,5001&status=2
        $rf         = new RobotFunctions();
        $cf         = new CommonFunctions();
        $responses  = $request->input('response');
        $id         = $request->input('id');
        $status     = $request->input('status');
    
        try
        {
            
            $robot=$rf->GetRobot($request->input("robotName"),$request->input("password"));
            
            if(count($robot)<1)
            {
                return response()->json([
                    "status"=> "false",
                    "message"=>"giris basarisiz"
                ]);
            }
            DB::beginTransaction();
            
            $req = Istek::where('id',$id)->where("robotId",$robot[0]->id)->first();
            //ilgili kayda bu robot mu atanmıs kontrolu
            

            if($req->robotDondu==0 )
            {
                //robot bosta olarak atandı
                $robotUpdate=Robot::where('id',$robot[0]->id)->first();
                $robotUpdate->mesgul=0;
                $robotUpdate->sonDegisiklikYapan="Api/setResponse";
                $robotUpdate->save();
                //kayıt döndü olrak isaretlendi
                $req->robotDondu=1;
                $req->donmeZamani=date('Y-m-d H:i:s', time());
                $req->cevap=$responses;
                $req->durum=$status;
                $robotUpdate->sonDegisiklikYapan="Api/setResponse";
                $req->save();

                
                $cf->GelenPaketler($responses,$id);//gelen paketler split edildi ve gelenpaketler tablosuna yazıldı
                DB::commit();
            }
            else
            {  
                DB::rollBack();
                $req->denemeSayisi=$req->denemeSayisi+1;
                $req->save();
                return response()->json([
                    "status"=> "false",
                    "message"=>"aldigin paketin cavabini ver!"
                ]); 
            }
            

            return response()->json([
                "status"=> "true"
            ]);
        }
        catch(\Exception $e)
        {  
            DB::rollBack();
            return response()->json([
                "status"=> "false",
                "message"=>$e->getMessage()
            ]);
        }
       

        
    }
    public function PacketList(Request $request)
    {
        //http://localhost/SorguProject/public/api/Packets?operator=Turkcell
        
       $cf         = new CommonFunctions();
       $packets    = $cf->GetAvaiblePackets($request->input("operator"));
       $arr2       = array();
       foreach($packets as $packet)
       {
         $result=array(
             "id"=>$packet->id,
             "name"=>$packet->adi,
             "code"=>$packet->kod,
             "type"=>$packet->tipAdi,
             "amount"=>$packet->maliyetFiyati

         );
         array_push($arr2,$result);
       }
       $arr3=array("Results"=>$arr2);
       $finish =  json_encode($arr3,JSON_UNESCAPED_UNICODE);
       $finish = str_replace("\/","/",$finish);
       //sleep(1);
       return $finish;
       
      
    }
    public function NewPacket(Request $request)
    {
        //http://localhost/SorguProject/public/api/NewPacket?robotName=Robot1&password=123123&packetName=deneme2223&operator=Avea&type=ses
        $rf         = new RobotFunctions();
        $robot      = $rf->RobotLogin($request->input("robotName"),$request->input("password"));
        if(!$robot)
        {
            return response()->json([
                "status"=> "false"
            ]);
        }

        try
        {
            $packets = new Paket;
            $packets->yeni=1;
            $packets->adi=$request->input("packetName");
            $packets->kod=9999;
            $packets->tipId=Tip::where("adi",$request->input("type"))->first()->id;
            $packets->operatorId=Operator::where("adi",$request->input("operator"))->first()->id;
            $packets->sistemPaketKodu = Paket::find(DB::table('paket')->max('id'))->id+1;
            $packets->sonDegisiklikYapan=$request->input("robotName");

            $packets->save();
            return response()->json([
                "status"=> "true",
                "message"=>"Basarili"
            ]);
        }
        catch(\Exception $e)
        {
            $message=$e->getMessage();
            return response()->json([
                "status"=> "false",
                "message"=>$message
            ]);
        }
        
    }
    public function KayitBosalt(Request $request)
    {
        $cf = new CommonFunctions;
        $cf->RequestKayitBosalt($request->input("id"));
        return response()->json([
            "status"=> "true"
        ]);
    }
    
   
}


