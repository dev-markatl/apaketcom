<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\takip;
use App\bot;
use App\Classes\fonk;
use Validator;
use Illuminate\Support\Facades\DB;
class kontortoplamraporu extends Controller
{
    public function getIndex()
    {
    	$id    = Auth::User()->id;
        $now   = date("Y-m-d");
        $bot   = bot::whereRaw('kul_id =? ORDER BY np',array($id))->get();
        if ( isset($_COOKIE["kontortoplamraporu-tar1"])|| isset($_COOKIE["kontortoplamraporu-tip_operator"]) || isset($_COOKIE["kontortoplamraporu-kod"]) || isset($_COOKIE["kontortoplamraporu-filtre"]))
        {
            
            if (isset($_COOKIE["kontortoplamraporu-tar1"])) $tar1=$_COOKIE["kontortoplamraporu-tar1"]; else $tar1=null;
            if (isset($_COOKIE["kontortoplamraporu-tar2"])) $tar2=$_COOKIE["kontortoplamraporu-tar2"]; else $tar2=null;
            if (isset($_COOKIE["kontortoplamraporu-tip_operator"])) $tip_operator=$_COOKIE["kontortoplamraporu-tip_operator"]; else $tip_operator=null;
            if (isset($_COOKIE["kontortoplamraporu-kod"])) $kod=$_COOKIE["kontortoplamraporu-kod"]; else $kod=null;
            
            if (isset($_COOKIE["kontortoplamraporu-filtre"])) $filtre=$_COOKIE["kontortoplamraporu-filtre"]; else $filtre=null;

            $takip=$this->filtre_sorgu( $tip_operator, $tar1,$tar2,$kod,$filtre);
            $renk="red";
            return view("raporlar-kontortoplamraporu",array("kod"=>$kod, "tip_operator"=>$tip_operator,"tar1"=>$tar1,"tar2"=>$tar2,"takip"=>$takip,"kodlar"=>$bot,"filtre"=>$filtre,"temizle"=> $renk ));
        }
        else
        {
            $renk="#186ef1";
            $sorgu_bilesenleri="SELECT t.operator, gt.tip, kt.paket, gt.durum, kt.znet_id, count(gt.id) as adet, sum(gt.tutar) as e_maliyet, 
                sum(t.tutar) as s_maliyet, sum(t.kar) as g_kar 
                FROM ut_gezdirme_takip gt, takip t, kt_paket kt
                WHERE t.kul_id = ? AND t.durum = gt.durum AND t.durum = 1 AND 
                gt.takip_id = t.id AND t.kontor = kt.znet_id AND gt.tip = kt.tip AND t.operator = kt.operator AND gt.bot_tar >= ? 
                GROUP BY t.operator, gt.tip, kt.paket, kt.znet_id, gt.durum
                ORDER BY t.operator, gt.tip, kt.paket, kt.znet_id, gt.durum";
            $sorgu_degiskenleri=array($id,$now);
            $takip=DB::select($sorgu_bilesenleri,$sorgu_degiskenleri);
           
           //dd($takip);
            return view("raporlar-kontortoplamraporu",array("takip"=>$takip,"tar1"=>$now, "kodlar"=>$bot ,"temizle"=> $renk));
        }
    	
    }	 

