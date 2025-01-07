<?php
//Kullanıcılardan gelen yükleme isteklerini karşılar, exiptal ve bakiye sorgulama işlemlerini yerine getirir.
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Log;
use App\Classes\IstekKabul;
use App\Classes\CommonFunctions;
use App\Models\Paket;
class ServisTemizer 
{
  
    public function istekYap(Request $request) //Yükleme isteklerini sağlar
    {
    	$kontrol = Validator::make($request->all(),array(
        "bayi_kodu"    	=> "required|max:50|min:1",
        "sifre"        	=> "required|max:30|min:1",
        "operator"      => "max:10|min:1",
        "tip"    		=> "max:30|min:1",
        "kontor"    	=> "max:10|min:1",
        "gsmno"    		=> "required|max:11|min:10",
        "tekilnumara"   => "max:30|min:1"
        ));
        Log::info("GELEN istek TEMİZER $request ");
		$IstekKabul 			= new IstekKabul();
		$bayiKodu				= $request->input("bayi_kodu");
		$sifre					= $request->input("sifre");
        $operator				= ucfirst(strtolower($request->input("operator")));
		$tip					= strtolower($request->input("tip"));
		$kontor					= ucfirst(strtolower($request->input("kontor")));
		$gsmno					= $request->input("gsmno");
		$tekilNumara			= $request->input("tekilnumara");
		$ip 					= $_SERVER["REMOTE_ADDR"];
        
        Log::info("GELEN istek Temizer = bayikodu:$bayiKodu , sifre:$sifre  ,operator:$operator ,
         tip:$tip , kontor:$kontor , gsmno:$gsmno , tekilnumara:$tekilNumara , ip:$ip  ");

        $sifirVarMi= substr($gsmno,0,1);
        if($sifirVarMi=="0")
            $gsmno= substr($gsmno,1,strlen($gsmno));

	    if($kontrol->fails())
	    	return "2:Eksik Veri";
        
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"temizer");

        if($loginKontrol==null)
            return;
        
        if($loginKontrol[0]->aktif==0)
            return "2:Lütfen Admin ile irtibata geçiniz";

        if(5000<=intval($kontor) && intval($kontor)<=6000)
        {
            if($loginKontrol[0]->yetkiSorgu!=1)
            {
                echo "2:Bu paket geçici bir süre sorgulamalara kapalı(Sorgu)";
                return ;
            }
        }
        else
        {
            if($loginKontrol[0]->yetkiYukle!=1)
            {
                echo "2:Bu paket geçici bir süre yüklemelere kapalı(Yukleme)";
                return ;
            }
        }
        
        $tekilnumaraCakisma	= DB::select('SELECT COUNT(id) as toplam FROM istek WHERE kullaniciId=? AND tekilNumara=?',array(  $loginKontrol[0]->id, $tekilNumara ));  
        if($tekilnumaraCakisma[0]->toplam != 0)
        {
            echo "8:Tekil numara kayitli .,.:";
            return;
        }
        
        $kulId = $loginKontrol[0]->id;
        $uygunMu=DB::select("SELECT 
                                p.operatorId  , 
                                p.tipId ,
                                p.id ,
                                p.maliyetFiyati as tutar ,
                                p.kod ,
                                p.adi,
                                o.id as operatorNo
                            FROM 
                                paket p , 
                                tip as t , 
                                operator as o 
                            WHERE
                                p.operatorId=o.id AND
                                p.tipId=t.id AND
                                o.adi=? AND
                                t.adi=? AND  
                                p.kod=? AND
                                p.aktif=1 AND
                                p.silindi=0 ",array($operator,$tip,$kontor));

        if(count($uygunMu) == 0)
        {
            echo "4:Hatali kontor miktari, satilmayan kontor miktari";
            return;
        }
        
        
        $yeterliBakiye=0;
        if(5000<=intval($kontor) && intval($kontor)<=6000)
        {
            $yeterliBakiye=$loginKontrol[0]->bakiye - $loginKontrol[0]->sorguUcret;
        }
        else
        {
            $yeterliBakiye= $loginKontrol[0]->bakiye - $uygunMu[0]->tutar ;
        }

        if($yeterliBakiye<0)
        {
            echo "2:Yetersiz Bakiye";
            return;
        }
             
        $islem = 0;
        $exIptal=$IstekKabul->exIptal($uygunMu[0]->operatorId,$tip,$gsmno,$loginKontrol[0]->id,$uygunMu[0]->id,$tekilNumara,$loginKontrol[0]->adi,$uygunMu[0]->kod);

        if($exIptal===null)
            return "2:Sistemsel hata. Lütfen daha sonra tekrar deneyin. (İstek Kabul)";
            

        if(!$exIptal)
            $islem = $IstekKabul->kaydet($gsmno,$uygunMu,$loginKontrol,$tekilNumara,1,"TEMIZER");
        else
            $islem=$IstekKabul->BayiHareketKaydi($uygunMu[0]->operatorNo."1" ,$loginKontrol[0]->takmaAd,1,"TEMIZER");
        //Tüm bekleyenlerle ilgili değil yanlızca ilgili numöara için çalışacak.
        //iptal olupta sonradan yeniden işleme al yapılan numaralar bu komutun verildiği yerde işleme alınacak.
        if($islem===null)
            return "2:Sistemsel hata. Lütfen daha sonra tekrar deneyin. (İstek Kabul)";
        Log::info($gsmno.'sisteme istek alındı'.date("Y-m-d H:i:s"));
        echo  "1:yukleme talebi islem listesine alindi:".$uygunMu[0]->tutar;
	    
    }
    public function istekKontrol(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "bayi_kodu" => "required|max:50|min:1",
	    "sifre"        	=> "required|max:30|min:1",
	    "tekilnumara"   => "required|max:30|min:1"
	    ));
        $bayiKodu		    = $request->input("bayi_kodu");
        $sifre			    = $request->input("sifre");
        $tekilnumara	    = $request->input("tekilnumara");
        $IstekKabul 		= new IstekKabul();
        $ip                 = $_SERVER["REMOTE_ADDR"];
       
