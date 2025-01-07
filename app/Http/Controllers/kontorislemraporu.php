<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\takip;
use App\bot;
use App\Classes\fonk;
use Validator;
use Illuminate\Support\Facades\DB;
use Log;
class kontorislemraporu extends Controller
{
    public function getIndex()
    {
    	$id    = Auth::User()->id;
        $now   = date("Y-m-d");
        $bot   = bot::whereRaw('kul_id =? ORDER BY np',array($id))->get();
        if (isset($_COOKIE["kontorislemraporu-tar1"])|| isset($_COOKIE["kontorislemraporu-tip_operator"]) || isset($_COOKIE["kontorislemraporu-kod"]) || isset($_COOKIE["kontorislemraporu-tel"]) || isset($_COOKIE["kontorislemraporu-filtre"]) || isset($_COOKIE["kontorislemraporu-paket"]))
        {
            
            if (isset($_COOKIE["kontorislemraporu-tar1"])) $tar1=$_COOKIE["kontorislemraporu-tar1"]; else $tar1=$now;
            if (isset($_COOKIE["kontorislemraporu-tar2"])) $tar2=$_COOKIE["kontorislemraporu-tar2"]; else $tar2=null;
            if (isset($_COOKIE["kontorislemraporu-tip_operator"])) $tip_operator=$_COOKIE["kontorislemraporu-tip_operator"]; else $tip_operator=null;
            if (isset($_COOKIE["kontorislemraporu-kod"])) $kod=$_COOKIE["kontorislemraporu-kod"]; else $kod=null;
            if (isset($_COOKIE["kontorislemraporu-tel"])) $tel=$_COOKIE["kontorislemraporu-tel"]; else $tel=null;
            if (isset($_COOKIE["kontorislemraporu-filtre"])) $filtre=$_COOKIE["kontorislemraporu-filtre"]; else $filtre=null;
            if (isset($_COOKIE["kontorislemraporu-paket"])) $paket=$_COOKIE["kontorislemraporu-paket"]; else $paket=null;

            $takip=$this->filtre_sorgu( $tip_operator, $tar1,$tar2,$kod,$tel,$filtre,$paket);
            //$takip=DB::table("table2")->paginate(30);
            if($tip_operator!=null && $tip_operator!="1")
                    $paket_select=$this->paketSelect($tip_operator,$paket); //buraya $returnHTML yani paket dropdown objesi geliyor.
                else
                    $paket_select=null;
            $renk="red";
            return view("raporlar-kontorislemraporu",array("kod"=>$kod, "tar1"=>$tar1,"tar2"=>$tar2,"takip"=>$takip,"tip_operator"=>$tip_operator,"kodlar"=>$bot,"tel"=>$tel,"temizle"=> $renk ,"filtre"=>$filtre, "tip-op"=>$paket,"paketler"=>$paket_select));
        }
        else
        {//İd basılacak-istenen ve yüklenene ---- "durum" - nkod olarak düzeltilecek
            $renk="#186ef1";
            $takip=DB::table("uv_islem_rapor")->whereRaw('kul_id =? AND islem_tar >=?',array($id,$now))->orderBy('islem_tar', 'DESC')->limit(1000)->get();
            return view("raporlar-kontorislemraporu",array("takip"=>$takip,"tar1"=>$now, "kodlar"=>$bot ,"temizle"=> $renk));
        }
    	
    }	 

