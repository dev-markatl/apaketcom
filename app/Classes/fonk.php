<?php 
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
//version 1.00 -3.04.2017 
class fonk
{

  function hata($mesaj)
  {
    return '<div class="alert alert-danger alert-dismissable" style="position:absolute; z-index: 2; width:100%; margin-left: auto; margin-right: auto;height:200px; margin-top:10%;">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <div style="margin-left:0%; font-size:20px;" ><strong>'.$mesaj.'</strong> </div>
      </div>';
  }
  function basari($mesaj)
  {
    return '<div class="alert alert-success alert-dismissable" style="position:absolute; z-index: 2; width:100%; margin-left: auto; margin-right: auto;height:200px; margin-top:10%;">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <div style="margin-left:0%; font-size:20px;" ><strong>'.$mesaj.'</strong> </div>
      </div>';
  }


  function yuklenen_paket($takip_id)
  {
    $yuklenen_paket=DB::select("SELECT takip.tutar AS takip_tutar, fiyat.tutar, ut_gezdirme_takip.takip_id ,kt_paket.* FROM fiyat,takip, ut_gezdirme_takip , kt_paket WHERE takip.id=?  AND (ut_gezdirme_takip.durum='1' OR ut_gezdirme_takip.durum='9' ) AND ut_gezdirme_takip.bot_durum = '2' AND takip.id=ut_gezdirme_takip.takip_id AND takip.kul_id=fiyat.kul_id AND kt_paket.id=fiyat.paket_id AND ut_gezdirme_takip.api_id=kt_paket.api_id  AND ut_gezdirme_takip.tip=kt_paket.tip AND takip.operator=kt_paket.operator ",array($takip_id));

    $toplam_tutar=0;
    $toplam_maliyet=0;
    $takip_tutar=0;
    foreach ($yuklenen_paket as $key ) 
    {
      $toplam_tutar=$toplam_tutar+$key->tutar;
      $toplam_maliyet=$toplam_maliyet+$key->maliyet;
      $takip_tutar=$takip_tutar+$key->takip_tutar;
    }
    if($yuklenen_paket!=null)
    {
      $dizi=array($yuklenen_paket[0]);
      $dizi[0]->tutar=$toplam_tutar;
      $dizi[0]->maliyet=$toplam_maliyet;
      $dizi[0]->takip_tutar=$takip_tutar;
    }
    else
      $dizi=array();
    
    
    
    
    //$dizi[0]->maliyet=$toplam_maliyet;
    return $dizi;
  }

  function ip_oku($str,$ip)
  {

    $rtrn  = 0;
    if($str!="" && $str!=null )
    {
      $konum=strpos((string)$str,(string)$ip);
      
    }
    else
      $konum=false;
    
    
    if($konum!==false)
      $rtrn = 1;
    
    if($str=="" || $str==null )
      $rtrn=1;


      if($rtrn ==1)
      {
        return 1;
      }
      else
      {
        return 0;
      }
  }

  function bakiye_guncelle ($aciklama,$ekle_cikar,$yukleme_turu,$np,$kul_id,$tutar,$e_bakiye=0)
  {
    $tutar=number_format($tutar,4,".","");
    
    $o_bakiye=DB::select("SELECT sistem_bakiye , bakiye FROM bot WHERE kul_id=? AND np=?  ",array($kul_id,$np));
    if($ekle_cikar == "1")
    {
      $update=DB::update("UPDATE bot SET sistem_bakiye=(sistem_bakiye+?)   WHERE kul_id=? AND np=? ",array($tutar,$kul_id,$np));
      $s_bakiye=$tutar+$o_bakiye[0]->sistem_bakiye;
    }
    elseif($ekle_cikar == "0")
    {
      $update=DB::update("UPDATE bot SET sistem_bakiye=(sistem_bakiye-?)   WHERE kul_id=? AND np=?  ",array($tutar,$kul_id,$np,$o_bakiye[0]->sistem_bakiye));
      $s_bakiye=$o_bakiye[0]->sistem_bakiye-$tutar;
    }
    if($yukleme_turu=="0")
    {
      if($e_bakiye==0)
        $e_bakiye=$o_bakiye[0]->bakiye;
      //bot feedbacke alınabilirmi??
      $sorgu=DB::select("SELECT maliyet,takip_id FROM ut_gezdirme_takip WHERE takip_id=(SELECT takip_id FROM ut_gezdirme_takip WHERE id=?) AND (durum=1 OR durum=9) ",array($aciklama));
      $toplam_maliyet=0;
      $takip_id=0;
      foreach ($sorgu as $key ) 
      {
        $takip_id=$key->takip_id;
        $toplam_maliyet= $toplam_maliyet + $key->maliyet;
      }
      $islem=DB::update("UPDATE takip SET bot_kodu=?, tutar=? WHERE id=?",array($np,$toplam_maliyet,$takip_id));
      Log::info('update icerigi '.$np.'  '.$toplam_maliyet.'  '.$takip_id);
      $islem_2=DB::update("UPDATE ut_gezdirme_takip SET o_bakiye=? ,s_bakiye=? , e_bakiye=? WHERE id=?  ",array($o_bakiye[0]->sistem_bakiye,$s_bakiye,$e_bakiye,$aciklama));
    }
    else
    {
      $islem=DB::insert("INSERT INTO ut_raporlar (bot_np , tutar , o_bakiye , s_bakiye , aciklama , yukleme_turu ,ekle_cikar,e_bakiye) VALUES(?,?,?,?,?,?,?,?)",array($np,$tutar,$o_bakiye[0]->sistem_bakiye,$s_bakiye,$aciklama,$yukleme_turu,$ekle_cikar,$o_bakiye[0]->bakiye));
    }


  }

