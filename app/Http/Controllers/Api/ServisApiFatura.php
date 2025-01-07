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
use App\Classes\ServisApiCevaplar;
 
class ServisApiFatura
{
    public function istekYap(Request $request) //Yükleme isteklerini sağlar
    {
    	//validation için tam kontrol yapılacak.###
    	$kontrol = Validator::make($request->all(),array(
        "kullaniciAdi"              => "required|max:10|min:10",
	    "sifre"        	            => "required|max:200|min:1",
	    "kurumId"                   => "required|max:4|min:1",
	    "aboneAdi"    		        => "required|max:120|min:1",
	    "sonOdemeTarihi"    	    => "required|max:30|min:1",
        "telNo"    		            => "required|max:10|min:10",
        "faturaNo"    		        => "required|max:40|min:0",
        "faturaTutari"    		    => "required|max:20|min:0",
	    "id"                        => "required|max:50|min:1"
	    ));
		//kurum_kodu
        $IstekKabul 			= new IstekKabul();
        $cevap                  = new ServisApiCevaplar();
		$bayiKodu				= $request->input("kullaniciAdi");
		$sifre					= $request->input("sifre");
        $kurumId				= $request->input("kurumId");
        $aboneAdi				= $request->input("aboneAdi");
        $SonOdemeTarihi		    = $request->input("sonOdemeTarihi");
        $tel				    = $request->input("telNo");
        $faturaNo				= $request->input("faturaNo");
		$faturaTutari			= $request->input("faturaTutari");
		$tekilNumara			= $request->input("id");
        $ip 					= $_SERVER["REMOTE_ADDR"];

        Log::info("GELEN $request; istek ZNET Fatura = kod:$bayiKodu , sifre:$sifre , kurum_id:$kurumId , 
        aboneAdi:$aboneAdi , SonOdemeTarihi:$SonOdemeTarihi , tesisatNo:$tel , faturaNo:$faturaNo  , 
        faturaTutari:$faturaTutari  , tekilnumara:$tekilNumara , ip:$ip ");
        //dd($kontrol->errors());
	    if($kontrol->fails())
            return $cevap->EksikParametre();
        
        Log::info("Kontrol Basarili ");
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"ServisApi");
        Log::info("Login Basarili ");
        if($loginKontrol==null)
            return $cevap->GirisBasarisiz();
        
        if($loginKontrol[0]->aktif==0)
            return $cevap->YetkiYetersiz();

        if($loginKontrol[0]->yetkiFatura!=1)
            return $cevap->YetkiYetersiz();
        
        
        
        $tekilnumaraCakisma	= DB::select('SELECT COUNT(id) as toplam FROM istekfatura WHERE kullaniciId=? AND tekilNumara=?',array(  $loginKontrol[0]->id, $tekilNumara ));  
        if($tekilnumaraCakisma[0]->toplam != 0)
            return $cevap->IdCakismasi();
        
        $kulId = $loginKontrol[0]->id;
        $uygunMu=DB::select("SELECT * FROM kurum WHERE id=? LIMIT 1",array($kurumId));

        if(count($uygunMu)==0)
            return $cevap->KurumAktifDegil();

        $yeterliBakiye= $loginKontrol[0]->bakiye - $faturaTutari ;
        
        if($yeterliBakiye<0)
            return $cevap->BakiyeYetersiz();
             
        DB::beginTransaction();
        $islem=true;
        //kaydet islemi
        Log::info("KAYDET fatura gsmno:$tel,tutar:$faturaTutari , kurum=$kurumId , tekilNo=$tekilNumara ");
        try
        {
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
        }
        catch(\Exception $e)
        {
            $message=$e->getMessage();
            Log::info("Hata ServisApiFatura gsmno:$tel,   tekilNo=$tekilNumara hata:$message ");
            return $cevap->ErrorCevap("Sistemsel Hata Tekrar Deneyin!");
        }
        
       
        Log::info("KAYDET Fatura gsmno:$tel,   tekilNo=$tekilNumara ");

        
        $hesap=new HesapIslemleri;
        $islem=$hesap->KullaniciFaturaDus($loginKontrol,$tel,$faturaTutari,$faturaNo,$loginKontrol[0]->adi);
        if($islem)
        {
            DB::commit();
            return $cevap->BasariliCevap($faturaTutari);
        }
        else
        {
            DB::rollBack();
            return $cevap->ErrorCevap("Sistemsel Hata Tekrar Deneyin!");
        }
       
        //Tüm bekleyenlerle ilgili değil yanlızca ilgili numöara için çalışacak.
        //iptal olupta sonradan yeniden işleme al yapılan numaralar bu komutun verildiği yerde işleme alınacak.
        
        
	    
    }
    public function kurumListesi(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "kullaniciAdi"     => "required|max:11|min:10",
	    "sifre"            => "required|max:200|min:1",
	    
	    ));
		$IstekKabul 			= new IstekKabul();
        $cf                     = new CommonFunctions();
        $cevap                  = new ServisApiCevaplar();
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
	    
        $kurumlar    = DB::select("SELECT * FROM kurum");


        $arr2=array();
        foreach ($kurumlar as $kurum)
          {
            $result_bid= array(
              "id" => $kurum->id,
              "kurumKodu"=>$kurum->kod,
              "kurumAdi"=>$kurum->adi
            );
  
            array_push($arr2,$result_bid);
  
          }
          $arr3=array("kurumlar"=>$arr2);
  
          $finish =  json_encode($arr3);
          $finish = str_replace("\/","/",$finish);
          return $finish;
    }

   
    public function istekKontrol(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "kullaniciAdi"              => "required|max:11|min:10",
	    "sifre"        	            => "required|max:200|min:1",
	    "id"                        => "required|max:50|min:1"
	    ));
        $bayiKodu		    = $request->input("kullaniciAdi");
        $sifre			    = $request->input("sifre");
        $tekilnumara	    = $request->input("id");
        $IstekKabul 		= new IstekKabul();
        $cevap                  = new ServisApiCevaplar();
        $ip                 = $_SERVER["REMOTE_ADDR"];
        Log::info("GELEN ZNET Fatura istekKontrol= bayikodu:$bayiKodu ,sifre:$sifre ,tekilno:$tekilnumara ,ip:$ip");
	    if($kontrol->fails())
            return $cevap->EksikParametre();
        
        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"znet");

        if($loginKontrol==null)
            return $cevap->GirisBasarisiz();
    
        if($loginKontrol[0]->aktif==0)
            return $cevap->YetkiYetersiz();
        	
        $takip=DB::select('SELECT durum,tel,tutar FROM istekfatura WHERE kullaniciId=? AND tekilNumara=? LIMIT 1',array($loginKontrol[0]->id , $tekilnumara));
        if($takip == null)
        {
            Log::info("TekilNumaraYok = tel:$bayiKodu ,sifre:$sifre ,tekilnumara:$tekilnumara ");
            return $cevap->IdYok();
        }
            
        Log::info("Cevap Dondu = tel:$bayiKodu ,sifre:$sifre ,tekilnumara:$tekilnumara ");
        switch ($takip[0]->durum) {
            case '0':
                return $cevap->Islemde();
                break;
            case '1':
                return $cevap->Islemde();
                break;
            case '2':
                return $cevap->Yuklendi("",$takip[0]->tutar);
                break;
            case '3':
                return $cevap->Iptal();
                break;

            default:
                return $cevap->Islemde();
                break;
        }
    }    
}
