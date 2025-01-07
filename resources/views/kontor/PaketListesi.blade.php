<?php 
use App\Classes\DropDown;

$_SESSION["menu"]=2;
$_SESSION["altmenu"]=2;
$dd=new DropDown ;


?>
@extends('MasterPage')
@section('content')
<script src="{{ URL::asset('public/js/PaketListesi.js') }}"></script>

<form class="form-horizontal" method="post" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="kontor-paketlistesi" >
{{csrf_field()}}
    <div class="form-group col-md-12 filter" align="left"   >
        <input type="hidden" name="session" value="false" >
        <label class="lbl " >Operator:</label>
        <?php $dd->Make($dd->DdOperator(),"operator","operator","PaketListesi");?>
        <label class="lbl ">Tip:</label>
        <?php $dd->Make($dd->DdTip(),"tip","tip","PaketListesi");?>
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
        <input type="button"  onClick="exiptal()"
        class="btn btn-info btnYeni "  style="margin-left:230px;"
        id="sorgula" name="sorgula" 
        value="Ex iptal Sıfırla"> 
        <input type="button"  onClick="paketDuzenle(0)"
        class="btn btn-info btnYeni "   style="margin-left:20px;"
        id="sorgula" name="sorgula" 
        value="Yeni Paket Ekle">      
    </div>
    
</form>

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
        
    <button type="button" class="btn btn-primary btnSorgula" id="kaydet" name="kaydet" onClick="PaketDurumKaydet()" style="margin-left: 15px;">Kaydet</button>
</div>

<table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tablePaket" >
    <tbody>
        <tr style="color:white; background-color:#0b3779">
            <td style="width:40px;" align="center">Sec</td>
            <td style="width:70px;" align="center">Operator</td>
            <td style="width:70px;" align="center">Tip</td>
            <td style="width:50px;" align="center">Kod</td>
            <td style="width:300px" align="center">Paket Adı</td>
            <td style="width:70px;" align="center">Her Yöne<br>Konuşma</td>
            <td style="width:70px;" align="center">İnternet</td>
            <td style="width:70px;" align="center">Gun</td>
            <td style="width:140px" align="center">Son Görülme</td>
            <td style="width:60px" align="center">Sıra<br> No</td>
            <td style="width:100px" align="center">Kategori<br> Adi</td>
            <td style="width:60px" align="center">Kategori<br> No</td>
            <td style="width:70px;" align="center">R.Satış<br>Fiyatı</td>
            <td style="width:70px;" align="center">Maliyet<br>Fiyatı</td>
            <td style="width:100px;" align="center">Sorguya<br>Eklensin</td>
            <td style="width:100px;" align="center">Paket<br>Durumu</td>
        </tr>
    
        <?php
            foreach($paketler as $paket)
            {
                if($paket->sorguyaEkle==0)
                    $paket->sorguyaEkle="";
                else
                    $paket->sorguyaEkle="checked";

                if($paket->aktif==0)
                    $paket->aktif="";
                else
                    $paket->aktif="checked";
                
                $resmiSatisFiyati=number_format( $paket->resmiSatisFiyati, 3, '.', '');
                $maliyetFiyati=number_format( $paket->maliyetFiyati, 3, '.', '');
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='12'> </td>
                </tr>
                
                <tr >
                    <td style='width:40px;' align='center'>
                        <input type='checkbox' onClick='CbClicked($paket->id)' id='cb_$paket->id' class='seciniz tableCb' name='cbx' value='$paket->id' >
                    </td>
                    <td  align='center'>$paket->operatorAdi</td>
                    <td align='center'>$paket->tipAdi</td>
                    <td  align='center'>$paket->kod</td>
                    <td  align='center'
                    onClick='paketDuzenle($paket->id)'   
                    data-target='#kul_adi_sifre' 
                    data-toggle='modal' >
                        $paket->adi
                    </td>
                    <td  align='center'>$paket->herYoneKonusma</td>
                    <td  align='center'>$paket->internet</td>
                    <td  align='center'>$paket->gun</td>
                    <td  align='center'>$paket->sonGorulme</td>
                    <td  align='center'>$paket->siraNo</td>
                    <td  align='center'>$paket->kategoriAdi</td>
                    <td  align='center'>$paket->kategoriNo</td>
                    <td  align='center'>
                        <input type='number'   id='fiyat0_$paket->id' class='form-control tableInput' onChange='UpdateResmiFiyat($paket->id)'   value='$resmiSatisFiyati' name='$paket->id'>
                    </td>
                    <td align='center'>
                        <input type='number'  id='fiyat1_$paket->id'  class='form-control tableInput'  onChange='UpdateMaliyetFiyat($paket->id)'  value='$maliyetFiyati' name='$paket->id'>
                    </td>
                    <td align='center'>
                        <div class='onoffswitch' id='dv_$paket->id'>
                            <input type='checkbox' value='$paket->id' class='onoffswitch-checkbox' onClick='UpdateSorgu($paket->id)' id='sw_$paket->id' $paket->sorguyaEkle >
                            <label class='onoffswitch-label' for= 'sw_$paket->id'>
                                <span class='onoffswitch-inner'></span>
                                <span class='onoffswitch-switch'></span>
                            </label>
                        </div>
                    </td>
                    <td align='center'>
                        <div class='onoffswitch' id='dvi_$paket->id'>
                            <input type='checkbox' value='$paket->id' class='onoffswitch-checkbox' onClick='UpdateAktif($paket->id)' id='swi_$paket->id'  $paket->aktif>
                            <label class='onoffswitch-label' for='swi_$paket->id'>
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