  function kullanici_bakiyesi($kul_id)
  {
    $toplam["avea"]=0;
    $toplam["vodafone"]=0;
    $kontrol=DB::select("SELECT avea_bakiye ,vodafone_bakiye FROM kullanicilar WHERE id=? ",array($kul_id));
    $sorgu = DB::select("SELECT sistem_bakiye FROM bot WHERE kul_id=? AND aktif='1' AND operator=?",array($kul_id,"avea"));
    foreach ($sorgu as $key ) 
    {
     $toplam["avea"]=$key->sistem_bakiye+$toplam["avea"];
    } 
    if($toplam["avea"]!=$kontrol[0]->avea_bakiye)
      $guncelle=DB::update("UPDATE kullanicilar SET avea_bakiye=? WHERE id=? ",array($toplam["avea"],$kul_id ));
    

    $sorgu = DB::select("SELECT sistem_bakiye FROM bot WHERE kul_id=? AND aktif='1' AND operator=?",array($kul_id,"vodafone"));
    foreach ($sorgu as $key ) 
    {
     $toplam["vodafone"]=$key->sistem_bakiye+$toplam["vodafone"];
    }
    $toplam["vodafone"]=$toplam["vodafone"]*0.975;
    if($toplam["vodafone"]!=$kontrol[0]->vodafone_bakiye)
      $guncelle=DB::update("UPDATE kullanicilar SET vodafone_bakiye=? WHERE id=?  ",array($toplam["vodafone"],$kul_id ));

    if($toplam["avea"]!=$kontrol[0]->avea_bakiye || $toplam["vodafone"]!=$kontrol[0]->vodafone_bakiye)
      $guncelle=DB::update("UPDATE kullanicilar SET bakiye=? WHERE id=?",array($toplam["vodafone"]+$toplam["avea"],$kul_id));
    
    return $toplam;
  }



  function bot_giris($ip,$np,$operator,$kul_adi,$sifre)
  {

  $sorgu_bilesenleri="SELECT bot.* ,kullanicilar.avea_sifre,kullanicilar.vodafone_sifre ,kullanicilar.sifre AS kul_sifre  FROM bot,kullanicilar WHERE bot.np=? AND kullanicilar.id=bot.kul_id AND bot.aktif=1 AND kullanicilar.aktif=1 ";
  $sorgu_degiskenleri=array($np);
  $giris=null;

  if($operator=="avea")
  {
    $sorgu_bilesenleri=$sorgu_bilesenleri."AND( kullanicilar.avea_kul_adi =? OR kullanicilar.kul_adi=? ) AND kullanicilar.avea=1";
    array_push($sorgu_degiskenleri, $kul_adi, $kul_adi);
     $giris=DB::select($sorgu_bilesenleri,$sorgu_degiskenleri);


  }
  elseif($operator=="vodafone")
  {
    $sorgu_bilesenleri=$sorgu_bilesenleri."AND ( kullanicilar.vodafone_kul_adi =? OR kullanicilar.kul_adi=? )  AND kullanicilar.vodafone=1 ";
    array_push($sorgu_degiskenleri, $kul_adi,$kul_adi);
     $giris=DB::select($sorgu_bilesenleri,$sorgu_degiskenleri);

  }
    
   //$giris=DB::select($sorgu_bilesenleri,$sorgu_degiskenleri);

    if($giris != null)
    {
      $dizi["id"]       = $giris[0]->id;
      $dizi["ip"]       = $giris[0]->ip;
      $dizi["np"]       = $giris[0]->np;
      $dizi["sifre"]    = $giris[0]->sifre;
      $dizi["bakiye"]   = $giris[0]->bakiye;
      $dizi["kul_id"]   = $giris[0]->kul_id;
      $dizi["aktif"]    = $giris[0]->aktif;
      $dizi["giris"]    = 0;
      $dizi["operator"] = $giris[0]->operator;
      $dizi["tam"]      =$giris[0]->tam;
      $dizi["ses"]      =$giris[0]->ses;
      $dizi["3_gcep"]   =$giris[0]->_3_gcep;
      $dizi["sms"]      =$giris[0]->sms;
      $dizi["yds"]      =$giris[0]->yds;
      $dizi["firsat"]   =$giris[0]->firsat;
      $dizi["sorgu"]    =$giris[0]->sorgu;
      $dizi["fark"]     =$giris[0]->fark;
      $dizi["sistem_bakiye"]     =$giris[0]->sistem_bakiye;

      if($dizi["id"] != "" && $dizi["id"] != null )
      {
        $dizi["giris"]  =   1;
        
      }
      else
      {

        $dizi["giris"]  =   0;
        
      }
    }
    else
    {
      $dizi["giris"]  =   0;
    }
    


  return $dizi;
  }

  function gezdirme_cek($kul_id,$klon_no,$kt_paket_id,$sahte_id)
  {
    $sorgu=DB::select("SELECT fiyat.tutar ,  s.paket, s.id AS k_id ,s.znet_id, s.gun , s.h_y_ses ,s.h_y_sms ,s.s_i_ses , s.s_i_sms , s.internet , s.site_tutar ,u.gezdir, u.klon_no,u.aktif, s.operator ,s.tip ,u.kul_id, u.id AS u_id, u.gezdirme_id , u.kt_paket_id , u.sira , u.sahte_id FROM fiyat, kt_paket AS s LEFT JOIN ut_gezdirme AS u ON s.id=u.gezdirme_id  AND u.kul_id=? WHERE  u.id not in (SELECT id FROM ut_gezdirme WHERE aktif=0)AND fiyat.paket_id=s.id AND u.klon_no = ? AND u.kt_paket_id = ? AND u.sahte_id = ? AND fiyat.kul_id =? ORDER BY u.sira ASC ",array($kul_id,$klon_no,$kt_paket_id,$sahte_id,$kul_id));
    return $sorgu;
  }






  function id_esle($gelen_id,$tip,$operator)
  {
    $gelen_id = number_format($gelen_id,0,'.','');
    $kt_paket = DB::select('SELECT api_id FROM kt_paket WHERE znet_id =? AND operator=? AND tip =? LIMIT 1',array($gelen_id,$operator,$tip));
   
    return $kt_paket[0]->api_id;
  }



  function n_paket_cek($api_id , $kul_id)
  {
    $fiyatcek         =  DB::select('SELECT fiyat.tutar , kt_paket.* FROM fiyat,kt_paket  WHERE fiyat.paket_id=kt_paket.id AND kt_paket.api_id = ? AND fiyat.kul_id= ?',array($api_id,$kul_id));
      return $fiyatcek;
  }

  function paket_ozellikleri($kontor,$tip,$operator,$kul_id)
  {
    $kontor           =  number_format($kontor,0,'','');
    $fiyatcek         =  DB::select('SELECT fiyat.tutar , kt_paket.*  FROM fiyat,kt_paket ,ut_gezdirme WHERE fiyat.paket_id=kt_paket.id AND ut_gezdirme.kt_paket_id = kt_paket.id AND kt_paket.tip=? AND ut_gezdirme.sahte_id=? AND kt_paket.operator=? AND ut_gezdirme.kul_id=? AND fiyat.kul_id=? AND ut_gezdirme.sira=1',array($tip,$kontor,$operator,$kul_id,$kul_id));
    return $fiyatcek;
  }

