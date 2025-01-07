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
class ServisZnet 
{
    public function istekYap(Request $request) //Yükleme isteklerini sağlar
    {
    	//validation için tam kontrol yapılacak.###
    	$kontrol = Validator::make($request->all(),array(
	    "bayi_kodu"     => "required|max:50|min:1",
	    "sifre"        	=> "required|max:30|min:1",
	    "operator"      => "required|max:10|min:1",
	    "tip"    		=> "required|max:30|min:1",
	    "kontor"    	=> "required|max:10|min:1",
	    "gsmno"    		=> "required|max:10|min:10|regex:/(5)[0-9]{9}/",
	    "tekilnumara"   => "required|max:30|min:1"
	    ));
		
		$IstekKabul 			= new IstekKabul();
		$bayiKodu				= $request->input("bayi_kodu");
		$sifre					= $request->input("sifre");
        $operator				= ucfirst(strtolower($request->input("operator")));
		$tip					= strtolower($request->input("tip"));
		$kontor					= ucfirst(strtolower($request->input("kontor")));
		$gsmno					= $request->input("gsmno");
		$tekilNumara			= $request->input("tekilnumara");
		$ip 					= $_SERVER["REMOTE_ADDR"];
        
        Log::info("GELEN istek ZNET = bayikodu:$bayiKodu , sifre:$sifre , operator:$operator , 
        tip:$tip , kontor:$kontor , gsmno:$gsmno , tekilnumara:$tekilNumara , ip:$ip , zaman:".date("Y-m-d H:i:s"));
	    if($kontrol->fails())
	    	return "OK|3|Eksik Parametre|0.00";
        
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"znet");

        if($loginKontrol==null)
            return;
        
        if($loginKontrol[0]->aktif==0)
            return;

        if(5000<=intval($kontor) && intval($kontor)<=6000)
        {
            if($loginKontrol[0]->yetkiSorgu!=1)
            {
                echo "OK|3|Yetki Yetersiz! Sorgu|0.00";
                return ;
            }
        }
        else
        {
            if($loginKontrol[0]->yetkiYukle!=1)
            {
                echo "OK|3|Yetki Yetersiz! Yukleme|0.00";
                return ;
            }
        }
        
        $tekilnumaraCakisma	= DB::select('SELECT COUNT(id) as toplam FROM istek WHERE kullaniciId=? AND tekilNumara=?',array(  $loginKontrol[0]->id, $tekilNumara ));  
        if($tekilnumaraCakisma[0]->toplam != 0)
        {
            echo "OK|3|Tekil numara çakışması.|0.00";
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
            echo "OK|3|Aktif Kontor Miktarı Bulunamadı.|0.00";
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
            echo "OK|3|Bakiye Yetersiz|0.00";
            return;
        }
             
        $islem = 0;
        $exIptal=$IstekKabul->exIptal($uygunMu[0]->operatorId,$tip,$gsmno,$loginKontrol[0]->id,$uygunMu[0]->id,$tekilNumara,$loginKontrol[0]->adi,$uygunMu[0]->kod);

        if($exIptal===null)
            return "OK|3|Sistemsel hata. Tekrar deneyiniz.|0.00";

        if(!$exIptal)
            $islem = $IstekKabul->kaydet($gsmno,$uygunMu,$loginKontrol,$tekilNumara,"1","ZNET");
        else
            $islem=$IstekKabul->BayiHareketKaydi($uygunMu[0]->operatorNo."1" ,$loginKontrol[0]->takmaAd, "1" , "ZNET");
        //Tüm bekleyenlerle ilgili değil yanlızca ilgili numöara için çalışacak.
        //iptal olupta sonradan yeniden işleme al yapılan numaralar bu komutun verildiği yerde işleme alınacak.

        if($islem===null)
            return "OK|3|Sistemsel hata. Tekrar deneyiniz.|0.00";

        echo "OK|1|Talebiniz İşleme Alınmıştır.|".$uygunMu[0]->tutar;
        
	    
    }
    public function bakiyeKontrol(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "sifre"       => "required|max:30|min:1"
        ));
        $IstekKabul 			= new IstekKabul();
        $bayiTel				= $request->input("bayi_kodu");
        if($bayiTel==null)
            $bayiTel=$request->input("kod");
		$sifre					= $request->input("sifre");
		$ip 					= $_SERVER["REMOTE_ADDR"];
        
        if($bayiTel==null)
            return;
            
        if($kontrol->fails())
	    	return;
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiTel,$sifre,$ip,"znet");

        if($loginKontrol==null)
            return;
    
        if($loginKontrol[0]->aktif==0)
            return;
        


        try
        {
            $bakiye=$loginKontrol[0]->bakiye;
           
            echo "OK|".number_format($bakiye,4,'.','')."|0.00";
        }
        catch (\Exception $e)
        {
            echo "OK|"."0.0000"."|0.00";
        }
       
        
    }
    public function paketListesi(Request $request)
    {
    	//$ip = $this->server->get('REMOTE_ADDR');
    	//Request::ip();
    	$kontrol = Validator::make($request->all(),array(
	    "bayi_kodu"     => "required|max:30|min:1",
	    "sifre"        	=> "required|max:100|min:1",
	    
	    ));
		$IstekKabul 			= new IstekKabul();
		$cf                     = new CommonFunctions();
		$bayiTel				= $request->input("bayi_kodu");
		$sifre					= $request->input("sifre");
		$ip            			= $_SERVER["REMOTE_ADDR"];
		
        if($kontrol->fails())
	    	return;
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiTel,$sifre,$ip,"znet");

        if($loginKontrol==null)
            return;
    
        if($loginKontrol[0]->aktif==0)
            return;
	    
        $packets    = $cf->GetAvaiblePackets("*");

        foreach ($packets as $packet ) 
        {
            echo ucfirst($packet->operatorAdi)."|".ucfirst($packet->tipAdi)."|".$packet->kod."|".number_format($packet->maliyetFiyati,4)."|".$packet->maliyetFiyati ."TL ".$packet->adi."|^\r\n";
        }
    }
    public function paketListesi2(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
            "bayi_kodu"     => "required|max:30|min:1",
            "sifre"        	=> "required|max:100|min:1",
            
            ));
            $IstekKabul 			= new IstekKabul();
            $cf                     = new CommonFunctions();
            $bayiTel				= $request->input("bayi_kodu");
            $sifre					= $request->input("sifre");
            $ip            			= $_SERVER["REMOTE_ADDR"];
            
            if($kontrol->fails())
                return;
            
            $loginKontrol= $IstekKabul->bayiLogin($bayiTel,$sifre,$ip,"znet");
    
            if($loginKontrol==null)
                return;
    
            if($loginKontrol[0]->aktif==0)
                return;
            
            $packets    = $cf->GetAvaiblePackets("*");

            foreach ($packets as $packet ) 
            {
                echo "".$packet->adi."(".$packet->kod.")".";".ucfirst($packet->operatorAdi).";".ucfirst($packet->tipAdi).";".$packet->kod.".00;".number_format($packet->maliyetFiyati,2)."\r\n";
            }
                    
    }
    public function istekKontrol(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "bayi_kodu"     => "required|max:50|min:1",
	    "sifre"        	=> "required|max:30|min:1",
	    "tekilnumara"   => "required|max:30|min:1"
	    ));
        $bayiKodu		    = $request->input("bayi_kodu");
        $sifre			    = $request->input("sifre");
        $tekilnumara	    = $request->input("tekilnumara");
        $IstekKabul 		= new IstekKabul();
        $ip                 = $_SERVER["REMOTE_ADDR"];
        Log::info("GELEN ZNET istekKontrol= bayikodu:$bayiKodu ,sifre:$sifre ,tekilno:$tekilnumara ,ip:$ip");
	    if($kontrol->fails())
	    	return;
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"znet");

        if($loginKontrol==null)
            return;
    
        if($loginKontrol[0]->aktif==0)
            return;
        	
        $takip=DB::select('SELECT durum,cevap,tel,paketId,aciklama FROM istek WHERE kullaniciId=? AND tekilNumara=? LIMIT 1',array($loginKontrol[0]->id , $tekilnumara));
        if($takip == null)
            return;
        $operator=DB::select("SELECT p.operatorId FROM paket p  WHERE p.id=?",array($takip[0]->paketId));
        switch ($takip[0]->durum) {
            case '0':
                echo "2:islemde:";
                break;
            case '1':
                echo "2:islemde:";
                break;
            case '2':
                $paket=Paket::Where("id",$takip[0]->paketId)->first();
                echo "1:Ref.No=".$takip[0]->aciklama."_Abone=Bilinmiyor:".$paket->maliyetFiyati;
                break;
            case '3':
                $IstekKabul->kontrolCevap($takip[0]->cevap,"znet",$operator[0]->operatorId);
                break;
            case '5':
                $IstekKabul->kontrolCevap("","znet",$operator[0]->operatorId);
                break;
            case '6':
                echo "3:".$takip[0]->aciklama;
                break;
            case '7':
                echo "3:".$takip[0]->aciklama;
            default:
                echo "2:islemde:";
                break;
        }
    }    
}
