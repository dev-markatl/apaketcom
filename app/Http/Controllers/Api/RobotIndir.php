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
class RobotIndir 
{
	public function getDownload(Request $request)
	{
        //PDF file is stored under project/public/download/info.pdf
        $id=$request->input("id");
        $version=DB::select("SELECT dosyaIsmi FROM robotversiyon WHERE id=? ",array($id));
        
        $file="./download/".$version[0]->dosyaIsmi;
        return response()->download(public_path($file));
	}
	public function versiyonKontrol(Request $request)
    {
        $operator=$request->input("operator");
        $op=DB::select("SELECT id FROM operator WHERE adi=? LIMIT 1",array($operator));
    	$max=DB::select("SELECT * 
                                    FROM 
                                        robotversiyon 
                                    WHERE 
                                        versiyon=(SELECT max(versiyon) FROM robotversiyon WHERE operatorId=? ) 
                                    AND 
                                        operatorId=?  ",array($op[0]->id,$op[0]->id));
        //echo $operator;


        $arr2       = array();
        foreach($max as $m)
        {
            $result=array(
                "id"=>$m->id,
                "version"=>$m->versiyon,
                "tarih"=>$m->updated_at,
                "dosya_adi"=>$m->dosyaIsmi,
                "link"=>$m->link."?id=".$m->id

            );
            array_push($arr2,$result);
        }
        $arr3=array("Results"=>$arr2);
        $finish =  json_encode($arr3,JSON_UNESCAPED_UNICODE);
        $finish = str_replace("\/","/",$finish);
        return $finish;

    }

    
}