  //gezdirme takip id
  function idden_paket($id)
  {
    for ($i=0; $i < 5; $i++) 
    { 
      try
      {
        $paket_oz         =  DB::select('SELECT takip.gsmno , kt_paket.*, ut_gezdirme_takip.takip_id, ut_gezdirme_takip.api_id, ut_gezdirme_takip.tip, ut_gezdirme_takip.bot_kodu, ut_gezdirme_takip.bot_durum, ut_gezdirme_takip.durum, ut_gezdirme_takip.bot_tar,  ut_gezdirme_takip.n_paket, ut_gezdirme_takip.hata_aciklama, ut_gezdirme_takip.oto_cevap, ut_gezdirme_takip.sira, ut_gezdirme_takip.gez_tip,ut_gezdirme_takip.tutar AS yuklenen_tutar, fiyat.tutar as tutar FROM fiyat,takip, ut_gezdirme_takip ,kt_paket WHERE takip.id =ut_gezdirme_takip.takip_id AND ut_gezdirme_takip.id=? AND ut_gezdirme_takip.api_id=kt_paket.api_id  AND takip.operator=kt_paket.operator AND ut_gezdirme_takip.tip=kt_paket.tip AND takip.kul_id=fiyat.kul_id AND fiyat.paket_id=kt_paket.id ',array($id));
        if($paket_oz!=null)
          break;
      }
      catch (QueryException $e)
      {
        Log::info('--idden_paket -- patladi =='.$e->getMessage().' .--');
      }

    }
    
    
    return $paket_oz;
  }

  function takipden_paket($id)
  {
    $paket_oz         =  DB::select('SELECT fiyat.tutar,ut_gezdirme.gezdir,ut_gezdirme.sahte_paket_adi,takip.n_paket,takip.referans_no,takip.kul_id ,takip.durum ,takip.gsmno,takip.no_sahibi, kt_paket.*,takip.islem_tar,takip.bot_tar FROM ut_gezdirme, fiyat,kt_paket,takip WHERE fiyat.paket_id=kt_paket.id AND takip.operator=kt_paket.operator AND takip.id=? AND CAST(takip.tip AS DECIMAL(0))=kt_paket.tip AND takip.kontor=ut_gezdirme.sahte_id AND ut_gezdirme.kt_paket_id = kt_paket.id AND fiyat.kul_id = takip.kul_id AND ut_gezdirme.kt_paket_id=(SELECT kt_paket.id FROM kt_paket,takip WHERE takip.id=? AND takip.tip=kt_paket.tip AND takip.kontor=kt_paket.znet_id AND kt_paket.operator=takip.operator  ) AND ut_gezdirme.kul_id=takip.kul_id LIMIT 1 ',array($id,$id));

    return $paket_oz;
  }
  function cevap($api,$tutar)
  {
    if($api=="vip")
      $rt= "OK"; 
    elseif($api=="znet")
      $rt= "OK|1|Talebiniz İşleme Alınmıştır.|".$tutar;
    elseif($api=="truva")
      $rt= "1:yukleme talebi islem listesine alindi:".$tutar;
    elseif($api=="gencan")
      $rt= "200#OK#".$tutar."#"."999";
    return $rt;
  }


