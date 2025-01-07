<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script> window.Laravel={csrfToken:' {{csrf_token()}}'}</script>
    <link href="{{ URL::asset('public/css/bootstrap.min.css') }}"media="screen" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/font.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/general.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/new.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/style.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/toastr.min.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/bootstrapdate.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/switch.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/alertify.css') }}" rel='stylesheet'>
    <script src="{{ URL::asset('public/js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/moment.js') }}"></script>
    <script src="{{ URL::asset('public/js/toastr.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
    <script src="{{ URL::asset('public/js/bootstrapdate.js') }}"></script>
    <script src="{{ URL::asset('public/js/tarih.js') }}"></script>
    <script src="{{ URL::asset('public/js/alertify.js') }}"></script>
    <script>
      $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
      });
    </script>

    <title>Robot Ekle</title>
</head>
<?php 
use App\Classes\DropDown;
use App\Classes\CommonFunctions;
$dd=new DropDown ;
$cf=new CommonFunctions;
?>
<div style="position:relative">
    <h4 align="center" class="modalTitle" ref="robotAdi">Kullanici: {{$kullanici->takmaAd}}</h4>
    <form action="BayiHesapHareketleri">
    <input type="hidden" value="{{$kullanici->id}}" id="bayiId" name="id">
    <input type="hidden" value="false" name="session">
    <label class="lbl-sm " >Tarih:</label>
    <?php $dd->MakeInput("tarih1","tarih1","BayiHesap".$kullanici->id);?>
    - 
    <?php $dd->MakeInput("tarih2","tarih2","BayiHesap".$kullanici->id);?>
    <label class="lbl-sm " >İslem:</label>
    <?php $dd->Make($dd->DdIslemTuru(),"islemTuru","islemTuru","BayiHesap".$kullanici->id);?>
    <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
    <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
    </form>
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableYeniPaketler" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:70px;" align="center">İşlem Numarası</td>
                <td style="width:110px;" align="center">İşlem Türü</td>
                <td style="width:260px;" align="center">Paket/Banka</td>
                <td style="width:70px" align="center">Tutar</td>
                <td style="width:70px" align="center">Ö.Bakiye<br>S.Bakiye</td>
                <td style="width:115px;" align="center">İşlem Tarihi</td>
                <td style="width:180px;" align="center">Açıklama</td>
                </tr>
        </tbody>
        <?php
            $sayfaTutarToplami=0;
            foreach ($hareketler as $hareket) 
            {

                $aciklama=$hareket->aciklama;
                $paket=$hareket->paket;

                $tutar=(double)$hareket->sonrakiBakiye-$hareket->oncekiBakiye;
                $sayfaTutarToplami=$sayfaTutarToplami+$tutar;
                $tutar=number_format( $tutar, 3, '.', '');
                echo "
                    <tr  style='border-left:hidden; border-right:hidden;'>
                        <td style='padding:3px;' colspan='7'> </td>
                    </tr>
                    
                    <tr>
                        
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
    <div class="col-md-10" align="center"><b>Sayfa Tutar Toplamı:<?php  echo $sayfaTutarToplami; ?> TL</b></div>
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
                href='BayiHesapHareketleri?sayfa=$i&$filtreler'
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

    }

    $( ".hasDatePicker" ).datetimepicker(options);

});
function Temizle()
{
    var id=document.getElementById("bayiId").value;
    window.location.href = "BayiHesapHareketleri/temizle?id="+id; 
}
</script>

