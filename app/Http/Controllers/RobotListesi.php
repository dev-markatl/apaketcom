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
use App\Models\Robot;
use App\Models\Istek;
use App\Models\Robothesaphareketleri;
use App\Models\Islemturu;
use App\Models\Genelayarlar;
use App\Models\Fiyatgrup;
use App\Classes\CommonFunctions;
use App\Classes\SessionManager;
use Carbon\Carbon;



class RobotListesi
{
    public function HesapHareketleriTemizle(Request $request)
    {
        $manager            =   new SessionManager;
        $robotId             =   $request->id;
        $manager->PageName  =   "RobotHesap".$robotId;
        $manager->Tarih1    =   null;
        $manager->Tarih2    =   null;
        $manager->IslemTuru =   -1;
        $manager->SetAllData();
        return redirect("ajax/RobotHesapHareketleri?id=$robotId");
    }
    public function HesapHareketleri(Request $request)
    {
        $robotId                         =   $request->id;
        $session                        =   $request->session;
        $islemTuru                      =   $request->islemTuru;
        $tar1                           =   $request->tarih1;
        $tar2                           =   $request->tarih2;
        $sayfadaGosterilecekKayitSayisi =   25;
        $suankiSayfa                    =   $request->sayfa;
        $cf                             =   new CommonFunctions;
        $manager                        =   new SessionManager;
        $manager->PageName              =   "RobotHesap".$robotId;
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

        $filtreler  ="&islemTuru=$islemTuru&id=$robotId&session=$session&tarih1=$tar1&tarih2=$tar2";
        $sorguArr=array($robotId,$tar1);
        $sorgu="SELECT h.*,i.adi FROM robothesaphareketleri h, islemturu i Where i.id=h.islemTuruId AND
        h.robotId=? AND h.created_at >= ?";
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


        $robot=DB::select("SELECT adi,id FROM robot WHERE id=? LIMIT 1",array($robotId));

        $manager->IslemTuru=$islemTuru;
        $manager->Tarih1=$tar1;
        $manager->Tarih2=$tar2;
        $manager->SetAllData();

        return view("robotlistesi/RobotHareketleri",array("robot"=>$robot[0],
                                                        "hareketler"=>$hareketler,
                                                        "suankiSayfa"=>$suankiSayfa,
                                                        "filtreler"=>$filtreler,
                                                        "sayfaSayisi"=>$cf->SayfaSayisi,
                                                        "kayitSayisi"=>$sayfadaGosterilecekKayitSayisi,
                                                        "tarihHata"=>$tarihHata

                                                        ));
    }