        Log::info("GELEN Temizer istekKontrol= bayikodu:$bayiKodu ,sifre:$sifre ,tekilno:$tekilnumara ,ip:$ip");
	    if($kontrol->fails())
	    	return "102#Eksik Veri";
        
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"temizer");

        if($loginKontrol==null)
            return;
        
        if($loginKontrol[0]->aktif==0)
            return "2:Lütfen Admin ile irtibata geçiniz";
        	
        $takip=DB::select('SELECT durum,cevap,tel,paketId,aciklama FROM istek WHERE kullaniciId=? AND tekilNumara=? LIMIT 1',array($loginKontrol[0]->id , $tekilnumara));
        if($takip == null)
            return "2:Bu tekilnumara ile kayıt bulunamadı";
        $operator=DB::select("SELECT p.operatorId FROM paket p  WHERE p.id=?",array($takip[0]->paketId));
        switch ($takip[0]->durum) {
            case '0':
                echo "2:bekliyor";
                break;
            case '1':
                echo "2:bekliyor";
                break;
            case '2':
                $paket=Paket::Where("id",$takip[0]->paketId)->first();
                echo "1:Ref=".$takip[0]->aciklama."_Abone=Bilinmiyor:".$paket->maliyetFiyati;
                break;
            case '3':
                $IstekKabul->kontrolCevap($takip[0]->cevap,"temizer",$operator[0]->operatorId);
                break;
            case '5':
                $IstekKabul->kontrolCevap("5000","temizer",$operator[0]->operatorId);
                break;
            case '6':
                $IstekKabul->sadeceRobotCevap($takip[0]->aciklama,"temizer",$operator[0]->operatorId);
                break;
            default:
                echo "2:bekliyor";
                break;
        }
    }    
    public function bakiyeKontrol(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "bayi_kodu"   => "required|max:50|min:1",
        "sifre"           => "required|max:30|min:1"
        ));
        
        $IstekKabul 			= new IstekKabul();
        $bayiKodu				= $request->input("bayi_kodu");
        $sifre					= $request->input("sifre");
        $islem			 		= $request->input("islem");
        $ip 					= $_SERVER["REMOTE_ADDR"];

        if($kontrol->fails())
            return "2:Eksik Veri";
        
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"temizer");

        if($loginKontrol==null)
            return;
        
        if($loginKontrol[0]->aktif==0)
            return "2:Lütfen Admin ile irtibata geçiniz";

        try
        {
            $bakiye=$loginKontrol[0]->bakiye;
            echo "ok:".number_format($bakiye,4,'.','').":"."0000:0";
        }
        catch (\Exception $e)
        {
            echo "ok:0.0000:000:0";
        }
    }
    public function paketListesi(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "bayi_kodu"   => "required|max:50|min:1",
        "sifre"           => "required|max:30|min:1"
        ));
        
        $IstekKabul 			= new IstekKabul();
        $bayiKodu				= $request->input("bayi_kodu");
        $sifre					= $request->input("sifre");
        $ip 					= $_SERVER["REMOTE_ADDR"];
        $cf                     = new CommonFunctions;
        
        if($kontrol->fails())
            return "102#Eksik Veri";
        
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"temizer");

        if($loginKontrol==null)
            return;
        
        if($loginKontrol[0]->aktif==0)
            return "2:Lütfen Admin ile irtibata geçiniz";

        $packets    = $cf->GetAvaiblePackets("*");
        foreach ($packets as $packet ) //<![CDATA[a]]>
        {
            echo $packet->operatorAdi.";".$packet->adi.";".$packet->tipAdi.";".$packet->kod.";".number_format($packet->maliyetFiyati,4)."\r\n";

        }

        

        
    }
}