    public function getFiltre(Request $request)
    {
        Log::info(' ilk ');
            $id = Auth::User()->id;
            
           
           $kontrol = Validator::make($request->all(),array(
                
                "tarih1"        => "",
                "tarih2"        => "",
                "kod"           => "",
                "telefonno"     => "",
                "filtre"     => ""
                ));
            if($kontrol->fails())
            {
                return Redirect()->to('raporlar-kontorislemraporu')->withErrors($kontrol)->withImput(null);
            }
            else
            {
                
                $sorgu_bilesenleri  = "kul_id =?";
                $sorgu_degiskenleri = array($id);


                

                $tar1           = $request->input("tarih1");
                $tar2           = $request->input("tarih2");
                $tip_operator   = $request->input("tip_operator");
                $kod            = $request->input("kod");
                $tel            = $request->input("telefonno");
                $filtre         = $request->input("filtre");
                $paket          = $request->input("paket_sec");
                
                if($tip_operator!=null && $tip_operator!="1")
                    setcookie("kontorislemraporu-tip_operator", $tip_operator);
                if($kod!=null)
                    setcookie("kontorislemraporu-kod", $kod);
                if($tar1!=null)
                   setcookie("kontorislemraporu-tar1", $tar1);
                if($tar2!=null)
                    setcookie("kontorislemraporu-tar2", $tar2);
                if($tel!=null)
                    setcookie("kontorislemraporu-tel", $tel);
                if($filtre!=null)
                    setcookie("kontorislemraporu-filtre", $filtre);
                if($paket!=null)
                    setcookie("kontorislemraporu-paket", $paket);
                
                if($tip_operator!=null && $tip_operator!="1")
                    $paket_select=$this->paketSelect($tip_operator,$paket); //buraya $returnHTML yani paket dropdown objesi geliyor.
                else
                    $paket_select=null;

                //$bot   = bot::whereRaw('kul_id =? ORDER BY np',array($id))->get(); ///??? bu ne işe yarıyor
                Log::info(' giris ');
                $bot    =DB::select("SELECT np FROM bot WHERE kul_id=? ORDER BY np",array($id));
                Log::info(' filtre_sorgu giris ');
                $takip1=$this->filtre_sorgu( $tip_operator, $tar1,$tar2,$kod,$tel,$filtre,$paket);
                Log::info(' cikis ');
                $renk="red";

                return view("raporlar-kontorislemraporu",array("kod"=>$kod, "tip_operator"=>$tip_operator,"tar1"=>$tar1,"tar2"=>$tar2,"takip"=>$takip1,"kodlar"=>$bot, "tel"=>$tel, "temizle"=> $renk,"filtre"=>$filtre, "tip-op"=>$paket, "paketler"=>$paket_select));
        }
    }
    public function filtre_sorgu($tip_operator,$tar1,$tar2,$kod,$tel,$filtre,$paket)
    {
        $id = Auth::User()->id;
        $now   = date("Y-m-d");
        if(!isset($tip_operator))
            $tip_operator=null;
        if(!isset($tar1))
            $tar1=null;
        if(!isset($tar2))
            $tar2=null;
        if(!isset($kod))
            $kod=null;
        if(!isset($tel))
            $tel=null;
        if(!isset($filtre))
            $filtre=null;
        if(!isset($paket))
            $paket=null;
        Log::info(' is null ');
        $kul_id    = Auth::User()->id;
        $sorgu_bilesenleri=" kul_id = ? ";

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

        Log::info(' sorgu_bilesenleri ');
        if($kar!=null)
        {
            
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND kar>0 ";
        }
        if($kod!=null)
        {
            array_push($sorgu_degiskenleri, $kod);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND bot_kodu=? ";
        }
        if($tip!=null)
        {
            array_push($sorgu_degiskenleri, $tip);
            $sorgu_bilesenleri=$sorgu_bilesenleri."  AND tip=? ";
        }
        if($operator!=null)
        {
            array_push($sorgu_degiskenleri, $operator);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND operator=? ";
        }
        if($tar1!=null)
        {
            array_push($sorgu_degiskenleri, $tar1);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND bot_tar >= ? ";
        }
        if($tar2!=null)
        {
            array_push($sorgu_degiskenleri, $tar2);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND bot_tar <= ? + INTERVAL 1 DAY ";
        }
        if($tel!=null)
        {
            array_push($sorgu_degiskenleri, $tel);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND gsmno = ? ";
        }
        if($filtre!=null)
        {
            if($filtre=="2")
            {
                //array_push($sorgu_degiskenleri, $filtre);
                $sorgu_bilesenleri=$sorgu_bilesenleri." AND durum in (2,4,5,6,7) "; 
            }
            if($filtre==1)
            {
                //array_push($sorgu_degiskenleri, $filtre);
                $sorgu_bilesenleri=$sorgu_bilesenleri." AND durum in(1,9) "; 
            }
            if($filtre==3)
            {
                //array_push($sorgu_degiskenleri, $filtre);
                $sorgu_bilesenleri=$sorgu_bilesenleri." AND durum in(3,8) "; 
            }
        }
        if($paket!=null && $paket!="-1")
        {
            array_push($sorgu_degiskenleri, $paket);
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND id = ? ";
        }
        Log::info(' sorgu_degiskenleri ');
       return $takip=DB::table("uv_islem_rapor")->whereRaw($sorgu_bilesenleri,$sorgu_degiskenleri)->orderBy('islem_tar', 'DESC')->limit(1000)->get();
        
    }

