<?php
//Kullanıcılardan gelen yükleme isteklerini karşılar, exiptal ve bakiye sorgulama işlemlerini yerine getirir.
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Log;
use App\Classes\IstekKabul;
use App\Classes\BayiNoHareket;
use App\Classes\IstekIptalKontrol;
use App\Classes\CommonFunctions;
use App\Models\Paket;
class ServisGencan
{
    private function operatorTipAyristir($str)
    {
        $deger=strpos($str,"Turkcell");
        $len=strlen("Turkcell");
        if($deger===false)
        {
        $deger=strpos($str,"Avea");
        $len=strlen("Avea");
        }
        if($deger===false)
        {
        $deger=strpos($str,"Vodafone");
        $len=strlen("Vodafone");
        }
        if($deger===false)
            return;


        $operator= substr($str,0,$len);
        $tip=substr($str,$len,strlen($str)-$len);
        return array("tip"=>$tip,"operator"=>$operator);
    }
    public function istekYap(Request $request) //Yükleme isteklerini sağlar
    {

        //validation için tam kontrol yapılacak.###
    	$kontrol = Validator::make($request->all(),array(
	    "kullanici_adi" => "required|max:50|min:1",
	    "sifre"        	=> "required|max:30|min:1",
	    "operator"      => "required|max:20|min:1",
	    "tl"    	    => "required|max:10|min:1",
	    "numara"    	=> "required|max:10|min:10|regex:/(5)[0-9]{9}/",
        "yukleme_id"    => "required|max:30|min:1",
	    ));

        $IstekKabul 			= new IstekKabul();
        $bayiKodu				= $request->input("kullanici_adi");
        $sifre					= $request->input("sifre");
        $opTipStr				= ucfirst(strtolower($request->input("operator")));
        $kontor					= $request->input("tl");
        $gsmno					= $request->input("numara");
        $tekilNumara			= $request->input("yukleme_id");
        $siteAdres			    = $request->input("site");
        $ip 					= $_SERVER["REMOTE_ADDR"];
        $ayristir               = $this->operatorTipAyristir($opTipStr);
        $tip                    = strtolower ($ayristir["tip"]);
        $operator               = ucfirst($ayristir["operator"]);


  

        Log::info("GELEN istek GENCAN = bayikodu:$bayiKodu , sifre:$sifre ,opTipSTR:$opTipStr ,operator:$operator ,
         tip:$tip , kontor:$kontor , gsmno:$gsmno , siteadres:$siteAdres, tekilnumara:$tekilNumara , ip:$ip , zaman:".date("Y-m-d H:i:s"));
	    if($kontrol->fails())
	    	return "102#Eksik Veri";


        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"gencan");

        if($loginKontrol==null)
            return;

        if($loginKontrol[0]->aktif==0)
            return "112#0x0003 Lütfen bayiniz ile irtibata geçiniz";

        if(5000<=intval($kontor) && intval($kontor)<=6000)
        {
            if($loginKontrol[0]->yetkiSorgu!=1)
            {
                echo "117#Bu paket geçici bir süre sorgulamalara kapalı(Sorgu)";
                return ;
            }
        }
        else
        {
            if($loginKontrol[0]->yetkiYukle!=1)
            {
                echo "117#Bu paket geçici bir süre yüklemelere kapalı(Yukleme)";
                return ;
            }
        }

        $tekilnumaraCakisma	= DB::select('SELECT COUNT(id) as toplam FROM istek WHERE kullaniciId=? AND tekilNumara=?',array(  $loginKontrol[0]->id, $tekilNumara ));
        if($tekilnumaraCakisma[0]->toplam != 0)
        {
            echo "103#Aynı yukleme_id ile daha önce işlem yapılmış";
            return;
        }



        if ($request->input("bayi_id")!=null)
        {
            $altbayiNo = $request->input("bayi_id");
        }
        else
        {
            $altbayiNo = 0;
        }
        
        if($siteAdres == null || $siteAdres == "")
        {
            $siteAdres = "GENCAN";
        }
        // --- ***** ---
        // bayi_id



        // --- ***** ---



        $kulId = $loginKontrol[0]->id;
        // 10.05.2019 Tutar Resmi Satış Fiyatı
        // Bayi Satış Fiyat Tutar Değişikliği
        // tutar -> maliyetFiyati X resmiSatisFiyati OK
        // İptal edilmişti. Geri açıldı 26.05.2019
        $uygunMu=DB::select("SELECT
                                p.operatorId  ,
                                p.tipId ,
                                p.id ,
                                p.maliyetFiyati as tutar,
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
            echo "107#Yüklemek istediğiniz tutar aktif değil";
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
            echo "105#Yetersiz Bakiye";
            return;
        }

        $islem=0;
        $exIptal=$IstekKabul->exIptal($uygunMu[0]->operatorId,$tip,$gsmno,$loginKontrol[0]->id,$uygunMu[0]->id,$tekilNumara,$loginKontrol[0]->adi,$uygunMu[0]->kod);


        if($exIptal===null)
        {
            Log::info($gsmno.'112#0x0003 EXIPTAL'.date("Y-m-d H:i:s"));
            return "112#0x0003 Lütfen biraz sonra tekrar deneyin";
        }

        if(!$exIptal)
            $islem=$IstekKabul->kaydet($gsmno,$uygunMu,$loginKontrol,$tekilNumara,$altbayiNo,$siteAdres);
        else
            $islem=$IstekKabul->BayiHareketKaydi($uygunMu[0]->operatorNo."1" ,$loginKontrol[0]->takmaAd, $altbayiNo , $siteAdres);

        //Tüm bekleyenlerle ilgili değil yanlızca ilgili numöara için çalışacak.
        //iptal olupta sonradan yeniden işleme al yapılan numaralar bu komutun verildiği yerde işleme alınacak.
        if($islem===null)
        {
            Log::info($gsmno.'112#0x0003 ISLEM NULL'.date("Y-m-d H:i:s"));
            return "112#0x0003 Lütfen biraz sonra tekrar deneyin";
        }

        Log::info($gsmno.'sisteme istek alındı'.date("Y-m-d H:i:s"));
        echo "200#OK#".$uygunMu[0]->tutar."#".$yeterliBakiye;

    }
    public function istekKontrol(Request $request)
    {
    	$kontrol = Validator::make($request->all(),array(
	    "kullanici_adi" => "required|max:50|min:1",
	    "sifre"        	=> "required|max:30|min:1",
	    "yukleme_id"   => "required|max:30|min:1"
	    ));
        $bayiKodu		    = $request->input("kullanici_adi");
        $sifre			    = $request->input("sifre");
        $tekilnumara	    = $request->input("yukleme_id");
        $IstekKabul 		= new IstekKabul();
        $ip                 = $_SERVER["REMOTE_ADDR"];

        Log::info("GELEN GENCAN istekKontrol= bayikodu:$bayiKodu ,sifre:$sifre ,tekilno:$tekilnumara ,ip:$ip");
	    if($kontrol->fails())
	    	return "102#Eksik Veri";


        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"gencan");