    public function Ekle(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $r=Robot::where("id",$request->id)->first();
            $oncekiBakiye = $r->sistemBakiye;
            $pos=$r->posBakiye;
            $r->sistemBakiye=$r->sistemBakiye+$request->tutar;
            $r->sonDegisiklikYapan=Auth::user()->takmaAd;
            $r->save();
            $robotHareketleri=new Robothesaphareketleri;
            $robotHareketleri->islemTuruId=1;//1 para ekle
            $robotHareketleri->robotId=$request->id;
            $robotHareketleri->aciklama=$request->aciklama;
            $robotHareketleri->oncekiBakiyeSistem=$oncekiBakiye;
            $robotHareketleri->sonrakiBakiyeSistem=$oncekiBakiye+$request->tutar;
            $robotHareketleri->posBakiye=$pos;
            $robotHareketleri->sonDegisiklikYapan=Auth::user()->takmaAd;
            $robotHareketleri->tarih=date('Y-m-d H:i:s', time());
            $robotHareketleri->save();
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
            $r=Robot::where("id",$request->id)->first();
            $oncekiBakiye = $r->sistemBakiye;
            $pos=$r->posBakiye;
            $r->sistemBakiye=$r->sistemBakiye-$request->tutar;
            $r->sonDegisiklikYapan=Auth::user()->takmaAd;
            $r->save();
            $robotHareketleri=new Robothesaphareketleri;
            $robotHareketleri->islemTuruId=2;//2 para cikar
            $robotHareketleri->robotId=$request->id;
            $robotHareketleri->aciklama=$request->aciklama;
            $robotHareketleri->oncekiBakiyeSistem=$oncekiBakiye;
            $robotHareketleri->sonrakiBakiyeSistem=$oncekiBakiye-$request->tutar;
            $robotHareketleri->posBakiye=$pos;
            $robotHareketleri->sonDegisiklikYapan=Auth::user()->takmaAd;
            $robotHareketleri->tarih=date('Y-m-d H:i:s', time());
            $robotHareketleri->save();
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
    public function TopluGuncelle(Request $request)
    {
        try
        {
            $idler = $request->Cb;
            $durum = $request->Durum;
            $sorgu = "UPDATE robot SET aktif=? Where id IN(";
            foreach($idler as $id)
            {
                //toplu update sql i calıstırılabilir
                //UPDATE paket SET sorguyaEkle=1 Where id IN(1,2,3)

                $sorgu=$sorgu.$id.",";

            }
            $sorgu=substr($sorgu,0,strlen($sorgu)-1);
            $update=DB::select($sorgu." )",array($durum));

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
    public function YeniRobot(Request $request)
    {
        try
        {

            $r=new Robot;
            $r->adi=$request->adi;
            $r->sifre=$request->sifre;
            $r->aktif=$request->aktif;
            $r->operatorId=$request->operator;
            $r->kullaniciId=$request->bayi;
            $r->kullanici2=$request->bayi2;
            $r->robotTipId=$request->turu;
            $r->yetkiYukle=$request->yukle;
            $r->yetkiSorgu=$request->sorgu;
            $r->yetkiFatura=$request->fatura;
            $r->fiyatgrubuId = $request->fiyatgrup;
            $r->sonDegisiklikYapan=Auth::user()->takmaAd;

            $r->save();
            return response()->json([

                "status"=>"true",
                "message"=>"İslem Başarılı! "

            ]);

        }
        catch(\Exception $e)
        {
            $message="";
            if($e->getCode()==23000)
                $message="Bu robotadi kullanımda";
            return response()->json([
                "status"=>"false",
                "message"=>"Işlem Başarısız!(".$e->getCode().") $message"

            ]);
        }

    }
    public function RobotGuncelle(Request $request)
    {
        try
        {

            $r=Robot::where("id",$request->id)->first();
            $r->adi=$request->adi;
            $r->sifre=$request->sifre;
            $r->aktif=$request->aktif;
            $r->operatorId=$request->operator;
            $r->kullaniciId=$request->bayi;
            $r->kullanici2=$request->bayi2;
            $r->robotTipId=$request->turu;
            $r->yetkiYukle=$request->yukle;
            $r->yetkiSorgu=$request->sorgu;
            $r->yetkiFatura=$request->fatura;
            $r->fiyatgrubuId = $request->fiyatgrup;
            $r->sure_siniri = $request->sure_siniri;
            $r->sonDegisiklikYapan=Auth::user()->takmaAd;

            $r->save();
            return response()->json([

                "status"=>"true",
                "message"=>"İslem Başarılı! "

            ]);

        }
        catch(\Exception $e)
        {
            return response()->json([
                "status"=>"false",
                "message"=>"İslem Başarısız! (".$e->getMessage().")"

            ]);
        }

    }

    public function RobotOzellikleri(Request $request)
    {
        try
        {
            $id=$request->id;
            $fiyatGrup = Fiyatgrup::where("aktif",1)->get();
            if($id>0)
            {
                $r = Robot::where("id",$id)->first();

                return view("robotlistesi/YeniRobot",array(
                "update"=>true,
                "id"=>$r->id,
                "adi"=>$r->adi,
                "sifre"=>$r->sifre,
                "operator"=>$r->operatorId,
                "turu"=>$r->robotTipId,
                "sorgu"=>$r->yetkiSorgu,
                "yukle"=>$r->yetkiYukle,
                "aktif"=>$r->aktif,
                "fatura"=>$r->yetkiFatura,
                "bayi"=>$r->kullaniciId,
                "bayi2"=>$r->kullanici2,
                "aktifgrup"=>$r->fiyatgrubuId,
                "fiyatgruplar"=>$fiyatGrup,
                "robotSureSiniri"=>$r->sure_siniri,
                "status"=>"true",
                "message"=>"İslem Başarılı! "));
            }
            else
            {
                return view("robotlistesi/YeniRobot",array(
                    "update"=>"",
                    "id"=>"",
                    "adi"=>"",
                    "sifre"=>"",
                    "operator"=>"",
                    "turu"=>"",
                    "sorgu"=>"",
                    "yukle"=>"",
                    "aktif"=>"",
                    "fatura"=>"",
                    "bayi"=>"",
                    "bayi2"=>"",
                    "aktifgrup"=>"0",
                    "fiyatgruplar"=>$fiyatGrup,
                    "robotSureSiniri"=>"0",
                    "status"=>"true",
                    "message"=>"İslem Başarılı! "));
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
    public function ParaEkrani(Request $request)
    {
        $robot=DB::select("SELECT adi FROM robot WHERE id=? LIMIT 1",array($request->id));

        return view("robotlistesi/RobotPara",array("robot"=>$robot[0],"id"=>$request->id,"cikar"=>$request->cikar));
    }
    public function Temizle(Request $request)
    {
        $manager    = new SessionManager;
        $manager->PageName="RobotListesi";
        $manager->Durum=1;
        $manager->SetDataD();
        $manager->Operator=-1;
        $manager->SetDataO();
        return redirect("robotlistesi-robotlar");
    }

    public function TumRobotlar(Request $request)
    {
        $operator   = $request->operator;
        $durum      = $request->durum;
        $session    = $request->session;
        $manager    = new SessionManager;
        $manager->PageName="RobotListesi";
        $sorguArr   = array();
        $sorgu      ="SELECT
                            r.id,
                            r.adi as robotAdi,
                            k1.firmaAdi as kulAdi,
                            k2.firmaAdi as kul2Adi,
                            rt.adi as robotTuru,
                            r.sistemBakiye,
                            r.posBakiye,
                            r.yetkiSorgu as sorgu,
                            r.yetkiYukle as yukle,
                            r.yetkiFatura as fatura,
                            r.aktif,
                            r.yukleyici,
                            r.sure_siniri,
                            r.fiyatgrubuId
                        FROM
                            robot r,
                            kullanici k1,
                            kullanici k2,
                            robottip rt
                        WHERE
                            r.kullaniciId = k1.id AND
                            r.kullanici2 = k2.id AND
                            r.robotTipId=rt.id AND
                            r.id!=1";

                            /*
                        WHERE
                            r.kullaniciId=k.id AND
                            r.kullanici2 = k.id AND
                            r.robotTipId=rt.id AND
                            r.id!=1";
                            */

        if($session==null)
        {
            $manager->GetDataO();
            $manager->GetDataD();
            $operator=$manager->Operator;
            $durum=$manager->Durum;
            if($durum==null )
            {
                $durum=1;
            }
        }


        if($operator!=-1 && $operator!=null )
        {
        array_push($sorguArr,$operator);
        $sorgu=$sorgu." AND r.operatorId=?";
        }
        if( $durum!=-1 && $durum!=null)
        {
        array_push($sorguArr,$durum);
        $sorgu=$sorgu." AND r.aktif=?";
        }
        $manager->Durum=$durum;
        $manager->SetDataD();
        $manager->Operator=$operator;
        $manager->SetDataO();
        $sorgu=$sorgu." ORDER BY
                        r.aktif DESC,
                        r.yukleyici ASC,       
                         
                        r.fiyatgrubuId ASC,      
                        r.adi ASC,      
                        
                                    
                        rt.adi DESC";
        $robotlar=DB::select($sorgu,$sorguArr);
        $bakiyeToplam=DB::select("SELECT SUM(posBakiye) as posToplam , SUM(sistemBakiye) as sistemToplam FROM robot WHERE aktif=1 ");

        $genelAyar=Genelayarlar::where("id","1")->first();

        if ($genelAyar->olumsuzSorguTekrar == 0)
        {
          $olumsuzCheck = "";
        }
        else
        {
          $olumsuzCheck = "checked";
        }


        if ($genelAyar->sistemiKapat == 0)
        {
          $sistemiKapatCheck = "";
        }
        else
        {
          $sistemiKapatCheck = "checked";
        }

        if ($genelAyar->sistemiKapatYukleme == 0)
        {
          $sistemiKapatYuklemeCheck = "";
        }
        else
        {
          $sistemiKapatYuklemeCheck = "checked";
        }

        if ($genelAyar->sistemiKapatGNC == 0)
        {
          $sistemiKapatGNCCheck = "";
        }
        else
        {
          $sistemiKapatGNCCheck = "checked";
        }

        if ($genelAyar->sistemiKapatYuklemeGNC == 0)
        {
          $sistemiKapatYuklemeGNCCheck = "";
        }
        else
        {
          $sistemiKapatYuklemeGNCCheck = "checked";
        }


        if ($genelAyar->istekIptalAktif == 0)
        {
          $sureliIptalYuklemeCheck = "";
        }
        else
        {
          $sureliIptalYuklemeCheck = "checked";
        }

        

        foreach ($robotlar as $robot)
        {
            $sonIslem = Istek::where('robotId',$robot->id)->orderBy('id','desc')->first();

            if(!$sonIslem)
            {
                $robot->sonGorulme = "-";
                $robot->tabloRenk = "black";

            }
            else
            {

                $tarih = Carbon::now();
                $robotTarih = $sonIslem->updated_at;
                $fark = $tarih->diffInMinutes($robotTarih);

                if($fark >= 60)
                {
                    $robot->tabloRenk = "red";
                }
                else
                {
                    $robot->tabloRenk = "black";
                }


                $robot->sonGorulme = Carbon::parse($sonIslem->updated_at)->format("d/m H:i");

            }

            if ($robot->fiyatgrubuId == 0)
            {
                $robot->fiyatgrubuId = "ANA FIYAT GRUBU";
            }
            else
            {
                $fiyatGrubu=Fiyatgrup::where("id",$robot->fiyatgrubuId)->first();
                $robot->fiyatgrubuId = $fiyatGrubu->grup_ad;
            }

        }

        return view("robotlistesi/Robotlar",array("robotlar"=>$robotlar,"robotBakiye"=>$bakiyeToplam[0],
        "olumsuzCheck"=>$olumsuzCheck,"sistemiKapatCheck"=>$sistemiKapatCheck,
        "sistemiKapatYuklemeCheck"=>$sistemiKapatYuklemeCheck,
        "sistemiKapatGNCCheck"=>$sistemiKapatGNCCheck,
        "sistemiKapatYuklemeGNCCheck"=>$sistemiKapatYuklemeGNCCheck,
        "sureliIptalYuklemeCheck"=>$sureliIptalYuklemeCheck));
    }
    public function UpdateSw(Request $request)
    {
        try
        {
            $id=$request->id;
            $status=$request->status;
            $type=$request->type;
            $robot=Robot::where("id",$id)->first();
            switch ($type) {
                case 'aktif':
                    $robot->aktif=$status;
                    break;
                case 'sorgu':
                    $robot->yetkiSorgu=$status;
                    break;
                case 'yukle':
                    $robot->yetkiYukle=$status;
                    break;
                case 'fatura':
                    $robot->yetkiFatura=$status;
                    break;
                case 'yukleyici':
                    $robot->yukleyici=$status;
                    break;
                default:
                    # code...
                    break;
            }




            $robot->save();

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

    public function UpdateSureSinir(Request $request)
    {
        try
        {
            $id = $request->id;
            $sureSinir = $request->value;

            $robot = Robot::where("id",$id)->first();
            $robot->sure_siniri = $sureSinir;
            $robot->save();

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


    public function OlumsuzSorguTekrar(Request $request)
    {
      try
      {
          //$id=$request->id;
          $status=$request->status;
          $type=$request->type;
          $genelAyar=Genelayarlar::where("id","1")->first();
          $genelAyar->olumsuzSorguTekrar=$status;
          $genelAyar->save();

          return response()->json([
              "status"=>"true",
              "message"=>"İşlem Başarılı!"

          ]);
      }catch(\Exception $e)
      {
          return response()->json([
              "status"=>"false",
              "message"=>"İşlem Başarısız! (".$e->getMessage().")"

          ]);
      }

    }

    public function SistemiKapat(Request $request)
    {
      try
      {
          //$id=$request->id;
          $status=$request->status;
          $type=$request->type;
          $genelAyar=Genelayarlar::where("id","1")->first();

          if($type == "gnc")
          {
            $genelAyar->sistemiKapatGNC=$status;
          }
          else if($type == "normal")
          {
            $genelAyar->sistemiKapat=$status;
          }

          $genelAyar->save();

          return response()->json([
              "status"=>"true",
              "message"=>"İşlem Başarılı!"

          ]);
      }catch(\Exception $e)
      {
          return response()->json([
              "status"=>"false",
              "message"=>"İşlem Başarısız! (".$e->getMessage().")"

          ]);
      }

    }

    public function SistemiKapatYukleme(Request $request)
    {
      try
      {
          //$id=$request->id;
          $status=$request->status;
          $type=$request->type;
          $genelAyar=Genelayarlar::where("id","1")->first();

          if($type == "gnc")
          {
            $genelAyar->sistemiKapatYuklemeGNC=$status;
          }
          else if($type == "normal")
          {
            $genelAyar->sistemiKapatYukleme=$status;
          }

          $genelAyar->save();

          return response()->json([
              "status"=>"true",
              "message"=>"İşlem Başarılı!"

          ]);
      }catch(\Exception $e)
      {
          return response()->json([
              "status"=>"false",
              "message"=>"İşlem Başarısız! (".$e->getMessage().")"

          ]);
      }

    }

    public function SureliIptalYukleme(Request $request)
    {
      try
      {
          //$id=$request->id;
          $status=$request->status;
          $type=$request->type;
          $genelAyar=Genelayarlar::where("id","1")->first();
          $genelAyar->istekIptalAktif=$status;
          $genelAyar->save();

          return response()->json([
              "status"=>"true",
              "message"=>"İşlem Başarılı!"

          ]);
      }catch(\Exception $e)
      {
          return response()->json([
              "status"=>"false",
              "message"=>"İşlem Başarısız! (".$e->getMessage().")"

          ]);
      }

    }

}
