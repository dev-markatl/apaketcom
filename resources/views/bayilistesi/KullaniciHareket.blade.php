<?php
use App\Classes\DropDown;
use App\Classes\CommonFunctions;

$_SESSION["menu"]=7;
$_SESSION["altmenu"]=4;
$dd=new DropDown;

//$toplamBakiye=number_format( $toplamBakiye, 3, '.', '');

?>
@extends('MasterPage')
@section('content')



<script src="{{ URL::asset('public/js/Bayiler.js') }}"></script>
<div >
    <div class="form-group col-md-12"  >

    <input type="hidden" value="false" name="session">
    <form class="form-horizontal" align="center" name="hareketlistesi" style=" display:inline ;clear:both; " id="hareketlistesi" action="bayinohareket-kullanicihareket" >
        <div class="form-group col-md-12 filter"  align="left" >


            <div class="form-group col-md-12 filter">
                <input type="hidden" value="false" name="session">
                <label class="lbl-sm " >Tarih:</label>

                <?php $dd->MakeInput("tarih1","tarih1","KullaniciHareket");?>
                <?php $dd->MakeInput("tarih2","tarih2","KullaniciHareket");?>
                <!--<input type="text" class="form-control hasDatepicker dtPicker" name="tarih1" id="tarih1" value="" size="10">
                <input type="text" class="form-control hasDatepicker dtPicker" name="tarih2" id="tarih2" value="" size="10">-->
             
            
           
            <label class="lbl-sm " >Bayi:</label>
            <?php $dd->Make($dd->DdBayiler(),"bayiler","bayiler","KullaniciHareket");?>  


            </div>


            <div class="form-group col-md-8 filter" align="left"  >

            <label style="width:auto!important;" class="lbl-lg " >Bayi Site Adres:</label>
            <?php $dd->MakeInput("siteadres","siteadres","KullaniciHareket");?>

                <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
                <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">



            </div>
         


        </div>
       



    </form>



    </div>




    <table style="font-size:14px!important;" align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableHareketListesi" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:200px;" align="center">Kullanıcı Adı</td>
                <td style="width:200px;" align="center">Site Adresi</td>
                <td style="width:130px;" align="center">Turkcell</td>
                <td style="width:130px;" align="center">Turkcell<br>ExIptal</td>
                <td style="width:130px;" align="center">Vodafone</td>
                <td style="width:130px;" align="center">Vodafone<br>ExIptal</td>
                <td style="width:130px;" align="center">Türk Telekom</td>
                <td style="width:130px;" align="center">Türk Telekom<br>ExIptal</td>
            </tr>
        </tbody>

        <!-- DINAMIK -->
        @foreach($hrkT as $hrk)
        <tr>
        <td align="center">
            {{$hrk->ad}}
        </td>
        <td align="center">
            {{$hrk->site_adres}}
        </td>
        <td>
          <center>
          {{$hrk->turkcell}}
        </center>
        </td>
        <td>
          <center>
          {{$hrk->turkcellExIptal}}
        </center>
        </td>
        <td>
          <center>
          {{$hrk->vodafone}}
        </center>
        </td>
        <td>
          <center>
          {{$hrk->vodafoneExIptal}}
        </center>
        </td>
        <td>
          <center>
          {{$hrk->turktelekom}}
        </center>
        </td>
        <td>
          <center>
          {{$hrk->turktelekomExIptal}}
        </center>
        </td>
      </tr>

        @endforeach


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
                href='bayinohareket-kullanicihareket?sayfa=$i&$filtreler'
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


function Temizle()
{
    //var id=document.getElementById("bayiId").value;
    window.location.href = "ajax/KullaniciHareket/temizle";
}


</script>
@endsection