        if($loginKontrol==null)
            return;

        if($loginKontrol[0]->aktif==0)
            return "112#0x0003 Lütfen bayiniz ile irtibata geçiniz";

        $takip=DB::select('SELECT durum,cevap,tel,paketId,aciklama,robotId FROM istek WHERE kullaniciId=? AND tekilNumara=? LIMIT 1',array($loginKontrol[0]->id , $tekilnumara));

        if(!$takip)
            return "103#Bu yukleme_id ile kayıt bulunamadı";
            
        
        $iptalKontrol = IstekIptalKontrol::IstekIptalKontrol($takip);

        if($iptalKontrol)
        {
            $takipIptal = DB::update("UPDATE istek SET durum=?,robotAldi=1,robotDondu=1, sonDegisiklikYapan=? WHERE id=? ",array(6,"SYSTEM IPTAL KONTROL",$takip[0]->id));

            $takip = DB::select('SELECT durum,cevap,tel,paketId,aciklama,robotId FROM istek WHERE id=? LIMIT 1',array($takip[0]->id));
        }
        //******************************* */
        
        $takipRobot = DB::select("SELECT yukleyici FROM robot WHERE id=?",array($takip[0]->robotId));

    	if (strpos($takip[0]->aciklama, 'GNC001') !== false) {
			// 'tekrar' kelimelerini sil
			$takip[0]->aciklama = str_replace('tekrar', '', $takip[0]->aciklama);
		}

