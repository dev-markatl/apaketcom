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
use App\Classes\ApiRobotCevaplar;
use Validator;
use Log;

class RobotApi1
{
    public function PaketAyar(Request $request)
    {
        $packets="907,940,483,484,549,548";
        
        // $aa=DB::update("UPDATE genelayarlar SET olumsuzaTavsiyeDon =0 WHERE id>0 ");
        // echo $aa;
        // $paketUpdates=Paket::get();
        // foreach($paketUpdates as $paketUpdate)
        // {
        //     $paketUpdate->sistemPaketKodu=$paketUpdate->id;
        //     $paketUpdate->save();
        // }
        
    }
    public function getNumbers(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "robotName"     => "required|max:50|min:1",
        "password"      => "required|max:30|min:1"
        ));
        $cevaplar   = new ApiRobotCevaplar;

        if($kontrol->fails())
            $cevaplar->HataliBilgi();

        $rf         = new RobotFunctions();
        $robotAdi   = $request->input("robotName");
        $robotSifre = $request->input("password");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        Log::info("GELEN getNumbers-RobotAdi:$robotAdi sifre:$robotSifre");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        Log::info("getNumbers-Robot:$robotAdi Giriş Başarılı");

        if($robot[0]->yetkiSorgu==0 && $robot[0]->yetkiYukle==0)
        {
            Log::info("Robot:$robotAdi Yetki Yetersiz");
            return $cevaplar->YetkiYetersiz();
        }

        $bekleyenVarMi=$rf->BekleyenVarmi();
        if(count($bekleyenVarMi)==0)
        {
            Log::info("getNumbers-Robot API Sistemde Hiç Bekleyen Kayıt yok");
            return $cevaplar->BekleyenKayitYok();
        }
        
        if($robot[0]->mesgul!=0)
        {
            $busyRobot=$rf->BusyRobot($robot);
            if(count($busyRobot)==0)
            {
                DB::update("UPDATE robot SET mesgul=0 WHERE id=?",array($robot[0]->id));
                Log::info("getNumbers-Robot:$robotAdi Mesgul Düzeltildi!");
            }
            else
            {
                Log::info("getNumbers-Robot:$robotAdi Mesgul Geldi! tel:".$busyRobot[0]->tel);
                return $cevaplar->BekleyenVar($busyRobot,$robot);
            }   
        }
        $bekleyenVarMi=$rf->YetkiKontrol($robot);
        if(count($bekleyenVarMi)==0)
        {
            Log::info("getNumbers-Robot:$robotAdi yetkiKontorl Bekleyen YOK");
            return $cevaplar->BekleyenKayitYok();
        }
        //ilgili id li Kayit icin select for update baslat 
        try
        {
            $now=date('Y-m-d H:i:s', time());
            DB::beginTransaction();
            
            $KilitNoktasi=DB::select("SELECT * FROM istek WHERE id=? AND robotAldi=0 AND robotDondu=0 AND robotId=1 LIMIT 1 for UPDATE",array($bekleyenVarMi[0]->id));
            
            $robotUpdate=DB::update("UPDATE robot SET mesgul=1 , sonDegisiklikYapan=? WHERE id=?",array("RobotAPi",$robot[0]->id));
            $istekUpdate=DB::update("UPDATE 
                                        istek 
                                    SET 
                                        robotAldi=1 ,  
                                        durum=1 , 
                                        robotId=? ,
                                        almaZamani=? ,
                                        sonDegisiklikYapan=?
                                    WHERE id=? AND 
                                        robotAldi=0 AND 
                                        robotDondu=0 AND 
                                        durum=0 AND 
                                        robotId=1",
                                        array($robot[0]->id , $now , $robotAdi , $bekleyenVarMi[0]->id));
            if($istekUpdate!=1 || $robotUpdate!=1)
            {
                DB::rollBack();
                Log::info("getNumbers-Robot:$robotAdi updated row sayilari yanlis istek:$istekUpdate robotupdate:$robotUpdate");
                return $cevaplar->BekleyenKayitYok();
            }
            DB::commit();
            Log::info("getNumbers-Robot:$robotAdi Kayit Cekme Basarili tel:".$bekleyenVarMi[0]->tel);
            return $cevaplar->BekleyenVar($bekleyenVarMi,$robot);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            Log::info("getNumbers-Robot:$robotAdi Hata Olustu hata:".$e->getMessage());
            return $cevaplar->BekleyenKayitYok();
        }
    }
    //http://localhost/RobotikSorgu/public/api/Response?robotName=Robot1&password=123&id=2&response=11,5001&status=2
    public function cevapRobot(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "robotName"     => "required|max:50|min:1",
        "password"      => "required|max:30|min:1",
        "id"            => "required|max:30|min:1",
        "status"        => "required|max:2|min:1"
        ));
        $cevaplar   = new ApiRobotCevaplar;
        
        if($kontrol->fails())
            $cevaplar->HataliBilgi();
    
        $rf         = new RobotFunctions();
        $cf         = new CommonFunctions();
        $responses  = $request->input('response');
        $id         = $request->input('id');
        $status     = $request->input('status');
        $robotAdi   = $request->input("robotName");
        $robotSifre = $request->input("password");
        $bakiye     = $request->input("bakiye");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        $now        = date('Y-m-d H:i:s', time());
        Log::info("GELEN cevapRobot-RobotAdi:$robotAdi sifre:$robotSifre id:$id status:$status responses:$responses bakiye:$bakiye");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        Log::info("cevapRobot-Robot:$robotAdi Giriş Başarılı");
        $istek=DB::select("SELECT 
                            i.id , 
                            p.kod , 
                            p.maliyetFiyati ,
                            p.resmiSatisFiyati , 
                            i.kullaniciId , 
                            i.tel ,
                            i.paketId ,
                            i.robotDondu ,
                            i.robotAldi ,
                            i.denemeSayisi ,
                            p.adi
                        FROM istek i , 
                             paket p 
                        WHERE p.id=i.paketId AND 
                              i.id=? AND 
                              robotId=? 
                        LIMIT 1",array($id,$robot[0]->id));

        if(count($istek)==0)
        {
            Log::info("cevapRobot-Robot:$robotAdi  id:$id YANLİS CEVAP");
            return $cevaplar->HataliCevap();
        }
        $gelenPaketler=$cf->GelenPaketler($responses,$robot[0]->operatorId);
        if($istek[0]->robotDondu!=0)
        {
            Log::info("cevapRobot-Robot:$robotAdi Hata id:$id Bu kayita Daha önce cevap verilmis");
            DB::update("UPDATE istek SET denemeSayisi=? WHERE id=?",array($istek[0]->denemeSayisi + 1 , $id));
        }
        //cf sorgu hazırla
        $kullaniciPaketIadeVarMi=$rf->kullaniciPaketIadeVarMi($istek[0]->kullaniciId,$istek[0]->kod,$status);
        $robotBakiyeGuncellesinMi=$rf->robotBakiyeGuncellesinMi($istek[0]->kod,$status);
        $robotDursunMu=$rf->robotDursunMu($robot[0]->id,$status);
        $islemIade=1;
        $islemHesap=1;
        $islemIstek=1;
        $islemDurdur=1;
        try
        {
            DB::beginTransaction();
            if(count($kullaniciPaketIadeVarMi)!=0)
                $islemIade=$cf->ucretIadePaket($kullaniciPaketIadeVarMi,$istek);
            if($robotBakiyeGuncellesinMi)
                $islemHesap=$rf->RobotHesap($robot,$istek,$bakiye);
            if($robotDursunMu)
            {
                Log::info("cevapRobot-Robot:$robotAdi DURDURULDU hatali islem sayısı 3 e ulastı");
                $islemDurdur=$rf->robotDurdur($robot[0]->id);
            }
                

            $islemIstek=DB::update("UPDATE istek SET robotDondu=1 , cevap=? , durum=? , donmeZamani=? , sonDegisiklikYapan=? WHERE id=? ",array($responses,$status,$now,$robotAdi,$id));
            if(count($gelenPaketler)!=0)
            {
                foreach($gelenPaketler as $gelen)
                {
                    DB::insert("INSERT INTO istekcevap (istekId , paketId , sonDegisiklikYapan ) VALUES (?,?,?)",array($istek[0]->id , $gelen->id , $robotAdi));
                }
            }
            
            if($islemIstek==1 && $islemIade==1 && $islemHesap==1 && $islemDurdur==1)
            {
                DB::commit();
                Log::info("cevapRobot-Robot:$robotAdi Basarili islem Sonucu");
                return $cevaplar->BasariliCevap();
            }
            else
            {
                DB::rollBack();
                Log::info("cevapRobot-Robot:$robotAdi updated row sayilari yanlis islemIstek:$islemIstek islemIade:$islemIade islemHesap:$islemHesap");
                return $cevaplar->ErrorCevap("Hata Olustu");
            }
        }
        catch(\Exception $e)
        {
            return $cevaplar->ErrorCevap($e->getMessage());
        }
        
        
       //gelen cevap dogrumu kontrol et
       //dogru degil ise yanlis cevap de
       //dogru ise cevabı kaydet transaction kullan
       //robot hesap hareketleri guncellenecek 
       //eger yukleme talebi ise  ve olumsuz(3-4) dönüldü ise iade edilecek
       //eger sorgu talebi ise ve Hatali(4) dönüldü ise iade edilecek
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
             "amount"=>$packet->resmiSatisFiyati

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
        //http://localhost/RobotikSorgu/public/api/NewPacket?robotName=Robot1&password=123123&packetName=deneme2243&operator=Turkcell&type=ses&fiyat=11.22&no=5325446303
        $kontrol = Validator::make($request->all(),array(
        "robotName"     => "required|max:50|min:1",
        "password"      => "required|max:30|min:1"
        ));
        $cevaplar   = new ApiRobotCevaplar;

        if($kontrol->fails())
            $cevaplar->HataliBilgi();

        $rf         = new RobotFunctions();
        $robotAdi   = $request->input("robotName");
        $robotSifre = $request->input("password");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        $fiyat      = $request->input("fiyat");
        Log::info("GELEN NewPacket-RobotAdi:$robotAdi sifre:$robotSifre");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        Log::info("NewPacket-Robot:$robotAdi Giriş Başarılı");

        try
        {
            if($fiyat==null)
                $fiyat=0;

            $packets = new Paket;
            $packets->yeni=1;
            $packets->adi=$request->input("packetName");
            $packets->kod=9999;
            $packets->tipId=Tip::where("adi",$request->input("type"))->first()->id;
            $packets->operatorId=Operator::where("adi",$request->input("operator"))->first()->id;
            $packets->sistemPaketKodu = Paket::find(DB::table('paket')->max('id'))->id+1;
            $packets->sonDegisiklikYapan=$request->input("robotName")." / ".$request->input("no");
            $packets->resmiSatisFiyati=$request->input("fiyat");

            $packets->save();
            return $cevaplar->BasariliCevap();

        }
        catch(\Exception $e)
        {
            $message=$e->getMessage();
            return $cevaplar->ErrorCevap($message);
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


