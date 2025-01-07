<?php
use App\Classes\DropDown;
use App\Classes\CommonFunctions;

$_SESSION["menu"]=2;
$_SESSION["altmenu"]=5;
$dd=new DropDown ;
$cf=new CommonFunctions;

?>
@extends('MasterPage')
@section('content')
<div >

    <form class="form-horizontal" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="kontor-kazanctakip" >
        <div class="form-group col-md-12 filter"  align="left" >
            <div class="col-md-8" style="margin-left:-14px;">
                <input type="hidden" value="false" name="session">
                <label class="lbl-sm " >Tarih:</label>
                <?php $dd->MakeInput("tarih1","tarih1","KazancTakip");?>
                -
                <?php $dd->MakeInput("tarih2","tarih2","KazancTakip");?>

                <label class="lbl-lg " >Operator:</label>
                <?php $dd->Make($dd->DdOperator(),"operator","operator","KazancTakip");?>
                <label class="lbl-xsm ">Tip:</label>
                <?php $dd->Make($dd->DdTip(),"tip","tip","KazancTakip");?>


                <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">

                <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
            </div>


        </div>
        <div class="form-group col-md-12 filter" align="left"  >
            <label class="lbl-sm " >Bayi:</label>
            <?php $dd->Make($dd->DdBayiler(),"bayiler","bayiler","YuklemeTakip");?>

            <label class="lbl-lg "  >Robot Adi:</label>
            <?php $dd->Make($ddRobotlar,"robotlar","robotlar","YuklemeTakip");?>
        </div>
        <div class="form-group col-md-8 filter" align="left"  >
            <label class="lbl-xlg " >Telefon No:</label>
            <?php $dd->MakeInput("tel","tel","YuklemeTakip");?>
            <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">

        </div>

        <div class="form-group col-md-2 " style="margin-left: 180px;" align="left" >
                <b>Resmi Toplam..: {{$resmiToplam}} ₺</b>
                <br>
                <b>Maliyet Toplam: {{$maliyetToplam}} ₺</b>
                <br>
                <b style="color:green;">Kâr Toplam.......: {{$resmiToplam-$maliyetToplam}} ₺</b>

        </div>



    </form>


    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableRobotHesap" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">

                <td style="width:70px;" align="center">İşlem <br> Numarası</td>
                <td style="width:120px;" align="center">Bayi Adı</td>
                <td style="width:70px" align="center">Operatör <br> Tip</td>
                <td style="width:80px" align="center">Telefon <br> No</td>
                <td style="width:260px" align="center">Gelen Paket</td>
                <td style="width:60px" align="center">Ana Fiyat Tutarı</td>
                <td style="width:60px" align="center">Maliyet Tutarı</td>
                <td style="width:60px" align="center">Kar<br>Tutarı</td>
                <td style="width:140px" align="center">Geliş Tarihi<br>Sonuç Tarihi</td>
                <td style="width:105px" align="center">Robot Adı</td>
                <td style="width:90px" align="center">Robot Süresi</td>
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
                $robotAdi=$takip->robotAdi;
                if($takip->exIptal==1 && $takip->durum==3 && $robotAdi=="bos" )
                    $robotAdi="exIptal";
                $robotSonucSuresi=$cf->zamanHesapla($takip->almaZamani,$takip->donmeZamani);
                $sistemSonucSuresi=$cf->zamanHesapla($takip->created_at,$takip->donmeZamani);
                $tutar=number_format( $takip->maliyetTutar, 3, '.', '');
                $rsTutar=number_format( $takip->resmiTutar, 3, '.', '');
                $karTutar=number_format( $takip->resmiTutar-$takip->maliyetTutar, 3, '.', '');
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='12'> </td>
                </tr>

                <tr id='tr_$takip->id' name='aa' style='background-color:$renk'  >

                    <td  align='center'>$takip->id</td>
                    <td align='center'>$takip->firmaAdi</td>
                    <td  align='center'  onClick='satirAc($takip->id)' >$takip->operatorAdi<br>$takip->tipAdi</td>
                    <td  align='center'>$takip->tel</td>
                    <td  align='center'>($takip->paketKodu) $takip->paketAdi</td>
                    <td  align='center'>$rsTutar</td>
                    <td  align='center'>$tutar</td>                
                    <td  align='center'>$karTutar</td>
                    <td  align='center'>$takip->created_at<br> $takip->donmeZamani </td>
                    <td  align='center'>$robotAdi</td>
                    <td  align='center'>$robotSonucSuresi </td>
                    <td  align='center'>$sistemSonucSuresi </td>

                </tr>
                <tr onClick='satirKapa($takip->id)' >
                    <td style='text-align:left; color:red;' colspan='11'>
                        <div id='hd_$takip->id' style='background-color:$renk;  display:none;' >
                            Aciklama:$takip->aciklama <br>
                            Robotun Cevabı:($takip->cevap)<br>Paketler:<br>";
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
                href='kontor-kazanctakip?sayfa=$i&$filtreler'
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
<?php  $cf->TarihSiniriAsildiMi($tarihHata);?>
<script>

$( document ).ready(function()
{
    var todayDate = new Date().getDate();
    var options={
    format: 'YYYY-MM-DD',
    locale:'tr',
    // minDate: new Date(new Date().setDate(todayDate - 90)),
    // maxDate: new Date(new Date().setDate(todayDate + 90))
    }
    var options2={
    format: 'YYYY-MM-DD',
    locale:'tr',

    }

    $( "#tarih1" ).datetimepicker(options);
    $( "#tarih2" ).datetimepicker(options2);

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
    window.location.href = "kontor-kazanctakip/temizle";
}

</script>
@endsection
