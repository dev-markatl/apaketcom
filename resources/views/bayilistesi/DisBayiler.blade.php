<?php
use App\Classes\DropDown;
use App\Classes\CommonFunctions;

$_SESSION["menu"]=7;
$_SESSION["altmenu"]=1;
$dd=new DropDown;

//$toplamBakiye=number_format( $toplamBakiye, 3, '.', '');

?>
@extends('MasterPage')
@section('content')
<script src="{{ URL::asset('public/js/Bayiler.js') }}"></script>
<div >
    <div class="form-group col-md-12"  >

    <input type="hidden" value="false" name="session">
    <form class="form-horizontal" align="center" name="disbayiler" style=" display:inline ;clear:both; " id="disbayiler" action="bayinohareket-bayiler" >
        <div class="form-group col-md-12 filter"  align="left" >
            <div class="col-md-8" style="margin-left:-14px;">
                <input type="hidden" value="false" name="session">




            </div>



        </div>
            <div class="form-group col-md-12 filter" align="left"  >
            <label class="lbl-sm " >Bayi:</label>
            <?php $dd->Make($dd->DdBayiler(),"bayiler","bayiler","DisBayiler");?>

            <label class="lbl-xlg " >Bayi No:</label>
            <?php $dd->MakeInput("tel","tel","DisBayiler");?>
            </div>

            <div class="form-group col-md-8 filter" align="left"  >
            <label style="width:auto!important;" class="lbl-lg " >Bayi Site Adres:</label>
            <?php $dd->MakeInput("siteadres","siteadres","DisBayiler");?>

            <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">

            <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
            </div>


            

    </form>



    </div>




    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableBayiListesi" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:150px;" align="center">Kullanıcı<br>Adı</td>
                <td style="width:250px;" align="center">Site<br>Adresi</td>
                <td style="width:50px;" align="center">Bayi<br>No</td>
                <td style="width:150px;" align="center">Bayi<br>Kategori</td>
                <td style="width:200px;" align="center">Bayi<br>Adı</td>
                <td style="width:150px;" align="center">Bayi<br>Sorgu Blokaj</td>
                <td style="width:150px;" align="center">Bayi<br>Yükleme Blokaj</td>
                </tr>
        </tbody>

        <!-- DINAMIK -->
        @foreach($bayiler as $bayi)
        <tr>
        <td align="center">
            {{$bayi->ad}}
        </td>
        <td align="center">
            {{$bayi->site_adres}}
        </td>
        <td align="center">
            {{$bayi->bayi_id}}
        </td>
        <td align="center">
          <input style="text-align:left !important" class='form-control tableInput' onChange='UpdateBayiKategori({{$bayi->id}})' type='text'
           id='bayikategori_{{$bayi->id}}' style='width:50px' name='{{$bayi->kategori}}' value='{{$bayi->kategori}}'>
        </td>
        <td align='center'>
            <input style="text-align:left !important" class='form-control tableInput' onChange='UpdateBayiBilgi({{$bayi->id}})' type='text'
             id='bayibilgi_{{$bayi->id}}' style='width:50px' name='{{$bayi->id}}' value='{{$bayi->bayi_ad}}'>
        </td>
        <td align='center'>
          <div class='onoffswitch' id='swy_{{$bayi->id}}'>
              <input type='checkbox' value='{{$bayi->id}}' class='onoffswitch-checkbox'
                  onClick='UpdateBayiBlokaj("blk_{{$bayi->id}}",{{$bayi->id}})' id='blk_{{$bayi->id}}' {{$bayi->sorgu_blokaj}}>
              <label class='onoffswitch-label' for='blk_{{$bayi->id}}'>
                  <span class='onoffswitch-inner'></span>
                  <span class='onoffswitch-switch'></span>
              </label>
          </div>
        </td>
        <td align='center'>
          <div class='onoffswitch' id='swyb_{{$bayi->id}}'>
              <input type='checkbox' value='{{$bayi->id}}' class='onoffswitch-checkbox'
                  onClick='UpdateBayiBlokajYukleme("blkb_{{$bayi->id}}",{{$bayi->id}})' id='blkb_{{$bayi->id}}' {{$bayi->yukleme_blokaj}}>
              <label class='onoffswitch-label' for='blkb_{{$bayi->id}}'>
                  <span class='onoffswitch-inner'></span>
                  <span class='onoffswitch-switch'></span>
              </label>
          </div>
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
                href='bayinohareket-bayiler?sayfa=$i'
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
    window.location.href = "ajax/DisBayiler/temizle";
}


</script>
@endsection
