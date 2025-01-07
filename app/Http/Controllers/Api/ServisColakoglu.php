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
class ServisColakoglu
{
    public function getParam(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "bayikodu"    	=> "required|max:50|min:1",
        "sifre"        	=> "required|max:30|min:1",
        "operator"      => "max:10|min:1",
        "tip"    		=> "max:30|min:1",
        "kontor"    	=> "max:10|min:1",
        "numara"    	=> "max:10|min:10",
        "takipno"   	=> "max:30|min:1",
        "islem"    		=> "required|max:30|min:1"
        ));
        $ip 					= $_SERVER["REMOTE_ADDR"];
		$IstekKabul 			= new IstekKabul();
		$bayiKodu				= $request->input("bayikodu");
		$sifre					= $request->input("sifre");
		$operator				= strtolower($request->input("operator"));
		$tip					= strtolower($request->input("tip"));
		$kontor					= strtolower($request->input("kontor"));
		$gsmno					= $request->input("numara");
		$tekilNumara			= $request->input("takipno");
        $islem			 		= $request->input("islem");
        
        Log::info("GELEN istek Colakoglu = bayikodu:$bayiKodu , sifre:$sifre , operator:$operator , 
        tip:$tip , kontor:$kontor , gsmno:$gsmno , tekilNumara:$tekilNumara , ip:$ip ");
        if($kontrol->fails())
            return "01";

        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"colakoglu");

        if($loginKontrol===null)
            return;
        
        if($loginKontrol[0]->aktif==0)
            return "02";
        
        switch ($islem) 
        {
            case 'talimat':
                $this->istekYap($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol);
            break;
            case 'takip':
                $this->istekKontrol($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol);
            break;
            case 'fiyatlar':
                $this->fiyatlar($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol);
            break;
            case 'kredi':
                $this->bakiyeKontrol($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol);
            break;
            case 'tumurunler':
                $this->paketListesi($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol);
            break;
            default:
            
            break;
        }
       
    }
    public function istekYap($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol)
    {
        $IstekKabul 			= new IstekKabul();
        if(5000<=intval($kontor) && intval($kontor)<=6000)
        {
            if($loginKontrol[0]->yetkiSorgu!=1)
            {
                echo "02";
                return ;
            }
        }
        else
        {
            if($loginKontrol[0]->yetkiYukle!=1)
            {
                echo "02";
                return ;
            }
        }
        
        $tekilNumaraCakisma	= DB::select('SELECT COUNT(id) as toplam FROM istek WHERE kullaniciId=? AND tekilNumara=?',array(  $loginKontrol[0]->id, $tekilNumara ));  
        if($tekilNumaraCakisma[0]->toplam != 0)
        {
            echo "08";
            return;
        }
        
        $kulId = $loginKontrol[0]->id;
        $uygunMu=DB::select("SELECT 
                                p.operatorId  , 
                                p.tipId ,
                                p.id ,
                                p.maliyetFiyati as tutar ,
                                p.kod ,
                                p.adi
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
            echo "09";
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
            echo "09";
            return;
        }
             
        $islem = 0;
        $exIptal=$IstekKabul->exIptal($uygunMu[0]->operatorId,$tip,$gsmno,$loginKontrol[0]->id,$uygunMu[0]->id,$tekilNumara,$loginKontrol[0]->adi,$uygunMu[0]->kod);

        if($exIptal===null)
            return "02:Sistemsel Hata Tekar Deneyin!";

        if(!$exIptal)
            $islem = $IstekKabul->kaydet($gsmno,$uygunMu,$loginKontrol,$tekilNumara);
        //Tüm bekleyenlerle ilgili değil yanlızca ilgili numöara için çalışacak.
        //iptal olupta sonradan yeniden işleme al yapılan numaralar bu komutun verildiği yerde işleme alınacak.

        // İstek Kabul Deadlock
        if($islem===null)
            return "02:Sistemsel Hata Tekar Deneyin!";

        return "OK";
        
	    
    }
    public function istekKontrol($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol)
    {
    	$IstekKabul 			= new IstekKabul();
        $takip=DB::select('SELECT durum,cevap,tel,paketId,aciklama FROM istek WHERE kullaniciId=? AND tekilNumara=? LIMIT 1',array($loginKontrol[0]->id , $tekilNumara));
        if($takip == null)
            return;
        $operator=DB::select("SELECT p.operatorId FROM paket p  WHERE p.id=?",array($takip[0]->paketId));

        switch ($takip[0]->durum) {
            case '0':
                echo "OK 2 Beklemede";
                break;
            case '1':
                echo "OK 2 Beklemede";
                break;
            case '2':
                $paket=Paket::Where("id",$takip[0]->paketId)->first();
                echo "OK 1 Kontor Yüklendi ".$takip[0]->aciklama.":".$paket->maliyetFiyati."";
                break;
            case '3':
                $IstekKabul->kontrolCevap($takip[0]->cevap,"colakoglu",$operator[0]->operatorId);
                break;
            case '5':
                $IstekKabul->kontrolCevap("5000","colakoglu",$operator[0]->operatorId);
                break;
            default:
                echo "OK 2 Beklemede";
                break;
        }
    }    
    public function bakiyeKontrol($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol)
    {
        try
        {
            $bakiye=$loginKontrol[0]->bakiye;
           
            echo number_format($bakiye,4,'.','').";";
        }
        catch (\Exception $e)
        {
            echo "0.0000".";";
        }
       
        
    }
    public function fiyatlar($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol)
    {
        $cf                     = new CommonFunctions();

        $packets    = $cf->GetAvaiblePackets("*");

        foreach ($packets as $packet ) 
        {
            echo ucfirst($packet->operatorAdi)."(".ucfirst($packet->operatorAdi).")[".$packet->kod."];".number_format($packet->maliyetFiyati,4)." ";
        }
    }
    public function paketListesi($ip,$bayiKodu,$sifre,$operator,$tip,$kontor,$gsmno,$tekilNumara,$islem,$loginKontrol)
    {
        $cf                     = new CommonFunctions();

        $packets    = $cf->GetAvaiblePackets("*");

        foreach ($packets as $packet ) 
        {
            echo "(".$packet->kod.") ".$packet->operatorAdi."-".$packet->tipAdi."-".$packet->adi." (".$packet->maliyetFiyati.")".";".$packet->operatorAdi.";".$packet->tipAdi.";".$packet->kod.".00;".number_format($packet->maliyetFiyati,4)."\r\n";
        }
    }
    
    
}
