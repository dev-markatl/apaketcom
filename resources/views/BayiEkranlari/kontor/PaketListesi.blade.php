<?php 
use App\Classes\DropDown;

$_SESSION["menu"]=2;
$_SESSION["altmenu"]=2;
$dd=new DropDown ;


?>
@extends('BayiMasterPage')
@section('content')

<form class="form-horizontal" method="post" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="bayi-kontor-paketlistesi" >
{{csrf_field()}}
    <div class="form-group col-md-12 filter" align="left"   >
        <input type="hidden" name="session" value="false" >
        <label class="lbl " >Operator:</label>
        <?php $dd->Make($dd->DdOperator(),"operator","operator","BayiPaketListesi");?>
        <label class="lbl ">Tip:</label>
        <?php $dd->Make($dd->DdTip(),"tip","tip","BayiPaketListesi");?>
       
        <input type="submit" 
        class="btn btn-info btnSorgula" 
        id="sorgula" name="sorgula" value="Listele">
        <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">

    </div>
    
</form>


<table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tablePaket" >
    <tbody>
        <tr style="color:white; background-color:#0b3779">
            <td style="width:70px;" align="center">Operator</td>
            <td style="width:70px;" align="center">Tip</td>
            <td style="width:50px;" align="center">Kod</td>
            <td style="width:365px" align="center">Paket Adı</td>
            <td style="width:40px;" align="center">Gün</td>
            <td style="width:60px;" align="center">HerYöne <br>Konuşma</td>
            <td style="width:60px;" align="center">Şebeke içi<br>Konuşma</td>
            <td style="width:60px;" align="center">HerYöne<br>Sms</td>
            <td style="width:60px" align="center">Şebeke içi<br>Sms</td>
            <td style="width:60px" align="center">İnternet</td>
            <td style="width:90px;" align="center">R.Satış<br>Maliyet Fiyati</td>
           
        </tr>
    
        <?php
            foreach($paketler as $paket)
            {
                
                
                $resmiSatisFiyati=number_format( $paket->resmiSatisFiyati, 3, '.', '');
                $maliyetFiyati=number_format( $paket->maliyetFiyati, 3, '.', '');
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='11'> </td>
                </tr>
                
                <tr style='height:20px;' >
                    <td  align='center'>$paket->operatorAdi</td>
                    <td align='center'>$paket->tipAdi</td>
                    <td  align='center'>$paket->kod</td>
                    <td  align='center' >
                       $paket->adi 
                    </td>
                    
                    <td  align='center'>$paket->gun</td>
                    <td  align='center'>$paket->hyk</td>
                    <td  align='center'>$paket->sik</td>
                    <td  align='center'>$paket->hys</td>
                    <td  align='center'>$paket->sis</td>
                    <td  align='center'>$paket->internet</td>
                    <td  align='center'>
                        $resmiSatisFiyati<br>$maliyetFiyati  
                    </td>
                    
                </tr>";
            }
        ?>
    </tbody>
</table>

   
<script>
function Temizle()
{
    window.location.href = "bayi-kontor-paketlistesi/temizle"; 
}
</script>

@endsection