  function sisteme_istek_al($id,$operator,$tip,$kontor,$gsmno,$tekilnumara,$kul_id,$api)
  {
    //bir paket gezdirmesi olmasa bile kendini gezdirme olarak kabul eder ve ut_gezdirme_takip tablosuna kenidisini yazar.
    //Bu fonksiyon içerisinde:
    //1 Gelen numaranın son 1 saat içerisindeki gezdirmeleri çekilir
    //2 Herhangi bir kayıt bulunmaz ise direk insert işlemi yapılır
    //3 Exiptal yapılacak kayıtlar bulunursa array içerisine gerekli bilgiler doldurulur ve insert - update leri çalıştırılır.
    //Böylece gelen isteğin Exiptal olması gereken kayıtları daha insert aşamasında exiptal yapılmış olur.
    
    
    //insert edilecek paketin gezdirmelerine bakar
    $now                = date("Y-m-d H:i:s");
    $paket_ozellikleri  = $this->paket_ozellikleri($kontor,$tip,$operator,$kul_id);
    Log::info($gsmno.'--paket oz.--'.date("Y-m-d H:i:s"));
    #sorgu 1saat içindeki bu gsmnoya ait ve bu kategorideki işlemler ut_gezdirme takipden
    //$bir_saat=DB::select("SELECT id,n_paket  FROM takip WHERE kul_id=? AND gsmno=? AND islem_tar >= DATE_SUB(?, INTERVAL 1 HOUR) AND durum=3 AND oto_cevap=0 AND operator=?  ORDER BY islem_tar ASC LIMIT 1  ",array($kul_id,$gsmno,$now,$operator));
    $bir_saat=DB::select("SELECT ut_gezdirme_takip.hata_aciklama,ut_gezdirme_takip.n_paket , kt_paket.kategori  FROM takip,ut_gezdirme_takip, kt_paket WHERE takip.kul_id=? AND takip.gsmno=? AND takip.islem_tar >= DATE_SUB(?, INTERVAL 1 HOUR)  AND takip.oto_cevap=0 AND takip.operator=? AND ut_gezdirme_takip.n_paket IS NOT NULL AND ut_gezdirme_takip.takip_id=takip.id AND kt_paket.tip=ut_gezdirme_takip.tip AND kt_paket.api_id=ut_gezdirme_takip.api_id AND kt_paket.operator=takip.operator AND ut_gezdirme_takip.durum=3 AND ((takip.operator='avea' AND ut_gezdirme_takip.tip!='tam' ) OR (takip.operator='vodafone' AND ut_gezdirme_takip.tip='firsat'))  ORDER BY takip.islem_tar ASC , ut_gezdirme_takip.sira ASC   ",array($kul_id,$gsmno,$now,$operator));
    Log::info($gsmno.'--bir saat .--'.date("Y-m-d H:i:s"));
    if($bir_saat== null || $bir_saat== ""  )
    {
      Log::info($gsmno.'--bir saat=null .--'.date("Y-m-d H:i:s"));
      try
      {
        DB::beginTransaction();
        //direk kayıt calıscak
        $kaydet = DB::insert('INSERT INTO takip (kul_id , operator , tip , kontor , gsmno , tekilno , durum ,islem_tar , bot_kodu , kategori ) VALUES (?,?,?,?,?,?,?,?,?,? )',array($id , $operator,$tip,$kontor,$gsmno,$tekilnumara,'2',$now,'0',$paket_ozellikleri[0]->kategori));
      
         $last_insert_id=DB::select("SELECT id FROM takip WHERE kul_id =? AND operator=? AND tip=? AND kontor=? AND gsmno=? AND tekilno =?",array($id,$operator,$tip,$kontor,$gsmno,$tekilnumara));
         $gezdirmeler=DB::select("SELECT takip.gsmno, ut_gezdirme.gezdir,ut_gezdirme.sira , kt_paket.tip,kt_paket.api_id FROM takip, ut_gezdirme, kt_paket WHERE takip.id =? AND ut_gezdirme.kul_id =? AND ut_gezdirme.sahte_id = takip.kontor AND ut_gezdirme.gezdirme_id = kt_paket.id AND takip.operator = kt_paket.operator AND ut_gezdirme.kt_paket_id=(SELECT kt_paket.id FROM kt_paket,takip WHERE takip.id=? AND takip.tip=kt_paket.tip AND takip.kontor=kt_paket.znet_id  AND takip.operator=kt_paket.operator ) ",array($last_insert_id[0]->id,$kul_id,$last_insert_id[0]->id));
         
         foreach ($gezdirmeler as $key) 
         {

            $gezdirmelerini_kaydet=DB::insert("INSERT INTO ut_gezdirme_takip (takip_id,tip,api_id,bot_kodu,bot_durum,durum,sira,gez_tip) VALUES(?,?,?,?,?,?,?,?)",array($last_insert_id[0]->id,$key->tip,$key->api_id,"0","0","2",$key->sira,$key->gezdir));
         }

     
        $tutar = number_format($paket_ozellikleri[0]->tutar,4);
        $cevap=$this->cevap($api,$tutar);
        DB::commit();
        return $cevap;
      }
      catch (QueryException $e)
      {
        Log::info($gsmno.'--hata=='.$e->getMessage().' .--'.date("Y-m-d H:i:s"));
        DB::rollback();
        Log::info($gsmno.'--hata=='.$e->getMessage().' .--'.date("Y-m-d H:i:s"));
        if($api=="vip")
          $rt= "13";
        elseif($api=="znet")
          $rt= "OK|3|APi Hatası.|0.00";
        elseif($api=="gencan")
          $rt= "107#Yüklemek istediğiniz tutar aktif değil";
        else
          $rt=$e->getMessage()."beklenmedik hata";

        return $rt;

      }
    }
    
    $hata_aciklama=$bir_saat[0]->hata_aciklama;
    if($hata_aciklama!=null &&  $hata_aciklama!="")
    {
      
      $ex_iptal_calismayacak=strpos((string)$hata_aciklama,"işlem gerçekleştirilemiyor");
      
      //bakiye bittimi
    }
    else
    {
      $ex_iptal_calismayacak=false;
      
    }
          

        
        

    if($ex_iptal_calismayacak!==false )
    {
      try
      {
        DB::beginTransaction();
        //direk kayıt calıscak
        $kaydet = DB::insert('INSERT INTO takip (kul_id , operator , tip , kontor , gsmno , tekilno , durum ,islem_tar , bot_kodu , kategori ) VALUES (?,?,?,?,?,?,?,?,?,? )',array($id , $operator,$tip,$kontor,$gsmno,$tekilnumara,'2',$now,'0',$paket_ozellikleri[0]->kategori));

        $last_insert_id=DB::select("SELECT id FROM takip WHERE kul_id =? AND operator=? AND tip=? AND kontor=? AND gsmno=? AND tekilno =?",array($id,$operator,$tip,$kontor,$gsmno,$tekilnumara));
        $gezdirmeler=DB::select("SELECT takip.gsmno, ut_gezdirme.gezdir,ut_gezdirme.sira , kt_paket.tip,kt_paket.api_id FROM takip, ut_gezdirme, kt_paket WHERE takip.id =? AND ut_gezdirme.kul_id =? AND ut_gezdirme.sahte_id = takip.kontor AND ut_gezdirme.gezdirme_id = kt_paket.id AND takip.operator = kt_paket.operator AND ut_gezdirme.kt_paket_id=(SELECT kt_paket.id FROM kt_paket,takip WHERE takip.id=? AND takip.tip=kt_paket.tip AND takip.kontor=kt_paket.znet_id  AND takip.operator=kt_paket.operator ) ",array($last_insert_id[0]->id,$kul_id,$last_insert_id[0]->id));
         
         foreach ($gezdirmeler as $key) 
         {

            $gezdirmelerini_kaydet=DB::insert("INSERT INTO ut_gezdirme_takip (takip_id,tip,api_id,bot_kodu,bot_durum,durum,sira,gez_tip) VALUES(?,?,?,?,?,?,?,?)",array($last_insert_id[0]->id,$key->tip,$key->api_id,"0","0","2",$key->sira,$key->gezdir));
         }


        $tutar = number_format($paket_ozellikleri[0]->tutar,4);
        $cevap=$this->cevap($api,$tutar);
        DB::commit();
        return $cevap;
      }
      catch (QueryException $e)
      {
        Log::info($gsmno.'--hata=='.$e->getMessage().' .--'.date("Y-m-d H:i:s"));
        DB::rollback();
        Log::info($gsmno.'--hata=='.$e->getMessage().' .--'.date("Y-m-d H:i:s"));
        if($api=="vip")
          $rt= "13";
        elseif($api=="znet")
          $rt= "OK|3|APi Hatası.|0.00";
        else
          $rt=$e->getMessage()."beklenmedik hata";

        return $rt;

      }
    }

    try
    {
      DB::beginTransaction();

      //ex_iptal varmı bakılcak
      //paketin tüm gezdirmeleri cekilir ve arraye kaydedilir.
      $n_paket=array();
      $onceki_npaketler=array();
      foreach ($bir_saat as $key ) 
      {
        
        $npaket[]=array("n_paket"=>$key->n_paket ,"kategori"=>$key->kategori);
        array_push($onceki_npaketler, $key->n_paket);
        Log::info($gsmno.'--bir saat foreach .--'.date("Y-m-d H:i:s"));
      }
      
      $gezdirmeleri_cek=DB::select("SELECT  ut_gezdirme.sira , ut_gezdirme.gezdir , kt_paket.tip ,kt_paket.api_id ,kt_paket.kategori FROM  ut_gezdirme, kt_paket WHERE  ut_gezdirme.kul_id =? AND ut_gezdirme.sahte_id =? AND ut_gezdirme.gezdirme_id = kt_paket.id AND  kt_paket.operator=? AND ut_gezdirme.kt_paket_id=? ",array($kul_id,number_format($kontor,0,"",""),$operator,$paket_ozellikleri[0]->id));

      $kaydet = DB::insert('INSERT INTO takip (kul_id , operator , tip , kontor , gsmno , tekilno , durum ,islem_tar , bot_kodu , kategori ) VALUES (?,?,?,?,?,?,?,?,?,? )',array($kul_id , $operator,$tip,$kontor,$gsmno,$tekilnumara,'2',$now,'0',$paket_ozellikleri[0]->kategori));
        
      $last_insert_id=DB::select("SELECT id FROM takip WHERE kul_id =? AND operator=? AND tip=? AND kontor=? AND gsmno=? AND tekilno =? LIMIT 1",array($id,$operator,$tip,$kontor,$gsmno,$tekilnumara));
      Log::info($gsmno.'--insert_id='.$last_insert_id[0]->id.'.--'.date("Y-m-d H:i:s"));
      $sayi=count($gezdirmeleri_cek);
      $sayac=0;
      $ex=0;
      foreach ($gezdirmeleri_cek as $key ) 
      {
        Log::info($gsmno.'--op='.$operator.$key->tip.'=tip.--'.date("Y-m-d H:i:s"));
        if($operator=="vodafone" && ($key->tip == "firsat" || $key->tip == "sorgu") )
          $ex=$this->ex_iptal($npaket,$key->api_id,$key->kategori);
        elseif($operator=="avea" && $key->tip!="tam")
          $ex=$this->ex_iptal($npaket,$key->api_id,$key->kategori);
        else
          $ex=array(0,0);


        switch ($ex[0]) 
        {
          case '0':
            # olumlu
            $gezdirmelerini_kaydet=DB::insert("INSERT INTO ut_gezdirme_takip (takip_id,tip,api_id,bot_kodu,bot_durum,durum,sira,gez_tip) VALUES(?,?,?,?,?,?,?,?)",array($last_insert_id[0]->id,$key->tip,$key->api_id,"0","0","2",$key->sira,$key->gezdir));
            Log::info($gsmno.'--OLUMLU.--'.date("Y-m-d H:i:s"));
            break;
          
          case '1':
            # iptal...
            $gezdirmelerini_kaydet=DB::insert("INSERT INTO ut_gezdirme_takip (takip_id,tip,api_id,bot_kodu,bot_durum,durum,sira,gez_tip,bot_tar,oto_cevap,n_paket) VALUES(?,?,?,?,?,?,?,?,?,?,?)",array($last_insert_id[0]->id,$key->tip,$key->api_id,"ex_iptal","2","3",$key->sira,$key->gezdir,$now,$last_insert_id[0]->id,$ex[1]));
            
            Log::info($gsmno.'--OLUMSUZ.--'.date("Y-m-d H:i:s"));
            $sayac++;
            if($sayac==$sayi )
            {
              //ana paket iptal
              if($key->tip=="firsat" || $key->tip=="sorgu")
                $this->ana_paket_iptal($onceki_npaketler,$last_insert_id[0]->id,1);
              else
                $this->ana_paket_iptal($onceki_npaketler,$last_insert_id[0]->id);

              Log::info($gsmno.'--ANA PAKET İPTAL.--'.date("Y-m-d H:i:s"));
            }
            break;
        }

      }
      $tutar = number_format($paket_ozellikleri[0]->tutar,4);
      $cevap=$this->cevap($api,$tutar);
      DB::commit();
      return $cevap;
    }
    catch (QueryException $e)
    {
      Log::info($gsmno.'--ex_hata=='.$e->getMessage().' .--'.date("Y-m-d H:i:s"));
      DB::rollback();
      Log::info($gsmno.'--ex_hata=='.$e->getMessage().' .--'.date("Y-m-d H:i:s"));
      if($api=="vip")
        $rt= "13";
      elseif($api=="znet")
        $rt= "OK|3|APi Hatası.|0.00";

      return $rt;

    }
    
  }

