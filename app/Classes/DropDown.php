<?php
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Classes\SessionManager;
use App\Models\Operator ;
use App\Models\Tip;
use App\Models\Kullanici;
use App\Models\Rol;
use App\Models\RobotTip;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Robot ;
use App\Models\Islemturu ;
use App\Models\Kurum ;

class DropDown
{
    public function Make($veriler,$adi,$tip,$sayfaAdi,$selectedId=null,$extra=null,$extraStyle=null,$seciniz="Seçiniz")
    {
       $selected=-1;
       if($sayfaAdi!=null)
       {
        $manager    = new SessionManager;
        $manager->PageName=$sayfaAdi;
        switch ($tip) {
            case 'operator':
                $manager->GetDataO();
                $selected=$manager->Operator;
            break;
            case 'tip':
                $manager->GetDataT();
                $selected=$manager->Tip;
            break;
            case 'durum':
                $manager->GetDataD();
                $selected=$manager->Durum;
            break;
            case 'bayiler':
                $manager->GetDataB();
                $selected=$manager->Bayiler;
            break;
            case 'robotlar':
                $manager->GetDataR();
                $selected=$manager->Robotlar;
            break;
            case 'islemTuru':
                $manager->GetDataIslemTuru();
                $selected=$manager->IslemTuru;
            break;
            case 'kurum':
                $manager->GetDataKurum();
                $selected=$manager->Kurum;
            break;
      

        }

       }
       else
       {
           if($selectedId!=null)
               $selected=$selectedId;

       }
       if($selected!=-1 && $selected!=false && $selectedId==null)
        $renk="red";
       else
        $renk="";

       echo "
       <select $extra style=' $extraStyle border-color:$renk; ' class='form-control ddOperator' name='$adi' id='$adi'>";
       if($seciniz=="Seçiniz")
       {
           echo "<option value='-1'>Seçiniz</option>";

       }
       else
       {
           if($seciniz!=null)
            echo "<option value='-1'>$seciniz</option>";
       }

        foreach($veriler as $veri)
        {
            echo "<option ";
            if($selected!=-1 && $selected==$veri->id)
                echo "selected";
            else
                echo "";
            echo" value='$veri->id' >$veri->adi  </option>";

        }
        echo " </select>";

    }
    public function MakeInput($adi,$tip,$sayfaAdi=null,$veri=null,$extra=null)
    {
        $manager=new SessionManager;

        if($sayfaAdi!=null)
        {
            $manager->PageName=$sayfaAdi;
            $manager->GetDataT1();
            $manager->GetDataT2();
            $manager->GetDataTel();
            $manager->GetDataSiteAdres();

        }
        $class="";
        switch ($tip) {
            case 'tarih1':
                if($sayfaAdi!=null)
                    $manager->GetDataT1();
                if($manager->Tarih1!=null)
                {
                    $class="filtreli";
                    $veri=$manager->Tarih1;
                }

               echo " <input type='text' class=' $class form-control hasDatepicker dtPicker'  name='$adi' id='$adi'  value='$veri' size='10' $extra >";
            break;
            case 'tarih2':
                if($sayfaAdi!=null)
                    $manager->GetDataT2();
                if($manager->Tarih2!=null)
                {
                    $class="filtreli";
                    $veri=$manager->Tarih2;
                }
               echo " <input type='text'  class=' $class form-control hasDatepicker dtPicker'  name='$adi' id='$adi'  value='$veri' size='10' $extra >";
            break;
            case 'tel':
                if($sayfaAdi!=null)
                    $manager->GetDataTel();
                if($manager->Tel!=null)
                {
                    $class="filtreli";
                    $veri=$manager->Tel;
                }
               echo " <input type='tel' class=' $class form-control tel '  name='$adi' id='$adi'  value='$veri' $extra >";
            break;
            case 'siteadres':
                if($sayfaAdi!=null)
                    $manager->GetDataSiteAdres();
                if($manager->SiteAdres!=null)
                {
                    $class="filtreli";
                    $veri=$manager->SiteAdres;
                }
                echo " <input type='tel' class=' $class form-control tel '  name='$adi' id='$adi'  value='$veri' $extra >";
                break;
            

            default:
                # code...
                break;
        }
    }
    public function BtnDurum($adi,$degeri,$sayfaAdi,$text,$extra=null,$extraStyle=null)
    {
        $deger=99;
        if($sayfaAdi!=null)
        {
            $manager    = new SessionManager;
            $manager->PageName=$sayfaAdi;

            $manager->GetDataD();
            $deger=$manager->Durum;
        }

        if($deger==$degeri)
         $renk="red";
        else
         $renk="";
        echo "
        <button
        class='btn'
        type='submit'
        $extra
        style='border-color:$renk; $extraStyle'
        value='$degeri'
        name='$adi' id='$adi'  >
        $text
        </button>";

    }
    public function DdBayiler( )
    {

        $sorgu= "SELECT k.firmaAdi as adi,k.ad,k.id FROM kullanici k";
        $bayiler = DB::select($sorgu);

        return $bayiler;
    }
    public function DdAktifBayiler( )
    {

        $sorgu= "SELECT k.firmaAdi as adi,k.ad,k.id FROM kullanici k WHERE aktif=1 AND rolId=2";
        $bayiler = DB::select($sorgu);

        return $bayiler;
    }

    public function DdRobotTuru( )
    {
        $robotTip=RobotTip::all();

        return $robotTip;
    }
    public function DdKurum()
    {
        $kurum=Kurum::all();

        return $kurum;
    }
    public function DdRobotlar( )
    {
        $robot=Robot::all();

        return $robot;
    }

    public function DdOperator( )
    {
        $operator=Operator::all();

        return $operator;
    }
    public function DdTip( )
    {
        $tip=Tip::all();

        return $tip;
    }
    public function DdIl()
    {
        $il=Il::all();
        return $il;
    }
    public function DdIlce()
    {
        $ilce=Ilce::all();
        return $ilce;
    }
    public function DdIslemTuru()
    {
        $islemturu=Islemturu::all();
        return $islemturu;
    }

}

?>
