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
use App\Classes\XmlRobotCevaplar;
use App\Classes\HesapIslemleri;
use Validator;
use Log;

use App\Classes\AveaProtokol;


class ServisAvea
{

    public function BaglantiDogrula(Request $request)
    {

        $bekleyenKontrol = Validator::make($request->all(),array(
            "u" => "min:1|required",
            "p" => "min:1|required",
            "operator" => "min:1|numeric|required",
            "count" => "min:1|numeric|required"
        ));

        if(($bekleyenKontrol->fails()))
        {
            return "ERR - HATALI API PROTOKOLU - BEKLEYEN";        
        }
        else
        {
            return $this->IslemGonder($request);
        }

        $cevapKontrol = Validator::make($request->all(),array(
            "u" => "min:1|required",
            "p" => "min:1|required",
            "refId" => "min:1|required",
            "status" => "min:1|required",
            "point" => "min:1|required",
            "cost" => "min:1|required",
            "charged" => "min:1|required",
            "desc" => "min:1|required"
        ));

        if (($cevapKontrol->fails()))
        {
            return "ERR - HATALI API PROTOKOLU - CEVAP";
        }
        else
        {
            return $this->CevapAl($request);
        }
        
    }

    public function IslemGonder(Request $request)
    {
        $robotAd = $request->u;
        $robotSifre = $request->p;

        $protokol = new AveaProtokol;
        $robotDogrula = $protokol->RobotDogrula($robotAd,$robotSifre);

        if (!($robotDogrula))
        {
            Log::info("ROBOT ISTEK - GIRIS BASARISIZ - Robot Adı:$robotAd Şifre:$robotSifre");
            return "ERR - ROBOT GIRISI BASARISIZ";
        }

        Log::info("ROBOT ISTEK - GIRIS OK - Robot Adı:$robotAd Şifre:$robotSifre");

        if($robotDogrula[0]->yetkiSorgu==0 && $robotDogrula[0]->yetkiYukle==0 && $robotDogrula[0]->yetkiFatura==0)
        {
            Log::info("ROBOT ISTEK - YETKI ERR - Robot Adı:$robotAd");
            return "ERR - ROBOT YETKISIZ ISTEK";
        }




    }

    public function CevapAl(Request $request)
    {

    }