        $operator=DB::select("SELECT p.operatorId FROM paket p  WHERE p.id=?",array($takip[0]->paketId));
        switch ($takip[0]->durum) {
            case '0':
                echo "105#Bekliyor";
                break;
            case '1':
                echo "105#Bekliyor";
                break;
            case '2':
                echo "104#Yüklendi -".$takip[0]->aciklama;
                break;
            case '3':
                if ($takipRobot[0]->yukleyici == 1)
                    echo "106#Hata:". $takip[0]->aciklama;
                else
                    $IstekKabul->kontrolCevap($takip[0]->cevap,"gencan",$operator[0]->operatorId);
                break;
            case '5':
                echo "106#". $takip[0]->aciklama;
                //echo "106#Hata:". $takip[0]->aciklama;
                //$IstekKabul->kontrolCevap("5000","gencan",$operator[0]->operatorId);
                break;
            case '6':
                echo "106#". $takip[0]->aciklama;
                break;
            case '7':
                $IstekKabul->sadeceRobotCevap($takip[0]->cevap.",".$takip[0]->cevap.",".$takip[0]->cevap,"gencan",$operator[0]->operatorId);
                break;
            default:
                echo "105#Bekliyor";
                break;
        }
    }
    public function bakiyeKontrol(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "kullanici_adi"   => "required|max:50|min:1",
        "sifre"           => "required|max:30|min:1"
        ));

        $IstekKabul 			= new IstekKabul();
        $bayiKodu				= $request->input("kullanici_adi");
        $sifre					= $request->input("sifre");
        $ip 					= $_SERVER["REMOTE_ADDR"];

        if($kontrol->fails())
            return "102#Eksik Veri";


        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"gencan");

        if($loginKontrol==null)
            return;

        if($loginKontrol[0]->aktif==0)
            return "112#0x0003 Lütfen bayiniz ile irtibata geçiniz";

        try
        {
            $bakiye=$loginKontrol[0]->bakiye;

            echo "OK#".number_format($bakiye,3,'.','');
        }
        catch (\Exception $e)
        {
            echo "OK#"."0.000";
        }
    }
    public function paketListesi(Request $request)
    {
        $kontrol = Validator::make($request->all(),array(
        "kullanici_adi"   => "required|max:50|min:1",
        "sifre"           => "required|max:30|min:1"
        ));

        $IstekKabul 			= new IstekKabul();
        $bayiKodu				= $request->input("kullanici_adi");
        $sifre					= $request->input("sifre");
        $ip 					= $_SERVER["REMOTE_ADDR"];
        $cf                     = new CommonFunctions;

        if($kontrol->fails())
            return "102#Eksik Veri";


        $loginKontrol= $IstekKabul->bayiLogin($bayiKodu,$sifre,$ip,"gencan");

        if($loginKontrol==null)
            return;

        if($loginKontrol[0]->aktif==0)
            return "112#0x0003 Lütfen bayiniz ile irtibata geçiniz";

        $packets    = $cf->GetAvaiblePackets("*");
        echo header("Content-type: text/xml");
        $xmlResponse="<tlList>";

        foreach ($packets as $packet ) //<![CDATA[a]]>
        {
            $adi=$packet->operatorAdi.$packet->tipAdi;
            $xmlResponse=$xmlResponse."<kontorler>";
            $xmlResponse=$xmlResponse. "<ID><![CDATA[$packet->id]]></ID>";
            $xmlResponse=$xmlResponse. "<opName><![CDATA[$adi]]></opName>";
            $xmlResponse=$xmlResponse. "<Tl><![CDATA[$packet->kod]]></Tl>";
            $xmlResponse=$xmlResponse. "<packageName><![CDATA[$packet->adi ($packet->kod)]]></packageName>";
            $xmlResponse=$xmlResponse. "<packageDefine><![CDATA[$packet->adi]]></packageDefine>";
            $xmlResponse=$xmlResponse. "<price><![CDATA[$packet->maliyetFiyati]]></price>";
            $xmlResponse=$xmlResponse. "<stdSales><![CDATA[$packet->resmiSatisFiyati]]></stdSales>";
            $xmlResponse=$xmlResponse. "<operator><![CDATA[$packet->operatorAdi]]></operator>";
            $xmlResponse=$xmlResponse. "<type><![CDATA[$packet->tipAdi]]></type>";
            $xmlResponse=$xmlResponse. "</kontorler>";
        }
        $xmlResponse=$xmlResponse. '</tlList>';

        print($xmlResponse);



    }
}