  function ana_paket_iptal($onceki_npaketler,$takip_id,$sabit=0)
  {
    
    //string işlemleri
    $n_paketler="[";
    if($sabit==1)
    {
      $n_paketler=$n_paketler."1-2-5-17-23-24-26-27-72-75-89-90-25-43-52-63-68-99-13-79-81-";
    }
    foreach ($onceki_npaketler as $key_p ) 
    {
      if($key_p != "[0]" && $key_p !=null && $key_p != "" )
      {
        $n_paketler=$n_paketler.implode("-",$this->n_paket_cozumle($key_p) )."-";
      }
      
      
    }
    $n_paketler=substr($n_paketler,0,strlen($n_paketler)-1);
    $n_paketler=$n_paketler."]";
    $u_array=array_unique($this->n_paket_cozumle($n_paketler));
    $birlestir=implode("-",$u_array);
    $n_paketler="[".$birlestir."]";
    if($n_paketler=="[]")
      $n_paketler="[0]";

     $ana_paket_iptal=DB::update("UPDATE takip SET durum=?, bot_kodu =? ,bot_durum=?,bot_tar=NOW(), n_paket =? ,hata_aciklama=? WHERE id=?",array("3","ex-iptal","2",$n_paketler,"Seçmiş Olduğunuz Paket Hattiniza Uygun Değildir.",$takip_id));
  }
  function n_pakette_ara($id,$str)
  {
    Log::info(' n_pakette_ara'.$id."  ".$str." ".date("Y-m-d H:i:s"));
    $rtrn         = 0;
    
    
    if($str!="" && $str!=null && $id!="" && $id!=null )
    {
      $konum=strpos((string)$str,(string)$id);
    }
    else
      $konum=false;

    Log::info(' n_pakette_ara-konum='.$konum."  ".$str." ".date("Y-m-d H:i:s"));
    if($konum!==false)
    {
      $rtrn = 1;
      Log::info(' 1 donuyor n_pakette_ara'.$id."  ".$str." ".date("Y-m-d H:i:s"));
    }
     
    
      if($rtrn ==1)
      {
        Log::info(' 1 donuyor n_pakette_ara'.$id."  ".$str." ".date("Y-m-d H:i:s"));
        return 1;
      }
      else
      {
        Log::info(' 0 donuyor n_pakette_ara 11 '.$konum."  ".$id."  ".$str." ".date("Y-m-d H:i:s"));
        return 0;
      }

  }

