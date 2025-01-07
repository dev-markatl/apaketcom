<?php 
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;


class SessionManager
{
    public $Operator;
    public $Tip;
    public $Durum;
    public $Bayiler;
    public $Robotlar;
    public $Tarih1;
    public $Tarih2;
    public $PageName;
    public $Tel;
    public $IslemTuru;
    public $Kurum;
    public $SiteAdres;

    private function Sessioner()
    {
        if (session_status() == PHP_SESSION_NONE) 
		{
            session_start();
        }
        
    }
    
    public function GetDataOTD()
    {
        $this->Sessioner();
        if(isset($_SESSION[$this->PageName.'_Operator']) && isset($_SESSION[$this->PageName.'_Tip']) && isset($_SESSION[$this->PageName.'_Durum']))
        {
            $this->Operator=$_SESSION[$this->PageName.'_Operator'];
            $this->Tip=$_SESSION[$this->PageName.'_Tip'];
            $this->Durum=$_SESSION[$this->PageName.'_Durum'];
            return true;
        }
        return false;
    }
    public function GetAllData()
    {
        $this->Sessioner();
        $this->GetDataB();
        $this->GetDataD();
        $this->GetDataO();
        $this->GetDataR();
        $this->GetDataT();
        $this->GetDataT1();
        $this->GetDataT2();
        $this->GetDataTel();
        $this->GetDataIslemTuru();
        $this->GetDataKurum();
        $this->GetDataSiteAdres();
        
    }
    public function ClearAllData()
    {
        $this->Operator=-1;
        $this->Bayiler=-1;
        $this->Tip=-1;
        $this->Durum=-1;
        $this->Robotlar=-1;
        $this->Tarih1=null;
        $this->Tarih2=null;
        $this->Tel=null;
        $this->IslemTuru=-1;
        $this->Kurum=-1;
        $this->SiteAdres=null;
        $this->SetDataB();
        $this->SetDataD();
        $this->SetDataO();
        $this->SetDataR();
        $this->SetDataT();
        $this->SetDataT1();
        $this->SetDataT2();
        $this->SetDataTel();
        $this->SetDataIslemTuru();
        $this->SetDataKurum();
        $this->SetDataSiteAdres();

    }
    public function SetAllData()
    {
        $this->SetDataB();
        $this->SetDataD();
        $this->SetDataO();
        $this->SetDataR();
        $this->SetDataT();
        $this->SetDataT1();
        $this->SetDataT2();
        $this->SetDataTel();
        $this->SetDataIslemTuru();
        $this->SetDataKurum();
        $this->SetDataSiteAdres();
    }
    public function GetDataO()
    {
        $this->Sessioner();
        if(isset($_SESSION[$this->PageName.'_Operator']))
        {
            $this->Operator=$_SESSION[$this->PageName.'_Operator'];
            return true;
        }
        return false;
    }
    public function GetDataKurum()
    {
        $this->Sessioner();
        if(isset($_SESSION[$this->PageName.'_Kurum']))
        {
            $this->Kurum=$_SESSION[$this->PageName.'_Kurum'];
            return true;
        }
        return false;
    }
    public function GetDataTel()
    {
        $this->Sessioner();
        if(isset($_SESSION[$this->PageName.'_Tel']))
        {
            $this->Tel=$_SESSION[$this->PageName.'_Tel'];
            return true;
        }
        return false;
    }

    public function GetDataSiteAdres()
    {
        $this->Sessioner();
        if(isset($_SESSION[$this->PageName.'_SiteAdres']))
        {
            $this->SiteAdres=$_SESSION[$this->PageName.'_SiteAdres'];
            return true;
        }
        return false;
    }


    public function GetDataT()
    {
        $this->Sessioner();
 
        if(isset($_SESSION[$this->PageName.'_Tip']))
        {
            $this->Tip=$_SESSION[$this->PageName.'_Tip'];
            return true;
        }
        return false;
    }
    public function GetDataT1()
    {
        $this->Sessioner();
 
        if(isset($_SESSION[$this->PageName.'_Tarih1']))
        {
            $this->Tarih1=$_SESSION[$this->PageName.'_Tarih1'];
            return true;
        }
        return false;
    }
    public function GetDataT2()
    {
        $this->Sessioner();
 
        if(isset($_SESSION[$this->PageName.'_Tarih2']))
        {
            $this->Tarih2=$_SESSION[$this->PageName.'_Tarih2'];
            return true;
        }
        return false;
    }
    public function GetDataB()
    {
        $this->Sessioner();
 
        if(isset($_SESSION[$this->PageName.'_Bayiler']))
        {
            $this->Bayiler=$_SESSION[$this->PageName.'_Bayiler'];
            return true;
        }
        return false;
    }
    public function GetDataIslemTuru()
    {
        $this->Sessioner();
 
        if(isset($_SESSION[$this->PageName.'_IslemTuru']))
        {
            $this->IslemTuru=$_SESSION[$this->PageName.'_IslemTuru'];
            return true;
        }
        return false;
    }
    public function GetDataR()
    {
        $this->Sessioner();
 
        if(isset($_SESSION[$this->PageName.'_Robotlar']))
        {
            $this->Robotlar=$_SESSION[$this->PageName.'_Robotlar'];
            return true;
        }
        return false;
    }
    public function GetDataD()
    {
        $this->Sessioner();

        if(isset($_SESSION[$this->PageName.'_Durum']))
        {
            $this->Durum=$_SESSION[$this->PageName.'_Durum'];
            return true;
        }
        return false;
    }
    
    public function SetDataOTD()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Operator']=$this->Operator;
        $_SESSION[$this->PageName.'_Tip']=$this->Tip;
        $_SESSION[$this->PageName.'_Durum']= $this->Durum;
    }
    public function SetDataO()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Operator']=$this->Operator;
    }
    public function SetDataKurum()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Kurum']=$this->Kurum;
    }
    public function SetDataT()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Tip']=$this->Tip;
    }
    public function SetDataD()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Durum']= $this->Durum;
    }
    public function SetDataT1()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Tarih1']= $this->Tarih1;
    }
    public function SetDataIslemTuru()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_IslemTuru']= $this->IslemTuru;
    }
    public function SetDataT2()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Tarih2']= $this->Tarih2;
    }
    public function SetDataB()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Bayiler']= $this->Bayiler;
    }
    public function SetDataR()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Robotlar']= $this->Robotlar;
    }
    public function SetDataTel()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_Tel']= $this->Tel;
    }

    public function SetDataSiteAdres()
    {
        $this->Sessioner();
        $_SESSION[$this->PageName.'_SiteAdres']= $this->SiteAdres;
    }
}
?>