    public function tipFiltre(Request $request)
    {
        $id                 = Auth::User()->id;
        $tip_flt            = $request->input("sec_tip");
        $paket_sel          =$this->paketSelect($tip_flt,-1);
        return response()->json(array('msg'=> $paket_sel), 200);
    }

    public function paketSelect($tip_operator,$paket)
    {

        $id                 = Auth::User()->id;
        $tip_flt            = $tip_operator;
        $tip                = null;
        $operator           = null;
        $sorgu_bilesenleri  = "SELECT DISTINCT g.kt_paket_id, if(g.sahte_paket_adi is null, k.paket, g.sahte_paket_adi ) as paket_adi
                                FROM ut_gezdirme g, kt_paket k
                                WHERE g.kul_id = ? AND 
                                k.paket <> 'Alternatif Paket ismi icin Tiklayiniz' AND
                                g.kt_paket_id=k.id  ";
        $sorgu_degiskenleri = array($id);
       
        
        if($tip_flt=="avea" || $tip_flt=="vodafone")
        {
            $tip        = null;
            $operator   = $tip_flt;
            $sorgu_bilesenleri=$sorgu_bilesenleri." AND k.operator = ? ";
            array_push($sorgu_degiskenleri, $operator);
        }
        elseif($tip_flt!=null && $tip_flt!="1")
        {
            //dd($tip_flt);
            $tip_op             = explode("-", $tip_flt);
            $tip                = $tip_op[1];
            $operator           = $tip_op[0];
            $sorgu_bilesenleri  = $sorgu_bilesenleri." AND k.operator = ? ";
            $sorgu_bilesenleri  = $sorgu_bilesenleri." AND k.tip = ? ";   

            array_push($sorgu_degiskenleri, $operator);
            array_push($sorgu_degiskenleri, $tip);
        }


        $sorgu      = DB::select($sorgu_bilesenleri,$sorgu_degiskenleri);
        
        $returnHTML = '<label style=" margin-left: 160px; font-size: 12px; width:85px;">Paket Adı: </label> <select class="form-control" name="paket_sec" id="paket_sec" style="display:inline; font-size: 12px; font-weight: 100; outline: none; margin-top:5px;  height:32px; width:200px;">';
        $selected = "";
        if ($paket==-1) $returnHTML = $returnHTML."<option value=-1 selected >Seçiniz</option>"; 
        else $returnHTML = $returnHTML."<option value=-1>Seçiniz</option>"; 

        foreach ($sorgu as $key) 
        {
            if ($paket == $key->kt_paket_id) $selected = " selected "; else $selected="";
            $returnHTML = $returnHTML."<option value=".$key->kt_paket_id." ".$selected.">".$key->paket_adi."</option>";
        }
        $returnHTML = $returnHTML."</select>";
        return $returnHTML;

    }


}
