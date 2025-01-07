<?php
use App\Classes\DropDown;
use App\Classes\CommonFunctions;

$_SESSION["menu"]=7;
$_SESSION["altmenu"]=3;
$dd=new DropDown;

//$toplamBakiye=number_format( $toplamBakiye, 3, '.', '');

?>
@extends('MasterPage')
@section('content')
<script src="{{ URL::asset('public/js/Bayiler.js') }}"></script>
<div >
    <div class="form-group col-md-12"  >

    <input type="hidden" value="false" name="session">
    <form class="form-horizontal" align="center" name="hareketlistesi" style=" display:inline ;clear:both; " id="hareketlistesi" action="bayinohareket-bayihareket" >
        <div class="form-group col-md-12 filter"  align="left" >


            <div class="form-group col-md-12 filter">
                <input type="hidden" value="false" name="session">
                <label class="lbl-sm " >Tarih:</label>

                <?php $dd->MakeInput("tarih1","tarih1","BayiNoHareket");?>
                <?php $dd->MakeInput("tarih2","tarih2","BayiNoHareket");?>
                <!--<input type="text" class="form-control hasDatepicker dtPicker" name="tarih1" id="tarih1" value="" size="10">
                <input type="text" class="form-control hasDatepicker dtPicker" name="tarih2" id="tarih2" value="" size="10">-->
             
            
           
            <label class="lbl-sm " >Bayi:</label>
            <?php $dd->Make($dd->DdBayiler(),"bayiler","bayiler","BayiNoHareket");?>  


            <label class="lbl-xlg " >Bayi No:</label>
            <?php $dd->MakeInput("tel","tel","BayiNoHareket");?>
            </div>



            </div>


            <div class="form-group col-md-8 filter" align="left"  >
            <label style="width:auto!important;" class="lbl-lg " >Bayi Site Adres:</label>
            <?php $dd->MakeInput("siteadres","siteadres","BayiNoHareket");?>


                <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
                <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">



            </div>
         


        </div>
       



    </form>



    </div>




    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableBayiListesi" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:150px;" align="center">Kullanıcı<br>Adı</td>
                <td style="width:250px;" align="center">Site<br>Adresi</td>
                <td style="width:50px;" align="center">Bayi<br>No</td>
                <td style="width:150px;" align="center">Bayi<br>Adı</td>
                <td style="width:80px;" align="center">Turkcell</td>
                <td style="width:80px;" align="center">Turkcell ExIptal</td>
                <td style="width:80px;" align="center">Vodafone</td>
                <td style="width:80px;" align="center">Vodafone ExIptal</td>
                <td style="width:80px;" align="center">Türk Telekom</td>
                <td style="width:80px;" align="center">Türk Telekom ExIptal</td>



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
        <td align="center">
          {{$hrk->bayi_id}}
        </td>
        <td align='center'>
            <input style="text-align:left !important" class='form-control tableInput' onChange='UpdateBayiBilgi({{$hrk->id}})' type='text'
             id='bayibilgi_{{$hrk->id}}' style='width:50px' name='{{$hrk->id}}' value='{{$hrk->bayi_ad}}'>
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
                href='bayinohareket-bayihareket?sayfa=$i&$filtreler'
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
    window.location.href = "ajax/BayiNoHareket/temizle";
}


</script>
@endsection