    public function getNumbers(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
            "np"     		=> "required",
            "sifre"        	=> "required|min:1",
            "islem"      	=> "required",
            "operator" 		=> "required|max:30",
            "kullanici_adi" => "required|max:100"
            ));
        $cevaplar   = new XmlRobotCevaplar;

        if($kontrol->fails())
            $cevaplar->HataliBilgi();
        $cf         = new CommonFunctions();
        $rf         = new RobotFunctions();
        $robotAdi   = $request->input("kullanici_adi");
        $robotSifre = $request->input("sifre");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        Log::info("GELEN getNumbers-RobotAdi:$robotAdi sifre:$robotSifre");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        Log::info("getNumbers-Robot:$robotAdi Giriş Başarılı");

        if($robot[0]->yetkiSorgu==0 && $robot[0]->yetkiYukle==0 && $robot[0]->yetkiFatura==0)
        {
            Log::info("Robot:$robotAdi Yetki Yetersiz");
            return $cevaplar->YetkiYetersiz();
        }

        $bekleyenVarMi=$rf->BekleyenVarmi();
        if(count($bekleyenVarMi)==0 )
        {
            Log::info("getNumbers-Robot API Sistemde  Hiç Bekleyen Kayıt yok");
            return $cevaplar->BekleyenKayitYok($robot[0]->posBakiye );
        }
        /////
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
        $bekleyenVarMiFatura=array();
        $bekleyenVarMi=$rf->YetkiKontrol($robot);
        if(count($bekleyenVarMi)==0)
        {
            Log::info("getNumbers-Robot:$robotAdi yetkiKontorl Bekleyen YOK Kontor");
           
            Log::info("getNumbers-Robot:$robotAdi yetkiKontorl Bekleyen YOK Fatura");
            return $cevaplar->BekleyenKayitYok($robot[0]->posBakiye);
                
            
            
        }
        //ilgili id li Kayit icin select for update baslat 
       

        try//kontor
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
                return $cevaplar->BekleyenKayitYok($robot[0]->posBakiye);
            }
            DB::commit();
            Log::info("getNumbers-Robot:$robotAdi Kayit Cekme Basarili tel:".$bekleyenVarMi[0]->tel);
            return $cevaplar->BekleyenVar($bekleyenVarMi,$robot);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            Log::info("getNumbers-Robot:$robotAdi Hata Olustu hata:".$e->getMessage());
            return $cevaplar->BekleyenKayitYok($robot[0]->posBakiye);
        }
    }
    //http://localhost/RobotikSorgu/public/api/Response?robotName=Robot1&password=123&id=2&response=11,5001&status=2
    public function cevapRobot(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
            "np"     				=> "required",
            "sifre"        			=> "required",
            "id"      				=> "required|max:12",
            "bakiye"      			=> "required|max:50",
            "tutar"      			=> "required|max:10",
            "durum"      			=> "required|max:3",
            "referansno"    		=> "required|max:100",
            "npaket"      			=> "max:1000",
            "hata_aciklama" 		=> "max:1000",
            "operator" 				=> "required|max:30",
            "kullanici_adi" 		=> "required|max:100"
            ));
        $cevaplar   = new XmlRobotCevaplar;
        
        if($kontrol->fails())
            $cevaplar->HataliBilgi();
    
        $rf         = new RobotFunctions();
        $cf         = new CommonFunctions();
        $hesap      = new HesapIslemleri();
        $responses  = $request->input('npaket');
        $bakiye     = $request->input('bakiye');
        $id         = $request->input('id');
        $aciklama   = $request->input('hata_aciklama').$request->input('referansno').$request->input('no_sahibi');
        $status     = $request->input('durum');
        $robotAdi   = $request->input("kullanici_adi");
        $robotSifre = $request->input("sifre");
        $bakiye     = $request->input("bakiye");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        $now        = date('Y-m-d H:i:s', time());
        $tutar      = $request->input('tutar');

        if($responses=="5000")
            $responses=null;
        
        Log::info("GELEN cevapRobot-RobotAdi:$robotAdi sifre:$robotSifre id:$id status:$status responses:$responses bakiye:$bakiye aciklama:$aciklama");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();
        if($status==1 || $status==9)//onay
            $status=2;
        if($status==8 || $status==3)//iptal
            $status==3;
        if($status==4|| $status==5 || $status==6)//hatali
            $status=4;

        Log::info("cevapRobot-Robot:$robotAdi Giriş Başarılı");
        if($bakiye==null)
            $bakiye=0;
        else {
            $bakiye=$bakiye-(($bakiye/100)*2.5);
        }
      
       

        #region kontor
        //kontor
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
                              i.robotId=? 
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
            return $cevaplar->BasariliCevap($id); 
        }
        //cf sorgu hazırla
        $kullaniciPaketIadeVarMi=$rf->kullaniciPaketIadeVarMi($istek[0]->kullaniciId,$istek[0]->kod,$status);
        $robotBakiyeGuncellesinMi=$rf->robotBakiyeGuncellesinMi($istek[0]->kod,$status);
        $robotDursunMu=$rf->robotDursunMu($robot[0]->id,$status);
        $tutar=$tutar-(($tutar/100)*2.5);
        $tutar=number_format( $tutar, 2, '.', '');
        Log::info("cevapRobot-Robot:$robotAdi Hata id:$id HesaplananTutar=".$tutar."--");
        if($istek[0]->maliyetFiyati !=$tutar && $status==2)
            $this->FiyatBildirimi("firsat",$robot[0]->operatorAdi,$tutar,$istek[0]->adi,$istek[0]->tel,$robotAdi);
             
        $islemIade=1;
        $islemHesap=1;
        $islemIstek=1;
        $islemDurdur=1;
        try
        {
            DB::beginTransaction();

            if(count($kullaniciPaketIadeVarMi)!=0)
                $islemIade=$hesap->KullaniciPaketIade($kullaniciPaketIadeVarMi,$istek[0]->tel,$istek[0]->maliyetFiyati,$istek[0]->adi,$robot[0]->adi);

            if($robotBakiyeGuncellesinMi)
            {
                $islemHesap=$hesap->RobotPaketDus($robot,$istek[0]->tel,$istek[0]->maliyetFiyati,$istek[0]->adi,$robot[0]->adi);
               
            }
            $RobotPosBakiyeGuncelle=$hesap->RobotPosBakiyeGuncelle($robot[0]->id,$bakiye);   
            if($robotDursunMu && $robot[0]->yetkiYukle==1)
            {
                Log::info("cevapRobot-Robot:$robotAdi DURDURULDU hatali islem sayısı 3 e ulastı");
                $islemDurdur=$rf->robotYuklemeDurdur($robot[0]->id);
            }
                

            $islemIstek=DB::update("UPDATE istek SET robotDondu=1 , cevap=? , durum=? , donmeZamani=? , sonDegisiklikYapan=? , aciklama=? WHERE id=? ",
            array($responses,$status,$now,$robotAdi,$aciklama,$id));
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
                return $cevaplar->BasariliCevap($id);
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
        #endregion
        
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
        
        $kontrol = Validator::make($request->all(),array(
            "kullanici_adi"     => "required|max:20|min:3",
            "sifre"        		=> "required|max:100",
            "operator"			=> "required|max:20"
            ));
        $cf         = new CommonFunctions();
        $cevaplar   = new XmlRobotCevaplar;
        $packets    = $cf->FirsatPaketleri($request->input("operator"));
      
        if($kontrol->fails())
            $cevaplar->HataliBilgi();
        $rf         = new RobotFunctions();
        $robotAdi   = $request->input("kullanici_adi");
        $robotSifre = $request->input("sifre");
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        
        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        echo '<?xml version="1.0" encoding="utf-8" ?>';
        echo '<liste>';
        echo
        '<sonuc>
            <hata>0</hata>
        </sonuc>';
        foreach($packets as $packet)
        {
            echo "<firsat>";
            echo "<paket_adi>".$packet->adi."</paket_adi>";
            echo "<paket_id>".$packet->kod."</paket_id>";
            echo "</firsat>";
        }
        echo "</liste>";
       

       
      
    }

    private function FiyatBildirimi($tip,$operator,$fiyat,$adi,$tel,$robotAdi)
    {
        //http://localhost/RobotikSorgu/public/api/NewPacket?robotName=Robot1&password=123123&packetName=deneme2243&operator=Turkcell&type=ses&fiyat=11.22&no=5325446303
      

       
        Log::info("yeniFiyat-Robot:$robotAdi Giriş Başarılı");

        try
        {
            if($fiyat==null)
                $fiyat=0;

            if($fiyat!=0)
                $adi="fiyat-".$adi;
            $packets = new Paket;
            $packets->yeni=1;
            $packets->adi=$adi;
            $packets->kod=9999;
            $packets->tipId=Tip::where("adi",$tip)->first()->id;
            $packets->operatorId=Operator::where("adi",$operator)->first()->id;
            $packets->sistemPaketKodu = Paket::find(DB::table('paket')->max('id'))->id+1;
            $packets->sonDegisiklikYapan=$robotAdi." / ".$tel;
            $packets->resmiSatisFiyati=$fiyat;

            $packets->save();
            return true;

        }
        catch(\Exception $e)
        {
            $message=$e->getMessage();
            return false;
        }
        
    }
    function YeniPaketBildirimi(Request $request)
	{
		$robotAdi 				=	$request->input("kullanici_adi");
		$robotSifre				=	strtolower($request->input("sifre"));
		$ip 					=	$request->ip;
		$np						=	strtoupper($request->input("np"));
		$operator 				=	$request->input("operator");
		$paket_adi 				=	$request->input("paket_adi");
		$gsmno 					=	$request->input("gsmno");
        $cevaplar   = new XmlRobotCevaplar;
        $rf         = new RobotFunctions();
        $robot      = $rf->GetRobot($robotAdi,$robotSifre);
        $fiyat=0;
        

        Log::info("GELEN NewPacket-RobotAdi:$robotAdi sifre:$robotSifre");

        if(count($robot)==0)
            return $cevaplar->GirisBasarisiz();

        Log::info("NewPacket-Robot:$robotAdi Giriş Başarılı paketadi:--".$paket_adi."--");

        $this->FiyatBildirimi("firsat",$robot[0]->operatorAdi,$fiyat,$paket_adi,$gsmno,$robotAdi);
                
        echo "1";



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


