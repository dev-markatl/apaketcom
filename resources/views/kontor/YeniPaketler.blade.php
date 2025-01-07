<?php
use App\Classes\DropDown;

$_SESSION["menu"]=2;
$_SESSION["altmenu"]=4;
$dd=new DropDown ;


?>
@extends('MasterPage')
@section('content')
<script src="{{ URL::asset('public/js/YeniPaketler.js') }}"></script>
<div>
    <div ref="divKaydet" id="divKaydet" name="divKaydet" style="display:none; margin-left:30px; margin-bottom:-40px; margin-top: 15px;">
            <div class="checkbox" style=" display:inline ;">
            <label style="font-weight:600;"><input type="checkbox" id="cb_tum" onClick="SelectAll()" value="">Tümünü Seç</label>
            </div>
            <label for="durum_" style="margin-left: 15px;">Durumu </label>
            <select class="form-control ddDurum" name="durumKaydet" id="durumKaydet"  ref="durumKaydet"   >
                <option value="1">Sil</option>
            </select>

            <button type="button" class="btn btn-primary btnSorgula" id="kaydet" name="kaydet" onClick="PaketDurumKaydet()" style="margin-left: 15px;">Kaydet</button>
        </div>
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableYeniPaketler" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:40px;" align="center">
                    Sec
                </td>
                <td style="width:80px;" align="center">Operator</td>
                <td style="width:80px;" align="center">Tip</td>
                <td style="width:190px" align="center">Tarih</td>
                <td style="width:365px" align="center">Robot/Telefon</td>
                <td style="width:300px;" align="center">Paket<br> Adı</td>
                <td style="width:90px;" align="center">Fiyat</td>
                </tr>
        </tbody>
        <?php
            foreach($paketler as $paket)
            {
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='10'> </td>
                </tr>
                <tr >
                    <td style='width:40px;' align='center'>
                        <input type='checkbox' onClick='CbClicked($paket->id)' id='cb_$paket->id' class='seciniz tableCb' name='cbx' value='$paket->id' >
                    </td>
                    <td  align='center'>$paket->operatorAdi</td>
                    <td align='center'>$paket->tipAdi</td>
                    <td  align='center'>$paket->tarih</td>
                    <td  align='center'>$paket->sonDegisiklikYapan</td>
                    <td  align='center' >
                        $paket->adi
                    </td>
                    <td  align='center' > $paket->resmiSatisFiyati </td>
                </tr>";
            }
        ?>




    </table>
</div>


@endsection
