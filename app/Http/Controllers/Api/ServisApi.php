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
use App\Classes\IstekKabul;
use App\Classes\ApiRobotCevaplar;
use App\Classes\ServisApiCevaplar;
use Validator;
use Log;

class ServisApi
{
    //istekyap
    //cevapDon
    //paketlistesi
    //bakiyeSorgula
    public function istekYap(Request $request) //Yükleme isteklerini sağlar
    {
    	//validation için tam kontrol yapılacak.###
    	$kontrol = Validator::make($request->all(),array(
	    "kullaniciAdi"  => "required|max:50|min:1",
	    "sifre"        	=> "required|max:30|min:1",
	    "operator"      => "required|max:10|min:1",
	    "tip"    		=> "required|max:30|min:1",
	    "kontor"    	=> "required|max:10|min:1",
	    "telNo"    		=> "required|max:10|min:10",
	    "id"            => "required|max:30|min:1"
	    ));
		
        $IstekKabul 			= new IstekKabul();
        $cevap                  = new ServisApiCevaplar();
		$bayiKodu				= $request->input("kullaniciAdi");
		$sifre					= $request->input("sifre");
        $operator				= ucfirst(strtolower($request->input("operator")));
		$tip					= strtolower($request->input("tip"));
		$kontor					= ucfirst(strtolower($request->input("kontor")));
		$gsmno					= $request->input("telNo");
		$tekilNumara			= $request->input("id");
		$ip 					= $_SERVER["REMOTE_ADDR"];
        
        Log::info("GELEN istek ServisApi = bayikodu:$bayiKodu , sifre:$sifre , operator:$operator , 
        tip:$tip , kontor:$kontor , gsmno:$gsmno , tekilnumara:$tekilNumara , ip:$ip ");
	    if($kontrol->fails())
            return $cevap->EksikParametre();
        
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"ServisApi");

        if($loginKontrol==null)
            return $cevap->GirisBasarisiz();
        
        if($loginKontrol[0]->aktif==0)
            return $cevap->YetkiYetersiz();

        if(5000<=intval($kontor) && intval($kontor)<=6000)
        {
            if($loginKontrol[0]->yetkiSorgu!=1)
            {
                return $cevap->YetkiYetersiz();
            }
        }
        else
        {
            if($loginKontrol[0]->yetkiYukle!=1)
            {
                return $cevap->YetkiYetersiz();
            }
        }
        
        $tekilnumaraCakisma	= DB::select('SELECT COUNT(id) as toplam FROM istek WHERE kullaniciId=? AND tekilNumara=?',array(  $loginKontrol[0]->id, $tekilNumara ));  
        if($tekilnumaraCakisma[0]->toplam != 0)
        {
            return $cevap->IdCakismasi();
        }
        
        $kulId = $loginKontrol[0]->id;
        /*
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
        */
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
            return $cevap->KontorAktifDegil();
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
            return $cevap->BakiyeYetersiz();
        }
             
        $islem = 0;
        $exIptal=$IstekKabul->exIptal($uygunMu[0]->operatorId,$tip,$gsmno,$loginKontrol[0]->id,$uygunMu[0]->id,$tekilNumara,$loginKontrol[0]->adi,$uygunMu[0]->kod);

        if($exIptal===null)
            return $cevap->ErrorCevap("Sunucu Hatasi Tekrar Deneyiniz!");

        if(!$exIptal)
            $islem = $IstekKabul->kaydet($gsmno,$uygunMu,$loginKontrol,$tekilNumara);
        //Tüm bekleyenlerle ilgili değil yanlızca ilgili numöara için çalışacak.
        //iptal olupta sonradan yeniden işleme al yapılan numaralar bu komutun verildiği yerde işleme alınacak.

        if($islem===null)
        return $cevap->ErrorCevap("Sunucu Hatasi Tekrar Deneyiniz!");


