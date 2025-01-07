<?php 
use App\Classes\DropDown;

$_SESSION["menu"]=3;
$_SESSION["altmenu"]=1;
$dd=new DropDown ;

?>
@extends('BayiMasterPage')
@section('content')
<script src="{{ URL::asset('js/Bayiler.js') }}"></script>
<div >
<form class="form-horizontal" method="post" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="bayi-odemehareketleri-hesaphareketleri" >
{{csrf_field()}}
    <div class="form-group col-md-12 filter" align="left"   >
        <input type="hidden" name="session" value="false" >
       
        <label class="lbl-sm " >Tarih:</label>
        <?php $dd->MakeInput("tarih1","tarih1","BayiHesapHareketleri");?>
        - 
        <?php $dd->MakeInput("tarih2","tarih2","BayiHesapHareketleri");?>
       
        <label class="lbl-lg " >İşlem Türü:</label>
        <?php $dd->Make($dd->DdIslemTuru(),"islemTuru","islemTuru","BayiHesapHareketleri");?>

        <input type="submit" 
        class="btn btn-info btnSorgula" 
        onClick="GetPackets('btn')"  
        id="sorgula" name="sorgula" value="Listele">
        <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
       
        <input type="button"  onClick="bankaGoster()"
        class="btn btn-info btnYeni "   style="margin-left:450px;"
        id="sorgula" name="sorgula" 
        value="Bankalar">      
    </div>
    
</form>

    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableBayiOdeme" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:90px;" align="center">İşlem Numarası</td>
                <td style="width:130px;" align="center">İşlem Türü</td>
                <td style="width:300px;" align="center">Paket/Banka</td>
                <td style="width:90px" align="center">Tutar</td>
                <td style="width:90px" align="center">Ö.Bakiye<br>S.Bakiye</td>
                <td style="width:130px;" align="center">İşlem Tarihi</td>
                <td style="width:180px;" align="center">Açıklama</td>
                </tr>
        </tbody>
        <?php
            foreach ($hareketler as $hareket) 
            {

                $aciklama=$hareket->aciklama;
                $paket=$hareket->paket;
                $renk="";
                switch ($hareket->islemTuruId) 
                {
                    case '1':
                        break;
                    case '2':
                        $renk="#F5BCA9";
                        break;
                    case '3':
                        break;
                    case '4':
                        $renk="#F5BCA9";
                        break;
                    default:
                        break;
                }
                $tutar=(double)$hareket->sonrakiBakiye-$hareket->oncekiBakiye;
                $tutar=number_format( $tutar, 3, '.', '');
                echo "
                    <tr  style='border-left:hidden; border-right:hidden;'>
                        <td style='padding:3px;' colspan='7'> </td>
                    </tr>
                    <tr style='background-color:$renk'>
                        <td  align='center'>$hareket->id</td>
                        <td  align='center'>$hareket->adi</td>
                        <td  align='center'>$paket</td>
                        <td  align='center'>$tutar</td>
                        <td  align='center'>$hareket->oncekiBakiye<br>$hareket->sonrakiBakiye </td>
                        <td  align='center'>$hareket->tarih</td>
                        <td  align='center'>$aciklama</td>
                        
                    </tr>  ";
            }
            
        ?>
            
        
    </table>
    <div class="col-md-10" style="margin-left:20px;  margin-top:10px;">
    <?php
        $class="";
        for ($i=1; $i <=$sayfaSayisi ; $i++) 
        { 
            if($suankiSayfa==$i)
                $class="selectedPage";
            else
                $class="btn-info";
            echo "
            <a 
                href='?sayfa=$i&id=$id'
                style='color:white; margin-right:3px;' 
                id='btn_$suankiSayfa' 
                name='$suankiSayfa' 
                class='btn  $class'>
                $i
            </a>  ";
        }
        
    ?>
        
    </div>
</div>
<script>
 
 $( document ).ready(function() 
 {
     var options={
     format: 'YYYY-MM-DD',
     locale:'tr'
     }
 
     $( ".hasDatePicker" ).datetimepicker(options);
 });
  
function bankaGoster() 
{
    window.open("bayi-odemehareketleri-banka", "bankaGoster", "height=400,width=1100");
}

 
   
function Temizle()
{
    window.location.href = "bayi-odemehareketleri-hesaphareketleri/temizle"; 
}
 
 </script>
@endsection