  function panelac($baslik)
  {
      echo '                <div class="panel-heading" style="clear:both; padding:5px; background-color:#186ef1;color:white; position:relative; 
      border-radius:20px; z-index:1; width:15.15%; margin-left:3%; margin-bottom:-2%;"><center>'.$baslik.'</center></div>
                  <div class="panel panel-info col-md-12 col-lg-12 col-xs-12" style="min-height:80px; margin-top:0%;">
                      <div class="panel-body" style="">';
  }

  function panelkapa()
  {
    echo'</div></div>';
  }

  function n_paket_cozumle($str_paket)
  {
    $kirp1=substr($str_paket,1,-1);
    $kirp2=substr($kirp1,0,strlen($kirp1));
    $parcala=explode("-", $kirp2);
    return $parcala;
  }

  function paket_isim_cek($id,$tip,$operator)
  {
    
    $count=DB::select("SELECT COUNT(id) as say FROM kt_paket WHERE api_id = ?",array($id));
    $say=null;
    if($count[0]->say >0)
    {
      $sorgu="SELECT paket FROM kt_paket WHERE api_id = ? AND operator=?";
      $calistir=DB::select($sorgu,array($id,$operator));
      if($calistir != null && $calistir!="")
        $say=$calistir[0]->paket;
    }
    
    
    return $say;

  }
  
  function kontrol_cevap($takip_id,$gsmno=0,$operator=0,$islem=0)
  {
    


    $paket_ozellikleri=$this->takipden_paket($takip_id);
    $tutar=$paket_ozellikleri[0]->tutar;

      switch ( $paket_ozellikleri[0]->durum ) {
       case '1':
          if($paket_ozellikleri[0]->operator=="avea")
          {
              echo "</br>Ref.No=".$paket_ozellikleri[0]->referans_no;
          }
          else
          {
            if($paket_ozellikleri[0]->tip=="tam")
              echo "</br>Ref.No=".$paket_ozellikleri[0]->referans_no."_Abone=".$paket_ozellikleri[0]->no_sahibi;
            else
              echo "</br>Abone=".$paket_ozellikleri[0]->no_sahibi;
          }
         
          break;
        case '2':
          echo "</br>islemde:";
          break;
        case '3':
        if( ( ($paket_ozellikleri[0]->tip =="firsat" || $paket_ozellikleri[0]->tip =="sorgu") && $paket_ozellikleri[0]->operator == "vodafone") || ($paket_ozellikleri[0]->tip != "tam"  && $paket_ozellikleri[0]->operator =="avea"   ) )
        {
           $paketler= $this->n_paket_cozumle($paket_ozellikleri[0]->n_paket);
          if(count($paketler)!="0" && $paket_ozellikleri[0]->n_paket !="[0]")
          {
            echo "</br>Tavsiye=".$paket_ozellikleri[0]->n_paket;
            for($i=0;count($paketler) != $i;$i++)
            {
              if($paketler[$i]!="")
              {
                $paket_adi=$this->paket_isim_cek($paketler[$i],$paket_ozellikleri[0]->tip,$paket_ozellikleri[0]->operator);
                echo "</br>[".$paketler[$i]."] - ".$paket_adi;
                if(($paket_adi==null || $paket_adi=="")&&$islem==1)
                {
                  try
                  {
                    DB::insert("INSERT INTO ut_yeni_paket (paket_adi,gsmno,operator) VALUES (?,?,?)",array($paketler[$i],$gsmno,$operator));
                  }
                  catch (QueryException $e)
                  {
                    $hata=$e->getMessage();
                    Log::info('yeni paket insert edielemedi -----AVEA---__error__'.$hata);
                
                  }
                    
                }
              }
              
            }
          }
          else
            echo "</br>Hattiniza Uygun  Firsat Paketi Bulunamadi";
        }
      
      
          break;
        default:
          # code...
          break;
      }

   

  }
  function sayfala($dizi,$eleman)
  {
    $toplam=count($dizi);
    $sayfa_say=$toplam/$eleman;
    $tmpdizi=array();
    for ($i=0; $i <$eleman+1 ; $i++) 
    { 
      if(isset($dizi[$i]))
        array_push($tmpdizi, $dizi[$i]);

    }

  }
  function tam_gezdirme_listele($key,$sayac)
  {
    $id=$key->u_id;
    $renk="backgorund-color:white;";
    $dr=$key->durum;
    switch($dr)
    {
      case 2:
      $renk="background-color:#eaffa0;";
      break;
      case 1:
      $renk="background-color:#acf2af;";
      break;
      case 3:
      $renk="background:#F5BCA9;";
      break;
      case 4:
      $renk="background:#dff5e0;";
      
      break;
      case 5:
      $renk="background:#d05e39;";
      break;
      case 9:
      $renk="background-color:#acf2af;";
      break;
      case 8:
      $renk="background:#F5BCA9;";
      break;
      case 7:
      $renk="background-color:#eaffa0;";
      break;

    }

 
      
  
     echo "<tr style=' $renk  ' >
     <td style='text-align:center;'><input type='checkbox'  name='tam[$sayac]' value='$id' style='width:15px; height:15px; padding:15px;' /></td>
     <td style='padding:3px;' colspan=6 > ".$key->paket."</td>";
     $bot_kodu=DB::select("SELECT bot_kodu FROM ut_gezdirme_takip WHERE takip_id=?  ORDER BY id ",array($key->takip_id));
   
     switch (true) {
       case ($dr=="7" || $dr=="2"):
          $isleme_alindi_mi=DB::select("SELECT bot_kodu FROM ut_gezdirme_takip WHERE takip_id=? and bot_durum !='0' ORDER BY id ",array($id));
   
          echo "<td style='".$renk."' align=center><img id='bek_".$id."' src='img/beklemede.png' width='20' height='20'></td>";
          if($isleme_alindi_mi != null )
            echo "<td style='".$renk."' align=center>".$isleme_alindi_mi[0]->bot_kodu ;
          else
            echo "<td style='".$renk."' align=center> işleme alınmayi bekliyor";
      break;
      case ($dr=="3" || $dr=="8"):
      echo "<td style='".$renk."' align=center><img id='ipt_".$id."' src='img/iptal.png' width='20' height='20'></td>";
        echo "<td style='".$renk."' align=center>".$bot_kodu[0]->bot_kodu." </td>";
      break;
      case($dr=="9" || $dr=="1"):
        echo "<td style='".$renk."' align=center><img id='ode_".$id."' src='img/odendi.png' width='20' height='20'></td>";
        echo "<td style='".$renk."' align=center> ".$bot_kodu[0]->bot_kodu." </td>";
      break;

       
       default:
         # code...
         break;
     }
     
   echo "</td></tr>";
  }

  