    public function getFiltre(Request $request)
    {
            $id = Auth::User()->id;
            
           
           $kontrol = Validator::make($request->all(),array(
                "tip"     => "",
                "tarih1"        => "",
                "tarih2"        => "",
                "operator"        => "",
                "kod"           => "",
                "filtre"           => ""
                ));
            if($kontrol->fails())
            {
                return Redirect()->to('kontor-yuklemetakip')->withErrors($kontrol)->withImput(null);
            }
            else
            {
                
                $sorgu_bilesenleri="kul_id =?";
                $sorgu_degiskenleri=array($id);


                $tip            = $request->input("tip");
                $tar1           = $request->input("tarih1");
                $tar2           = $request->input("tarih2");
                $tip_operator   = $request->input("tip_operator");
                $kod            = $request->input("kod");
                $filtre         = $request->input("filtre");
                
                if($tip_operator!=null)
                    setcookie("kontortoplamraporu-operator", $tip_operator);

                if($kod!=null)
                    setcookie("kontortoplamraporu-kod", $kod);

               
                
                if($tar1!=null)
                   setcookie("kontortoplamraporu-tar1", $tar1);
                
                if($tar2!=null)
                    setcookie("kontortoplamraporu-tar2", $tar2);
                
                if($filtre!=null)
                    setcookie("kontortoplamraporu-filtre", $filtre);
                
                

                $bot   = bot::whereRaw('kul_id =? ORDER BY np',array($id))->get();
               // $takip1 = takip::whereRaw($sorgu_bilesenleri,$sorgu_degiskenleri)->orderBy('id', 'DESC')->paginate(30);
               $takip1=$this->filtre_sorgu( $tip_operator, $tar1,$tar2,$kod,$filtre);
                //$login_kontrol = kullanicilar::whereRaw('kul_adi =? AND sifre=? ',array($tel,$sifre))->first();
                
                //return view("anasayfa-adminuyarilar",array("login"=>$login_kontrol));
                
                $renk="red";
                
                   
                   return view("raporlar-kontortoplamraporu",array("kod"=>$kod, "tip_operator"=>$tip_operator,"tar1"=>$tar1,"tar2"=>$tar2,"takip"=>$takip1,"kodlar"=>$bot,"filtre"=>$filtre,"temizle"=> $renk));
        }
    }
    public function filtre_sorgu($tip_operator,$tar1,$tar2,$kod,$filtre)
    {
        $id = Auth::User()->id;
        
        if(!isset($tip_operator))
            $tip_operator=null;
        if(!isset($tar1))
            $tar1=null;
        if(!isset($tar2))
            $tar2=null;
        if(!isset($kod))
            $kod=null;
        if(!isset($filtre))
            $filtre=null;

        $kul_id    = Auth::User()->id;

        $sorgu_bilesenleri="SELECT t.operator, gt.tip, kt.paket, gt.durum, kt.znet_id, count(gt.id) as adet, sum(gt.tutar) as e_maliyet, 
                sum(t.tutar) as s_maliyet, sum(t.kar) as g_kar 
                FROM ut_gezdirme_takip gt, takip t, kt_paket kt
                WHERE t.kul_id = ? AND t.durum = gt.durum AND 
                gt.takip_id = t.id AND t.kontor = kt.znet_id AND gt.tip = kt.tip AND t.operator = kt.operator  ";
                //GROUP BY t.operator, gt.tip, kt.paket
                //ORDER BY t.operator, gt.tip, kt.paket";
        $sorgu_degiskenleri=array($kul_id);
        



        $tip_op             = array();
        $tip                = null;
        $operator           = null;
        $kar                = null;

        if($tip_operator=="avea" || $tip_operator=="vodafone")
        {
            $tip        =   null;
            $operator   =   $tip_operator;
        }
        elseif($tip_operator!=null && $tip_operator!="1")
        {
            $tip_op     =   explode("-", $tip_operator);
            $tip        =   $tip_op[1];
            $operator   =   $tip_op[0];
            if($tip=="kar")
            {
                $tip = null;
                $kar = 1;
            }
        }


        if($kar!=null)
        {
            
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND t.kar>0 ";
        }






        if($kod!=null)
        {
            array_push($sorgu_degiskenleri, $kod);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND gt.bot_kodu=? ";
        }
        if($tip!=null)
        {
            array_push($sorgu_degiskenleri, $tip);
            $sorgu_bilesenleri=$sorgu_bilesenleri."  AND gt.tip=? ";
        }
        if($operator!=null)
        {
            array_push($sorgu_degiskenleri, $operator);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND t.operator=? ";
        }
         
        
        if($tar1!=null)
        {
            array_push($sorgu_degiskenleri, $tar1);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND gt.bot_tar >= ? ";
        }
        if($tar2!=null)
        {
            array_push($sorgu_degiskenleri, $tar2);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND gt.bot_tar <= ?  + INTERVAL 1 DAY ";
        }
        if($filtre!=null)
        {
            if($filtre=="2")
            {
                //array_push($sorgu_degiskenleri, $filtre);
                $sorgu_bilesenleri=$sorgu_bilesenleri." AND gt.durum in (2,4,5,6,7)"; 
            }
            if($filtre==1)
            {
                //array_push($sorgu_degiskenleri, $filtre);
                $sorgu_bilesenleri=$sorgu_bilesenleri." AND gt.durum in(1,9) "; 
            }
            if($filtre==3)
            {
                //array_push($sorgu_degiskenleri, $filtre);
                $sorgu_bilesenleri=$sorgu_bilesenleri." AND gt.durum in(3,8) "; 
            }
        }
        
        $sorgu_bilesenleri=$sorgu_bilesenleri."GROUP BY t.operator, gt.tip, kt.paket, kt.znet_id, gt.durum ORDER BY t.operator, gt.tip, kt.paket, kt.znet_id, gt.durum";
        $sorgu= DB::select($sorgu_bilesenleri,$sorgu_degiskenleri);
       return $sorgu;
        
    }

}
