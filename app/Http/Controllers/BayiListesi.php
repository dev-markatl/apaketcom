<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Operator ;
use App\Models\Tip;
use App\Models\Kullanici;
use App\Models\Rol;
use App\Models\Ilce;
use App\Models\Robot;
use App\Models\Robothesaphareketleri;
use App\Models\Islemturu;
use App\Models\Kullanicihesaphareketleri;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;





class BayiListesi 
{
    public function KullaniciGuncelle(Request $request)
    {
        try
        {
            $kul=Kullanici::where("id",$request->id)->first();
            $kul->ad=$request->input("isim");
            $kul->soyAd=$request->input("soyad");
            
            $kul->takmaAd=$request->input("takmaAd");
            $kul->yetkiYukle=$request->yukle;
            $kul->yetkiFatura=$request->fatura;
            $kul->yetkiSorgu=$request->sorgu;
            $kul->rolId=Rol::where("rolAdi","Bayi")->first()->id;
            $kul->firmaAdi=$request->input("firmaAdi");
            $kul->aktif=$request->aktif;
            $kul->sonDegisiklikYapan=$request->input("takmaAd");
            $kul->mail=$request->input("mail");
            $kul->sabitTel=$request->input("sabitTel");
            $kul->vergiDairesi=$request->input("vergiDairesi");
            $kul->vergiNo=$request->input("vergiNo");
            $kul->cepTel=$request->input("cepTel");
            $kul->adres=$request->input("adres");
            $kul->ilceId=$request->input("ilce");
            if($request->input("sifre1") !=null && $request->input("sifre1")!="")
                $kul->sifre=Hash::make($request->sifre1);
            $kul->sonSifre=$request->sifre1;
            $kul->save();
            return response()->json([
                "status"=>"true",
                "message"=>"Işlem Başarılı!"
                
            ]);
        }
        catch(\Exception $e)
        {
            $message="";
            if($e->getCode()==23000)
                $message="Bu kullanıcıAdı veya CepTel kullanımda";
            return response()->json([
                "status"=>"false",
                "message"=>"Işlem Başarısız!(".$e->getCode().") $message"
                
            ]);
        }
    }
    public function KullaniciOzellikleri(Request $request)
    {
        try
        {
            $id=$request->id;
            if($id>0)
            {
                $r=Kullanici::where("id",$id)->first();
                $isyeriIpler=DB::select("SELECT * FROM ip WHERE kullaniciId=? AND isyeri=1",array($id));
                $sunucuIpler=DB::select("SELECT * FROM ip WHERE kullaniciId=? AND isyeri=0",array($id));
                return view("bayilistesi/YeniBayi",array(
                "id"=>$r->id,
                "isyeriIpler"=>$isyeriIpler,
                "sunucuIpler"=>$sunucuIpler,
                "ad"=>$r->ad,
                "soyAd"=>$r->soyAd,
                "takmaAd"=>$r->takmaAd,
                "bakiye"=>$r->bakiye,
                "sifre"=>$r->sifre,
                "yetkiYukle"=>$r->yetkiYukle,
                "yetkiSorgu"=>$r->yetkiSorgu,
                "yetkiFatura"=>$r->yetkiFatura,
                "sorguUcret"=>$r->sorguUcret,
                "firmaAdi"=>$r->firmaAdi,
                "aktif"=>$r->aktif,
                "mail"=>$r->mail,
                "sabitTel"=>$r->sabitTel,
                "vergiDairesi"=>$r->vergiDairesi,
                "vergiNo"=>$r->vergiNo,
                "cepTel"=>$r->cepTel,
                "adres"=>$r->adres,
                "ilceId"=>$r->ilceId,
                "ilId"=>Ilce::where('id',$r->ilceId)->first()->ilId,
                "update"=>"true",
                "status"=>"true",
                "message"=>"İslem Başarılı! "));
                
            }
            else
            {
                return view("bayilistesi/YeniBayi",array("id"=>0,
                "ad"=>"",
                "soyAd"=>"",
                "takmaAd"=>"",
                "bakiye"=>"",
                "sifre"=>"",
                "yetkiYukle"=>"",
                "yetkiSorgu"=>"",
                "yetkiFatura"=>"",
                "sorguUcret"=>"",
                "firmaAdi"=>"",
                "aktif"=>"",
                "mail"=>"",
                "sabitTel"=>"",
                "vergiDairesi"=>"",
                "vergiNo"=>"",
                "cepTel"=>"",
                "adres"=>"",
                "ilceId"=>null,
                "ilId"=>null,
                "update"=>"false",
                "status"=>"",
                "message"=>"",
                    ));
                
            }
            
    
        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
    }
    public function YeniKullanici(Request $request)
    {
        try
        {
            $kul= new Kullanici;
            $kul->ad=$request->input("isim");
            $kul->soyAd=$request->input("soyad");
            $kul->takmaAd=$request->input("takmaAd");
            $kul->bakiye=0;
            $kul->sonSifre=$request->sifre1;
            $kul->sifre=Hash::make($request->sifre1);
            $kul->yetkiYukle=$request->yukle;
            $kul->yetkiFatura=$request->fatura;
            $kul->yetkiSorgu=$request->sorgu;
            $kul->sorguUcret=0.0;
            $kul->rolId=Rol::where("rolAdi","Bayi")->first()->id;
            $kul->firmaAdi=$request->input("firmaAdi");
            $kul->aktif=$request->aktif;
            $kul->sonDegisiklikYapan=$request->input("takmaAd");
            $kul->mail=$request->input("mail");
            $kul->sabitTel=$request->input("sabitTel");
            $kul->vergiDairesi=$request->input("vergiDairesi");
            $kul->vergiNo=$request->input("vergiNo");
            $kul->cepTel=$request->input("cepTel");
            $kul->adres=$request->input("adres");
            $kul->ilceId=$request->input("ilce");
            $kul->save();
            return response()->json([
                "status"=>"true",
                "message"=>"Işlem Başarılı!"
                
            ]);
        }
        catch(\Exception $e)
        {
            $message="";
            if($e->getCode()==23000)
                $message="Bu kullanıcıAdı veya CepTel kullanımda";
            return response()->json([
                "status"=>"false",
                "message"=>"Işlem Başarısız!(".$e->getCode().") $message"
                
            ]);
        }
    }
    public function HesapHareketleriTemizle(Request $request)
    {
        $manager            =   new SessionManager;
        $bayiId             =   $request->id;
        $manager->PageName  =   "BayiHesap".$bayiId;
        $manager->Tarih1    =   null;
        $manager->Tarih2    =   null;
        $manager->IslemTuru =   -1;
        $manager->SetAllData();
        return redirect("ajax/BayiHesapHareketleri?id=$bayiId");
    }
    public function hesaphareketleri(Request $request)
    {   
        $bayiId                         =   $request->id;
        $session                        =   $request->session;
        $islemTuru                      =   $request->islemTuru;
        $tar1                           =   $request->tarih1;
        $tar2                           =   $request->tarih2;
        $sayfadaGosterilecekKayitSayisi =   25;
        $suankiSayfa                    =   $request->sayfa;
        $cf                             =   new CommonFunctions;
        $manager                        =   new SessionManager;
        $manager->PageName              =   "BayiHesap".$bayiId;
        $tarihHata                      =   $cf->TarihSiniri($tar1,$tar2);
        if($suankiSayfa==null)
            $suankiSayfa=1;

        if($session==null || !$tarihHata)
        {
            $manager->GetAllData();
            $islemTuru=$manager->IslemTuru;
            $tar1=$manager->Tarih1;
            $tar2=$manager->Tarih2;
           
        }

        if($tar1==null)
            $tar1=date('Y-m-d', time());
        
        $filtreler  ="&islemTuru=$islemTuru&id=$bayiId&session=$session&tarih1=$tar1&tarih2=$tar2";
        $sorguArr=array($bayiId,$tar1);
        $sorgu="SELECT h.*,i.adi FROM kullanicihesaphareketleri h, islemturu i Where i.id=h.islemTuruId AND
        h.kullaniciId=? AND h.created_at >= ? ";
        $sorguFiltre=" ";
        if($tar2!=null)
        {
            $sorguFiltre=$sorguFiltre."AND h.created_at <= ? + INTERVAL 1 DAY ";
            array_push($sorguArr,$tar2);
        }
        if($islemTuru!=null && $islemTuru!=-1)
        {
            $sorguFiltre=$sorguFiltre."AND i.id=? ";
            array_push($sorguArr,$islemTuru);
        }
        $sorgu=$sorgu.$sorguFiltre;
        $count=$cf->GetCount($sorgu,$sorguArr);
        $sorgu=$sorgu." Order By h.id desc";
        $hareketler=$cf->Paginate($sorgu,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);
        $kullanici=DB::select("SELECT takmaAd,id FROM kullanici WHERE id=? LIMIT 1",array($bayiId));


        $manager->IslemTuru=$islemTuru;
        $manager->Tarih1=$tar1;
        $manager->Tarih2=$tar2;
        $manager->SetAllData();
        return view("bayilistesi/Bayihareketleri",array("kullanici"=>$kullanici[0],
                                                        "hareketler"=>$hareketler,
                                                        "filtreler"=>$filtreler,
                                                        "suankiSayfa"=>$suankiSayfa,
                                                        "sayfaSayisi"=>$cf->SayfaSayisi,
                                                        "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi,
                                                        "tarihHata"=>$tarihHata

                                                        ));
    }
    public function UpdateFiyat(Request $request)
    {
        try
        {
            $id=$request->id;
            $sorguUcret=$request->sorguUcret;
            $bayi =Kullanici::where("id",$id)->first();
            $bayi->sorguUcret=$sorguUcret;
            $bayi->save();
            

            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı!"
                
            ]);
        }catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
    }
    public function Temizle(Request $request)
    {
        $manager = new SessionManager;
        $manager->PageName="Bayiler";
        $manager->ClearAllData();
        return redirect("bayilistesi-bayiler");
    }
    public function Bayiler(Request $request)
    {
        $kullaniciAdi   = $request->tel;
        $session        = $request->session;
        $manager        = new SessionManager;
        $manager->PageName="Bayiler";
        
        if($session==null)
        {
            $manager->GetAllData();
            $kullaniciAdi=$manager->Tel;
        }

        $filtreler  ="&tel=$kullaniciAdi";

        $sayfadaGosterilecekKayitSayisi=25;
        $suankiSayfa=$request->sayfa;
        if($suankiSayfa==null)
            $suankiSayfa=1;
        
        $cf= new CommonFunctions;
        //$count=DB::select("SELECT count(id) as toplam FROM kullanici ");
        $sorgu="SELECT k.*  FROM kullanici k WHERE k.rolId=2  ";
        $sorguArr=array();
        if($kullaniciAdi!=null)
        {
            $sorgu=$sorgu."AND k.takmaAd=? ";
            array_push($sorguArr,$kullaniciAdi);
        }
        
        

        $count=$cf->GetCount($sorgu,$sorguArr);
        $sorgu=$sorgu." Order By k.aktif DESC ,k.firmaAdi asc ";
        $bayiler=$cf->Paginate($sorgu,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count);
        $bakiyeToplami=DB::select("SELECT SUM(bakiye) toplam FROM kullanici WHERE rolId!=1 AND aktif=1");

        $manager->Tel=$kullaniciAdi;
        $manager->SetAllData();

        return view("bayilistesi/Bayiler",array("bayiler"=>$bayiler,
                                            "toplamBakiye"=>$bakiyeToplami[0]->toplam,
                                            "suankiSayfa"=>$suankiSayfa,
                                            "sayfaSayisi"=>$cf->SayfaSayisi,
                                            "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi,
                                            "filtreler"=>$filtreler
                                            ));
    }
    public function ParaEkrani(Request $request)
    {
        $takmaAd=DB::select("SELECT takmaAd FROM kullanici WHERE id=? LIMIT 1",array($request->id));
        return view("bayilistesi/BayiPara",array("kullanici"=>$takmaAd[0],"id"=>$request->id,"cikar"=>$request->cikar));
    }
    public function Ekle(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $r=Kullanici::where("id",$request->id)->first();
            $oncekiBakiye = $r->bakiye;
            $r->bakiye=$r->bakiye+$request->tutar;
            $r->sonDegisiklikYapan=Auth::user()->takmaAd;
            $r->save();
            $bayiHareketleri=new Kullanicihesaphareketleri;
            $bayiHareketleri->islemTuruId=1;//1 para ekle
            $bayiHareketleri->kullaniciId=$request->id;
            $bayiHareketleri->aciklama=$request->aciklama;
            $bayiHareketleri->oncekiBakiye=$oncekiBakiye;
            $bayiHareketleri->sonrakiBakiye=$oncekiBakiye+$request->tutar;
            $bayiHareketleri->sonDegisiklikYapan=Auth::user()->takmaAd;
            $bayiHareketleri->tarih=date('Y-m-d H:i:s', time());
            $bayiHareketleri->save();
            DB::commit();

            return response()->json([ 
              
                "status"=>"true",
                "message"=>"İslem Başarılı! "
                
            ]);
    
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
        
    }
    public function Cikar(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $r=Kullanici::where("id",$request->id)->first();
            $oncekiBakiye = $r->bakiye;
            $r->bakiye=$r->bakiye-$request->tutar;
            $r->sonDegisiklikYapan=Auth::user()->takmaAd;
            $r->save();
            $bayiHareketleri=new Kullanicihesaphareketleri;
            $bayiHareketleri->islemTuruId=2;//2 para ekle
            $bayiHareketleri->kullaniciId=$request->id;
            $bayiHareketleri->aciklama=$request->aciklama;
            $bayiHareketleri->oncekiBakiye=$oncekiBakiye;
            $bayiHareketleri->sonrakiBakiye=$oncekiBakiye-$request->tutar;
            $bayiHareketleri->sonDegisiklikYapan=Auth::user()->takmaAd;
            $bayiHareketleri->tarih=date('Y-m-d H:i:s', time());
            $bayiHareketleri->save();
            DB::commit();

            return response()->json([ 
              
                "status"=>"true",
                "message"=>"İslem Başarılı! "
                
            ]);
    
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
        
    }  
    public function UpdateSw(Request $request)
    {
        try
        {
            $id=$request->id;
            $status=$request->status;
            $type=$request->type;
            $kullanici=Kullanici::where("id",$id)->first();
            switch ($type) {
                case 'aktif':
                    $kullanici->aktif=$status;
                    break;
                case 'sorgu':
                    $kullanici->yetkiSorgu=$status;
                    break;
                case 'yukle':
                    $kullanici->yetkiYukle=$status;
                    break;
                case 'fatura':
                    $kullanici->yetkiFatura=$status;
                    break;
                default:
                    # code...
                    break;
            }

            $kullanici->save();

            return response()->json([
                "status"=>"true",
                "message"=>"İslem Başarılı!"
                
            ]);
        }catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"
                
            ]);
        }
    }

}