  function yukleme_takip_paket_listele($row,$sayac)
  {
    $renk="backgorund-color:white;";
    $id=$row['id'];
    $paket_ozellikleri_2=$this->takipden_paket($id);

    $dr=$row['durum'];

    switch($dr)
    {
      case 2:
      $renk="background-color:#eaffa0;";
      break;
      case 7 :
      $renk="background-color:#eaffa0;";
      break;
      case 1:
      $renk="background-color:#acf2af;";
      break;
      case 3:
      $renk="background:#F5BCA9;";
      break;
      case 4:
      $renk="background:#ea924f;";
      
      break;
      case 5:
      $renk="background:#ea924f;";
      break;
      case 6:
      $renk="background:#ea924f;";
      break;

    }
    $isleme_alindi_mi=DB::select("SELECT bot_kodu FROM ut_gezdirme_takip WHERE takip_id=? and bot_durum !='0' ORDER BY id ",array($id));
    if($isleme_alindi_mi != null && $row['durum']=="2" )
      $disable="disabled";
    else
      $disable="";

    echo "<tr style='  border-left:hidden; border-right:hidden;' ><td style='padding:3px;' colspan=10 > </td></tr>
            <tr style='height:20px;'>";
            

    echo "<td  style='".$renk." text-align:center;'  >"."<input type='checkbox' $disable class='seciniz' name='islem[$sayac]' value='$id' style='width:15px; height:15px; padding:15px;' />"."</td>"; 
    echo "<td style='".$renk." text-align:center;' onclick='ac(".$id.");' >".$row['operator']." </br>".$row['tip']. "</td>"; 

    echo "<td  style='".$renk." clear:both; text-align:center;'     >".$row['gsmno']."</br> </td>";
    echo "<td onclick='modal_ac(".$row["id"].");' data-toggle='modal' data-target='#modal_1' ";
    echo "style=' ".$renk." text-align:center;  overflow:hidden; '> <asd style='font-size:11px;'>(".number_format($row["kontor"],0,"","").") </asd>";
    if($paket_ozellikleri_2[0]->h_y_sms==1 && $paket_ozellikleri_2[0]->s_i_sms==1 && $paket_ozellikleri_2[0]->h_y_ses==1 && $paket_ozellikleri_2[0]->s_i_ses==1 && $paket_ozellikleri_2[0]->internet==1 && $paket_ozellikleri_2[0]->gun==1)
    {
      
      if($paket_ozellikleri_2[0]->sahte_paket_adi!=null)
        $pa=$paket_ozellikleri_2[0]->sahte_paket_adi;
      else
        $pa=$paket_ozellikleri_2[0]->paket;
      
      echo $pa;
    }
    else
      echo $paket_ozellikleri_2[0]->paket;
    echo " </td>";
    $yuklenen_paket=$this->yuklenen_paket($id);
    $kar="0.0000";
    if($yuklenen_paket==null || $yuklenen_paket==""   )
    {
     //paket adi
     $yuklenen_tutar=number_format($paket_ozellikleri_2[0]->tutar,4);
    }
    else
    {
     $yuklenen_tutar=number_format($row["tutar"],4);
     //if($paket_ozellikleri_2[0]->gezdir =="1")
      $kar= number_format($row["kar"],4);
    }

   
   

    echo "<td style='".$renk." text-align:right; '>".$yuklenen_tutar."</td>";
  

    
    echo "<td style='".$renk." text-align:right;'>".$kar."</td>";
    

    echo "<td style='".$renk." text-align:center;'>".$row['islem_tar']."</br>".$row["bot_tar"]."</td>";
    
    
    $durum=$row['durum'];
    
    switch ($durum) {
      case 2:
        
   
        echo "<td style='".$renk."' align=center><img id='bek_".$id."' src='img/beklemede.png' width='20' height='20'></td>";
        if($isleme_alindi_mi != null )
        {
            //echo "<td style='".$renk."' align=center>".$isleme_alindi_mi[0]->bot_kodu ;
          echo "<td style='".$renk."' align=center>"."İsleme Alındı";
        }
          else
            echo "<td style='".$renk."' align=center> işleme alınmayi bekliyor";
           
          
       echo"
        </tr>";
       
      break;
      case 7:
        
   
        echo "<td style='".$renk."' align=center><img id='bek_".$id."' src='img/beklemede.png' width='20' height='20'></td>";
       
            echo "<td style='".$renk."' align=center> Beklemede";
           
          
       echo"
        </tr>";
        
      break;
      case 1:

        echo "<td style='".$renk."' align=center><img id='ode_".$id."' src='img/odendi.png' width='20' height='20'></td>";
        echo "<td style='".$renk."' align=center> ".$row["bot_kodu"]." </td>";
        echo "<input type='hidden' name='hf' id='hf' value='".$id."'/></td>
        <input type='hidden' value='ac' id='ac1_".$id."' />";
         echo "</tr> ";
        //$toplam=$row['tutar']+$row["islem_komisyonu"]+$row["kk_komisyonu"]+$row["gecikme_bedeli"];
                        
        echo"<tr>   <td style='text-align:left; ".$renk."'colspan='10'><div id='ac_".$id."' style='".$renk."  display:none;'> ";
        
        echo $this->kontrol_cevap($id);
        echo "</div></td></tr>";
        
      break;
      case 3:
        echo "<td style='".$renk."' align=center><img id='ipt_".$id."' src='img/iptal.png' width='20' height='20'></td>";
        echo "<td style='".$renk."' align=center>".$row["bot_kodu"]." </td>";
        echo "<input type='hidden' name='hf' id='hf' value='".$id."'/></td>
        <input type='hidden' value='ac' id='ac1_".$id."' />";
        echo "</tr> ";
        //$toplam=$row['tutar']+$row["islem_komisyonu"]+$row["kk_komisyonu"]+$row["gecikme_bedeli"];

        echo"<tr>   <td style='text-align:left; ".$renk."'colspan='10'><div id='ac_".$id."' style='".$renk."  display:none;'> 
        "." 

        <b >".$row['hata_aciklama']."</b> -------";
        if($row['operator']=="avea")
        {
          echo $this->kontrol_cevap($id,$row['gsmno'],$row['operator'],"1");
        }
        else
          echo $this->kontrol_cevap($id);

        echo"</div></td>
        </tr>";
        
      break;
      case 4:
        echo "<td style='".$renk."' align=center><img id='ode_".$id."' src='img/odendi.png' width='20' height='20'></td>";
        echo "<td style='".$renk."' align=center> ".$row["bot_kodu"]." </td>";
        echo "<input type='hidden' name='hf' id='hf' value='".$id."'/></td>
        <input type='hidden' value='ac' id='ac1_".$id."' />";
        echo "</tr> ";
        //$toplam=$row['tutar']+$row["islem_komisyonu"]+$row["kk_komisyonu"]+$row["gecikme_bedeli"];

        echo"<tr>   <td style='text-align:left; ".$renk."'colspan='10'><div id='ac_".$id."' style='".$renk."  display:none;'> ";

        echo $this->kontrol_cevap($id);
        echo "Yükleme Başarılı Ancak Kontrol Edilmesi Gerekiyor!";
        echo "</div></td></tr>";
       
      break;
      case 5:
        echo "<td style='".$renk."' align=center><img id='ipt_".$id."' src='img/iptal.png' width='20' height='20'></td>";
        echo "<td style='".$renk."' align=center>".$row["bot_kodu"]." </td>";
        echo "<input type='hidden' name='hf' id='hf' value='".$id."'/></td>
        <input type='hidden' value='ac' id='ac1_".$id."' />";
        echo "</tr> ";
        //$toplam=$row['tutar']+$row["islem_komisyonu"]+$row["kk_komisyonu"]+$row["gecikme_bedeli"];

        echo"<tr>   <td style='text-align:left; ".$renk."'colspan='10'><div id='ac_".$id."' style='".$renk."  display:none;'> 
        "." Bot teknik bir hata ile karşılaştı .Lütfen Kontrol Ediniz!

        <b >".$row['hata_aciklama']."</b> -------";
        echo $this->kontrol_cevap($id);
        echo"</div></td>
        </tr>";
       
      break;
      case 6:
        echo "<td style='".$renk."' align=center><img id='ipt_".$id."' src='img/beklemede.png' width='20' height='20'></td>";
        if($isleme_alindi_mi != null )
          echo "<td style='".$renk."' align=center>".$isleme_alindi_mi[0]->bot_kodu ;
        else
          echo "<td style='".$renk."' align=center> işleme alınmayi bekliyor";
        //echo "<td style='".$renk."' align=center>".$row["bot_kodu"]." </td>";
        echo "<input type='hidden' name='hf' id='hf' value='".$id."'/></td>
        <input type='hidden' value='ac' id='ac1_".$id."' />";
        echo "</tr> ";
        //$toplam=$row['tutar']+$row["islem_komisyonu"]+$row["kk_komisyonu"]+$row["gecikme_bedeli"];

        echo"<tr>   <td style='text-align:left; ".$renk."'colspan='10'><div id='ac_".$id."' style='".$renk."  display:none;'> 
        ".$row['hata_aciklama']."<br> <b>Bot Bu işlemi aldı ancak apiye cevap dönmedi kontrol edilmesi gerekiyor!

        </b >";
        echo $this->kontrol_cevap($id);
        echo"</div></td>
        </tr>";
       
      break;

    }

  }

  
  
//aynı kategoride degilse bota direk gonder 
  //aynı kategorideyse npakette ara varsa bota gonder
  public function ex_iptal($sorgu,$api_id,$kategori,$ut_gezdirme_takip_id=null)
  {
    
    foreach ($sorgu as $key ) 
    {
      if($key["n_paket"] !=null)
      {
        Log::info($kategori.'--kategoriler .--'.$key["kategori"].date("Y-m-d H:i:s"));

        if((string)$kategori==(string)$key["kategori"])
        {
          
          if($this->n_pakette_ara($api_id,$key["n_paket"]) == 1  )
          {
            //bota gönderilebilir paketler
          }
          else
          {
            
            //bota göndermeye gerek olmayan paketler
            if($ut_gezdirme_takip_id!=null)
              $ex_iptal=DB::update("UPDATE ut_gezdirme_takip SET bot_kodu=? ,bot_durum=? , durum=? ,bot_tar=NOW() ,oto_cevap =?,n_paket=?  WHERE id=? ",array("ex-iptal","2","3",$ut_gezdirme_takip_id,$key["n_paket"],$ut_gezdirme_takip_id));

            $cevap=array(1,$key["n_paket"]);
            return $cevap;
          }
        }
      }
    }
    
      $cevap=array(0,0);
    

    return $cevap;
  }

function SMS_GONDER($numaras,$mesaj)
{
  $dizi=explode("\r\n", $numaras);
  $numaralar=array();
  foreach ($dizi as $key ) 
  {
      if(strlen($key)=="11")
      {
          $key=substr($key, 1,strlen($key));
          array_push($numaralar, $key);
      }
      elseif(strlen($key)=="10")
      {
          array_push($numaralar, $key);
      }
      else
      {
          $key=null;
      }
  }

  $user_name = "5067396836";   //kullanici adi
  $user_pass = "123456";   //kullanici sifre

  $sms_type = '1:n';    // 1:n tek sms coklu numaraya, n:n coklu sms coklu numaraya
  $orgin_name = "Apiekle.com"; //baslikli smslerde sms başligi

  $site_name = 'http://www.gittibile.net/index.php?function=api&obj1=takeOrder'; //post edilecek site adresi

  $phone_numbers = $numaralar;  //smslerin gönderilecegi numaralar

  $sms_message = $mesaj;  //mesaj metni

  $xml = <<<EOS
            <?xml version="1.0" encoding="utf-8" ?>
            <mainbody>
                <header>
                    <company>TEKNOGRAFI</company>
                    <usercode>{$user_name}</usercode>
                    <password>{$user_pass}</password>
                    <startdate></startdate>
                    <stopdate></stopdate>
                    <type>{$sms_type}</type>
                    <msgheader><![CDATA[{$orgin_name}]]></msgheader>
                </header>
EOS;
  

  $xml .= '<bodysms>';
  foreach ($phone_numbers as $key ) 
  {
    $xml .= '<mp><msg><![CDATA['.$sms_message.']]></msg><no>'.$key.'</no></mp>';
  }
  
  $xml .= '</bodysms>';
  $xml .= '</mainbody>';
              
  $result = $this->sendRequest($site_name,$xml,array('Content-Type: text/plain'));

  return var_export($result);  //donen sonuc bilgisi
}
function sendRequest($site_name,$send_xml,$header_type) 
{

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$site_name);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$send_xml);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$header_type);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);

  $result = curl_exec($ch);

  return $result;
}



}
?>
