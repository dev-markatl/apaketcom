<?php
//Kullanıcılardan gelen yükleme isteklerini karşılar, exiptal ve bakiye sorgulama işlemlerini yerine getirir.
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Log;
use App\Classes\IstekKabul;
use App\Classes\HesapIslemleri;
use App\Classes\CommonFunctions;
use App\Models\Paket;
use App\Models\Istekfatura;
use App\Models\Kullanicihesaphareketleri;

class ServisZnetFatura
{
    public function istekYap(Request $request) //Yükleme isteklerini sağlar
    {
    	//validation için tam kontrol yapılacak.###
    	$kontrol = Validator::make($request->all(),array(
	   
	    "sifre"        	            => "required|max:200|min:1",
	    "kurum_id"                  => "required|max:4|min:1",
	    "abone_adi"    		        => "required|max:120|min:1",
	    "son_odeme_tarihi"    	    => "required|max:30|min:1",
        "tesisat_no"    		    => "required|max:10|min:10",
        "fatura_no"    		        => "required|max:40|min:0",
        "fatura_tutari"    		    => "required|max:20|min:0",
	    "tahsilat_api_islem_id"     => "required|max:50|min:1"
	    ));
		//kurum_kodu
		$IstekKabul 			= new IstekKabul();
		$bayiKodu				= $request->input("kod");
		$sifre					= $request->input("sifre");
        $kurumId				= $request->input("kurum_id");
        $aboneAdi				= $request->input("abone_adi");
        $SonOdemeTarihi		    = $request->input("son_odeme_tarihi");
        $tel				    = $request->input("tesisat_no");
        $faturaNo				= $request->input("fatura_no");
		$faturaTutari			= $request->input("fatura_tutari");
		$tekilNumara			= $request->input("tahsilat_api_islem_id");
        $ip 					= $_SERVER["REMOTE_ADDR"];
        
        if($bayiKodu==null)
            $bayiKodu=$request->input("kadi");


        
        Log::info("GELEN $request; istek ZNET Fatura = kod:$bayiKodu , sifre:$sifre , kurum_id:$kurumId , 
        aboneAdi:$aboneAdi , SonOdemeTarihi:$SonOdemeTarihi , tesisatNo:$tel , faturaNo:$faturaNo  , 
        faturaTutari:$faturaTutari  , tekilnumara:$tekilNumara , ip:$ip ");
        //dd($kontrol->errors());
	    if($kontrol->fails())
	    	return;
        
        Log::info("Kontrol Basarili ");
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"znet");
        Log::info("Login Basarili ");
        if($loginKontrol==null)
            return;
        
        if($loginKontrol[0]->aktif==0)
            return;

        
        if($loginKontrol[0]->yetkiFatura!=1)
        {
            echo "OK|3|Yetki Yetersiz! Yukleme|0.00";
            return ;
        }
        
        
        $tekilnumaraCakisma	= DB::select('SELECT COUNT(id) as toplam FROM istekfatura WHERE kullaniciId=? AND tekilNumara=?',array(  $loginKontrol[0]->id, $tekilNumara ));  
        if($tekilnumaraCakisma[0]->toplam != 0)
        {
            echo "4|Daha Önce Gönderilmiş (166189)";
            return;
        }
        
        $kulId = $loginKontrol[0]->id;
        $uygunMu=DB::select("SELECT * FROM kurum WHERE kod=? LIMIT 1",array($kurumId));
        if(count($uygunMu)==0)
        {
            echo "4|Aktif Kurum Bulunamadı.|0.00";
            return;
        }
        
        
        
        $yeterliBakiye= $loginKontrol[0]->bakiye - $faturaTutari ;
        

        if($yeterliBakiye<0)
        {
            echo "4|Bakiye Yetersiz|0.00";
            return;
        }
             
        DB::beginTransaction();
        $islem=true;
        //kaydet islemi
        Log::info("KAYDET fatura gsmno:$tel,tutar:$faturaTutari , kurum=$kurumId , tekilNo=$tekilNumara ");
        $kaydet= new Istekfatura;
        $kaydet->tel=$tel;
        $kaydet->robotId=1;
        $kaydet->robotDondu=0;
        $kaydet->kullaniciId=$loginKontrol[0]->id;
        $kaydet->tekilNumara=$tekilNumara;
        $kaydet->sonDegisiklikYapan=$loginKontrol[0]->adi;
        $kaydet->tutar=$faturaTutari;
        $kaydet->sonOdemeTarihi=$SonOdemeTarihi;
        $kaydet->aboneAdi=$aboneAdi;
        $kaydet->faturaNo=$faturaNo;
        $kaydet->tesisatNo=$tel;
        $kaydet->kurumId=$uygunMu[0]->id;
        $kaydet->save();
       
        Log::info("KAYDET Fatura gsmno:$tel,   tekilNo=$tekilNumara ");

        
        $hesap=new HesapIslemleri;
        $islem=$hesap->KullaniciFaturaDus($loginKontrol,$tel,$faturaTutari,$faturaNo,$loginKontrol[0]->adi);
        if($islem)
        {
            DB::commit();
            echo "OK|0|$yeterliBakiye|".$faturaTutari;
        }
        else
        {
            DB::rollBack();
            echo "OK|Sistemsel hata Tekrar Deneyin|0.00";
        }
       
        //Tüm bekleyenlerle ilgili değil yanlızca ilgili numöara için çalışacak.
        //iptal olupta sonradan yeniden işleme al yapılan numaralar bu komutun verildiği yerde işleme alınacak.
        
        
	    
    }
    public function kurumListesi(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "kod"     => "required|max:30|min:1",
	    "sifre"   => "required|max:200|min:1",
	    
	    ));
		$IstekKabul 			= new IstekKabul();
		$cf                     = new CommonFunctions();
		$bayiTel				= $request->input("kod");
		$sifre					= $request->input("sifre");
		$ip            			= $_SERVER["REMOTE_ADDR"];
		
        if($kontrol->fails())
	    	return;
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiTel,$sifre,$ip,"znet");

        if($loginKontrol==null)
            return;
    
        if($loginKontrol[0]->aktif==0)
            return;
	    
        $kurumlar    = DB::select("SELECT * FROM kurum");

        foreach ($kurumlar as $kurum ) 
        {
            echo $kurum->kod.",".$kurum->id.",".$kurum->adi.",0,0#";
        }
    }
    public function topluIstekKontrol(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
           
            "sifre"        	            => "required|max:200|min:1",
            "tahsilat_api_islem_id"     => "required|max:300|min:1"
            ));
        $bayiKodu		    = $request->input("kod");
        $sifre			    = $request->input("sifre");
        $tekilnumara	    = $request->input("tahsilat_api_islem_id");
        $IstekKabul 		= new IstekKabul();
        $ip                 = $_SERVER["REMOTE_ADDR"];

        if($bayiKodu==null)
            $bayiKodu=$request->input("kadi");
            
        Log::info("GELEN ZNET Fatura TOPLUistekKontrol= bayikodu:$bayiKodu ,sifre:$sifre ,tekilno:$tekilnumara ,ip:$ip");
        if($kontrol->fails())
	    	return;
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"znet");
        Log::info("GELEN ZNET Fatura TOPLUistekKontrol Login Kontorl");
        if($loginKontrol==null)
            return;
    
        if($loginKontrol[0]->aktif==0)
            return;
        Log::info("GELEN ZNET Fatura TOPLUistekKontrol login basarili");
        $idler=explode(',',$tekilnumara);
        $bindingsString = implode(',', array_fill(0, count($idler), '?'));
        array_push($idler,$loginKontrol[0]->id); 
        $takip=DB::select("SELECT durum,tel,tutar,tekilNumara FROM istekfatura WHERE  tekilNumara IN ( {$bindingsString} ) AND kullaniciId=? ",$idler);
        Log::info("GELEN ZNET Fatura TOPLUistekKontrol takip basarili");
        if($takip == null)
        {
            Log::info("TekilNumaraYok = tel:$bayiKodu ,sifre:$sifre ,tekilnumara:$tekilnumara ");
            return;
        }
        Log::info("Cevap Dondu = tel:$bayiKodu ,sifre:$sifre ,tekilnumara:$tekilnumara ");
        $cevap="";
        foreach($takip as $t)
        {
            switch ($takip[0]->durum) {
                case '0':
                    $cevap=$cevap.$t->tekilNumara."|1|";
                    break;
                case '1':
                    $cevap=$cevap.$t->tekilNumara."|1|";
                    break;
                case '2':
                    $cevap=$cevap.$t->tekilNumara."|2|";
                    break;
                case '3':
                    $cevap=$cevap.$t->tekilNumara."|3|";
                    break;
                default:
                    $cevap=$cevap.$t->tekilNumara."|2|";
                    break;
            }
            $cevap=$cevap."##";
        }
        echo $cevap;
    }
   
    public function istekKontrol(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "kod"                       => "required|max:50|min:1",
	    "sifre"        	            => "required|max:200|min:1",
	    "tahsilat_api_islem_id"     => "required|max:50|min:1"
	    ));
        $bayiKodu		    = $request->input("kod");
        $sifre=null;
        $sifre			    = $request->input("sifre");
        $tekilnumara	    = $request->input("tahsilat_api_islem_id");
        $IstekKabul 		= new IstekKabul();
        $ip                 = $_SERVER["REMOTE_ADDR"];
        Log::info("GELEN ZNET Fatura istekKontrol= bayikodu:$bayiKodu ,sifre:$sifre ,tekilno:$tekilnumara ,ip:$ip");
	    if($kontrol->fails())
	    	return;
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"znet");

        if($loginKontrol==null)
            return;
    
        if($loginKontrol[0]->aktif==0)
            return;
        	
        $takip=DB::select('SELECT durum,tel,tutar FROM istekfatura WHERE kullaniciId=? AND tekilNumara=? LIMIT 1',array($loginKontrol[0]->id , $tekilnumara));
        if($takip == null)
        {
            Log::info("TekilNumaraYok = tel:$bayiKodu ,sifre:$sifre ,tekilnumara:$tekilnumara ");
            return;
        }
            
        Log::info("Cevap Dondu = tel:$bayiKodu ,sifre:$sifre ,tekilnumara:$tekilnumara ");
        switch ($takip[0]->durum) {
            case '0':
                echo "1|islemde";
                break;
            case '1':
                echo "1|islemde";
                break;
            case '2':
                echo "2|Onay";
                break;
            case '3':
                echo "3|iptal";
                break;

            default:
                echo "2:islemde:";
                break;
        }
    }    
}
