<?php 
use App\Classes\DropDown;
use App\Classes\CommonFunctions;

$_SESSION["menu"]=2;
$_SESSION["altmenu"]=1;
$dd=new DropDown ;
$cf=new CommonFunctions;

?>
@extends('BayiMasterPage')
@section('content')
<div style='margin-left:70px;' >
    <form class="form-horizontal" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="bayi-kontor-yuklemetakip" >
        <div class="form-group col-md-12 filter"  align="left" >
            <input type="hidden" value="false" name="session">
            <label class="lbl-sm " >Tarih:</label>
            <?php $dd->MakeInput("tarih1","tarih1","YuklemeTakip");?>
            - 
            <?php $dd->MakeInput("tarih2","tarih2","YuklemeTakip");?>

            <label class="lbl-lg " >Operator:</label>
            <?php $dd->Make($dd->DdOperator(),"operator","operator","YuklemeTakip");?>
            <label class="lbl-xsm ">Tip:</label>
            <?php $dd->Make($dd->DdTip(),"tip","tip","YuklemeTakip");?>
            <label class="lbl-sm " >Durum:</label>
            <select class="form-control ddDurum" name="durum" id="durum"  ref="durum"   >
                <option value="-1">Tümü</option>
                <option value="0">Beklemede</option>
                <option value="2">Onaylı</option>
                <option value="3">Red</option>
                <option value="4">Hatali</option>
            </select>   
            <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
            
            <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
                        
        </div>

        <div class="form-group col-md-12 filter" align="left"  >
            <label class="lbl-xlg " >Telefon No:</label>
            <?php $dd->MakeInput("tel","tel","YuklemeTakip");?>
            <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
        </div>
    </form>
   
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableBayiYuklemeTakip" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:70px;" align="center">İşlem <br> Numarası</td>
                <td style="width:70px" align="center">Operatör</td> 
                <td style="width:70px" align="center">Tip</td>
                <td style="width:80px" align="center">Telefon <br> No</td>
                <td style="width:260px" align="center">Gönderilen Paket</td>
                <td style="width:60px" align="center">Alış Tutarı</td>
                <td style="width:180px" align="center">Geliş Tarihi<br>Sonuç Tarihi</td>
                <td style="width:90px" align="center">Sonuç Süresi</td>
                </tr>
        </tbody>
        <?php
            foreach($takipler as $takip)
            {
                $renk="";
                switch ($takip->durum) 
                {
                    case '0'://robot henuz almadı
                        $renk="#EAFFA0";
                        break;
                    case '1'://robot aldı
                        $renk="#EAFFA0";
                        break;
                    case '2'://olumlu cevap
                        $renk="#ACF2AF";
                        break;
                    case '3'://olumsuz cevap
                        $renk="#F5BCA9";
                        break;  
                        
                    case '4'://Sorunlu cevap
                        $renk="#b69fff";
                        break;     
                    case '5'://Kesin olumsuz cevap
                        $renk="#F5BCA9";
                        break; 
                }
                $sistemSonucSuresi=$cf->zamanHesapla($takip->created_at,$takip->donmeZamani);
                if($takip->donmeZamani==null)
                    $takip->donmeZamani="<br>";
                $tutar=number_format( $takip->tutar, 3, '.', '');
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='8'> </td>
                </tr>
                
                <tr id='tr_$takip->id' name='aa' style='background-color:$renk'  >
                    <td  align='center'>$takip->id</td>
                    <td  align='center'  onClick='satirAc($takip->id)' >$takip->operatorAdi  </td>
                    <td align='center'>$takip->tipAdi</td>
                    <td  align='center'>$takip->tel</td>
                    <td  align='center'>($takip->paketKodu) $takip->paketAdi</td>
                    <td  align='center'>$tutar</td>
                    <td  align='center'>$takip->created_at<br> $takip->donmeZamani </td>
                    <td  align='center'>$sistemSonucSuresi </td>
                    
                </tr>
                <tr onClick='satirKapa($takip->id)' >
                    <td style='text-align:left; color:red;' colspan='8'>
                        <div id='hd_$takip->id' style='background-color:$renk;  display:none;' >   
                            $takip->cevap<br><br>";
                            $cevapPaketleri=$cf->cevapPaketleri($takip->id);
                            foreach($cevapPaketleri as $cevap)
                            {
                                echo "($cevap->kod) $cevap->adi <br> ";
                            }
                echo   "</div>
                    </td>
                </tr>";
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
                href='bayi-kontor-yuklemetakip?sayfa=$i&$filtreler'
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
 
 
function satirAc(id)
{
    console.log("11");
    var satir=window.$("#hd_"+id);
    satir.show(600);
}
function satirKapa(id) 
{
    var satir=window.$("#hd_"+id);
    satir.hide(600);
}

  
function Temizle()
{
    window.location.href = "bayi-kontor-yuklemetakip/temizle"; 
}

</script>
@endsection