        return $cevap->BasariliCevap($uygunMu[0]->tutar);
        
	    
    }
    public function bakiyeKontrol(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
            "kullaniciAdi"  => "required|max:50|min:1",
	        "sifre"         => "required|max:30|min:1"
        ));
        $cevap                  = new ServisApiCevaplar();
        $IstekKabul 			= new IstekKabul();
        $bayiTel				= $request->input("kullaniciAdi");
		$sifre					= $request->input("sifre");
		$ip 					= $_SERVER["REMOTE_ADDR"];

            
        if($kontrol->fails())
	    	return $cevap->EksikParametre();
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiTel,$sifre,$ip,"ServisApi");

        if($loginKontrol==null)
            return $cevap->GirisBasarisiz();
    
        if($loginKontrol[0]->aktif==0)
            return $cevap->GirisBasarisiz();

        


        try
        {
            $bakiye=$loginKontrol[0]->bakiye;
            return $cevap->Bakiye($bakiye);

        }
        catch (\Exception $e)
        {
            return $cevap->Bakiye("0");
        }
       
        
    }
    public function paketListesi(Request $request)
    {
    	//$ip = $this->server->get('REMOTE_ADDR');
    	//Request::ip();
    	$kontrol = Validator::make($request->all(),array(
            "kullaniciAdi"  => "required|max:50|min:1",
	        "sifre"         => "required|max:30|min:1"
	    
        ));
        $cevap                  = new ServisApiCevaplar();
		$IstekKabul 			= new IstekKabul();
		$cf                     = new CommonFunctions();
		$bayiTel				= $request->input("kullaniciAdi");
		$sifre					= $request->input("sifre");
		$ip            			= $_SERVER["REMOTE_ADDR"];
		
        if($kontrol->fails())
            return $cevap->EksikParametre();
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiTel,$sifre,$ip,"ServisApi");

        if($loginKontrol==null)
            return $cevap->GirisBasarisiz();
    
        if($loginKontrol[0]->aktif==0)
            return $cevap->YetkiYetersiz();
	    
        $packets    = $cf->GetAvaiblePackets("*");

        $arr2=array();
        foreach ($packets as $packet)
          {
            $result_bid= array(
              "id" => $packet->kod,
              "operatorAdi"=>$packet->operatorAdi,
              "tipAdi"=>$packet->tipAdi,
              "fiyati"=>$packet->maliyetFiyati,
              "paketAdi"=>$packet->adi
            );
  
            array_push($arr2,$result_bid);
  
          }
          $arr3=array("paketler"=>$arr2);
  
          $finish =  json_encode($arr3);
          $finish = str_replace("\/","/",$finish);
          return $finish;
    }
    public function istekKontrol(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "kullaniciAdi"  => "required|max:50|min:1",
	    "sifre"        	=> "required|max:30|min:1",
	    "id"            => "required|max:30|min:1"
        ));
        $cevap              = new ServisApiCevaplar();
        $bayiKodu		    = $request->input("kullaniciAdi");
        $sifre			    = $request->input("sifre");
        $tekilnumara	    = $request->input("id");
        $IstekKabul 		= new IstekKabul();
        $ip                 = $_SERVER["REMOTE_ADDR"];
        Log::info("GELEN ServisApi istekKontrol= bayikodu:$bayiKodu ,sifre:$sifre ,tekilno:$tekilnumara ,ip:$ip");
	    if($kontrol->fails())
            return $cevap->EksikParametre();

        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"ServisApi");

        if($loginKontrol==null)
            return $cevap->GirisBasarisiz();
    
        if($loginKontrol[0]->aktif==0)
            return $cevap->GirisBasarisiz();
        	
        $takip=DB::select('SELECT durum,cevap,tel,paketId,aciklama FROM istek WHERE kullaniciId=? AND tekilNumara=? LIMIT 1',array($loginKontrol[0]->id , $tekilnumara));
        if($takip == null)
            return $cevap->ErrorCevap("Id uygun degil");
        $operator=DB::select("SELECT p.operatorId FROM paket p  WHERE p.id=?",array($takip[0]->paketId));
        switch ($takip[0]->durum) {
            case '0':
                return $cevap->Islemde();
                break;
            case '1':
                return $cevap->Islemde();
                break;
            case '2':
                $paket=Paket::Where("id",$takip[0]->paketId)->first();
                return $cevap->Yuklendi($takip[0]->aciklama,$paket->maliyetFiyati);
                break;
            case '3':
                return $IstekKabul->kontrolCevap($takip[0]->cevap,"ServisApi",$operator[0]->operatorId);
                break;
            case '5':
                return $IstekKabul->kontrolCevap("","ServisApi",$operator[0]->operatorId);
                break;
            default:
                return $cevap->Islemde();
                break;
        }
    }
    
    
   
}


