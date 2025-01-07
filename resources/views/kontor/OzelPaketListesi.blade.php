<?php
use App\Classes\DropDown;

$_SESSION["menu"]=2;
$_SESSION["altmenu"]=3;
$dd=new DropDown ;


?>
@extends('MasterPage')
@section('content')
<script src="{{ URL::asset('public/js/OzelFiyatListesi.js') }}"></script>

<form class="form-horizontal" method="post" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="kontor-grupduzenle?i={{$grupdetay[0]->id}}" >
{{csrf_field()}}
    <div class="form-group col-md-12 filter" align="left"   >
        <input type="hidden" name="session" value="false" >
        <label class="lbl " >Operator:</label>
        <?php $dd->Make($dd->DdOperator(),"operator","operator","OzelPaketListesi");?>
        <label class="lbl ">Tip:</label>
        <?php $dd->Make($dd->DdTip(),"tip","tip","OzelPaketListesi");?>
        <label class="lbl " >Durum:</label>
        <select  class="form-control ddDurum" name="durum" id="durum"  ref="durum"   >
            <option value="1">Aktif</option> 
            <option value="-1">Tümü</option>
            <option value="0">Pasif</option>
            
              
        </select>   
        <input type="submit" 
        class="btn btn-info btnSorgula" 
        onClick="GetPackets('btn')"  
        id="sorgula" name="sorgula" value="Listele">
        <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
     
        <input type="hidden" name="grupno" id="grupno" value="{{$grupdetay[0]->id}}">
        <input style="width:60px;margin-left:300px; display:inline;" type='number' class='form-control'  name='carpan' id='carpan' value='0'>
        <input type="button"  onClick="OtoFiyatDuzenle()"
        class="btn btn-success"   style="display: inline; margin-left:0px;"
        id="fiyatduzenle" name="fiyatduzenle"
        value="Oto. Fiyat Düzenle">
     
     
    </div>
    
</form>

<div class="form-group col-md-12 filter" align="left"   >
      <b>{{$grupdetay[0]->grup_ad}}</b>


</div>
<div class="form-group col-md-8 filter" align="left"   >



</div>

<div  id="divKaydet" name="divKaydet" style=" clear:both; display:none; margin-left:30px; margin-bottom:-40px; margin-top: 15px;">
    <div class="checkbox" style=" display:inline ;">
    <label style="font-weight:600;"><input type="checkbox" id="cb_tum" onClick="SelectAll()" value="">Tümünü Seç</label>
    </div>
    <label for="durumKaydet" style="margin-left: 15px;">Durumu </label>
    <select class="form-control ddDurum" name="durumKaydet" id="durumKaydet"   >
        <option value="0">Pasif</option>
        <option value="1">Aktif</option>
        <option value="2">Sil</option>
    </select>

    <button type="button" class="btn btn-primary btnSorgula" id="kaydet" name="kaydet" onClick="OzelFiyatDurumKaydet()" style="margin-left: 15px;">Kaydet</button>
</div>

<table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tablePaket" >
    <tbody>
        <tr style="color:white; background-color:#0b3779">
            <td style="width:40px;" align="center">Sec</td>
            <td style="width:70px;" align="center">Operator</td>
            <td style="width:70px;" align="center">Tip</td>
            <td style="width:50px;" align="center">Kod</td>
            <td style="width:300px" align="center">Paket Adı</td>
            <td style="width:140px" align="center">Son Görülme</td>
            <td style="width:100px" align="center">Kategori<br> Adi</td>
            <td style="width:60px" align="center">Kategori<br> No</td>
            <td style="width:70px;" align="center">R.Satış<br>Fiyatı</td>
            <td style="width:70px;" align="center">Maliyet<br>Fiyatı</td>     
            <td style="width:150px;" align="center">Paket<br>Durumu</td>
        </tr>

        <?php
            foreach($paketler as $paket)
            {
                if($paket->sorguya_ekle==0)
                    $paket->sorguya_ekle="";
                else
                    $paket->sorguya_ekle="checked";

                if($paket->ozelAktif==0)
                    $paket->ozelAktif="";
                else
                    $paket->ozelAktif="checked";

                $resmiSatisFiyati=number_format( $paket->resmi_fiyat, 3, '.', '');
                $maliyetFiyati=number_format( $paket->maliyet_fiyat, 3, '.', '');
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='12'> </td>
                </tr>

                <tr >
                    <td style='width:40px;' align='center'>
                        <input type='checkbox' onClick='CbClicked($paket->ozelPaketNo)' id='cb_$paket->ozelPaketNo' class='seciniz tableCb' name='cbx' value='$paket->ozelPaketNo' >
                    </td>
                    <td  align='center'>$paket->operatorAdi</td>
                    <td align='center'>$paket->tipAdi</td>
                    <td  align='center'>$paket->kod</td>
                    <td  align='center'
                    onClick='paketDuzenle($paket->ozelPaketNo)'
                    data-target='#kul_adi_sifre'
                    data-toggle='modal' >
                        $paket->adi
                    </td>
                    <td  align='center'>$paket->sonGorulme</td>
                    <td  align='center'>$paket->kategoriAdi</td>
                    <td  align='center'>$paket->kategoriNo</td>
                    <td  align='center'>
                        <input type='number'   id='fiyat0_$paket->ozelPaketNo' class='form-control tableInput' onChange='UpdateOzelResmiFiyat($paket->ozelPaketNo)'   value='$resmiSatisFiyati' name='$paket->ozelPaketNo' disabled>
                    </td>
                    <td align='center'>
                        <input type='number'  id='fiyat1_$paket->ozelPaketNo'  class='form-control tableInput'  onChange='UpdateOzelMaliyetFiyat($paket->ozelPaketNo)'  value='$maliyetFiyati' name='$paket->ozelPaketNo'>
                    </td>
               
                    <td align='center'>
                        <div class='onoffswitch' id='dvi_$paket->ozelPaketNo'>
                            <input type='checkbox' value='$paket->ozelPaketNo' class='onoffswitch-checkbox' onClick='UpdateOzelAktif($paket->ozelPaketNo)' id='swi_$paket->ozelPaketNo'  $paket->ozelAktif>
                            <label class='onoffswitch-label' for='swi_$paket->ozelPaketNo'>
                                <span class='onoffswitch-inner'></span>
                                <span class='onoffswitch-switch'></span>
                            </label>
                        </div>
                    </td>
                </tr>";
            }
        ?>
    </tbody>
</table>




@endsection
