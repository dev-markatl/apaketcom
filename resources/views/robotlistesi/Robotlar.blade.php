<?php

use App\Classes\DropDown;



$_SESSION["menu"]=3;

$_SESSION["altmenu"]=1;

$dd=new DropDown ;





?>

@extends('MasterPage')

@section('content')

<script src="{{ URL::asset('public/js/Robotlar.js') }}"></script>

<div >

<form class="form-horizontal" method="post" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="robotlistesi-robotlar" >

{{csrf_field()}}

    <div class="form-group col-md-12 filter" align="left"   >

        <input type="hidden" name="session" value="false" >

        <label class="lbl " >Operator:</label>

        <?php $dd->Make($dd->DdOperator(),"operator","operator","RobotListesi");?>



        <label class="lbl " >Durum:</label>

        <select  class="form-control ddDurum" name="durum" id="durum"  ref="durum"   >

            <option value="1">Aktif</option>

            <option value="-1">Tümü</option>

            <option value="0">Pasif</option>





        </select>

        <input type="submit"
        class="btn btn-info btnSorgula"

        id="sorgula" name="sorgula" value="Listele">
        <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
        <input type="button"  onClick="ModalClick('yeni')"
            style=" margin-left:630px !important;"
            class="btn btn-info btnYeni"
            id="sorgula" name="sorgula"
            value="Yeni Robot Ekle">

    </div>

</form>

        <div class="form-group col-md-12"   >


            <?php echo "<b style='margin-left:10px;' >Toplam PosBakiye:$robotBakiye->posToplam </b>"; ?>
            <?php echo "<b style='margin-left:40px;' >Toplam S.Bakiye:$robotBakiye->sistemToplam </b>"; ?>

             <div style="margin-left:1050px;" class="form-check">
            <input type="checkbox" class="form-check-input" onClick='UpdateOlumsuzSorguTekrar()' id='sws_olumsuzTekrar' {{$olumsuzCheck}}>
            <label class="form-check-label" for="exampleCheck1">Olumsuz Sorgu Tekrar</label>
            </div>

            <div style="margin-left:1050px;" class="form-check">
            <input type="checkbox" class="form-check-input"  onClick='UpdateSistemiKapat("1")' id='sws_sistemiKapatGNC' {{$sistemiKapatGNCCheck}}>
            <label class="form-check-label" for="exampleCheck1">Otomatik İptal Sorgu GNC</label>
            </div>
            
            <div style="margin-left:1050px;" class="form-check">
            <input type="checkbox" class="form-check-input"  onClick='UpdateSistemiKapatYukleme("0")' id='sws_sistemiKapatYukleme' {{$sistemiKapatYuklemeCheck}}>
            <label class="form-check-label" for="exampleCheck1">Otomatik İptal Yükleme</label>
            </div>

            <div style="margin-left:1050px;" class="form-check">
            <input type="checkbox" class="form-check-input"  onClick='UpdateSistemiKapatYukleme("1")' id='sws_sistemiKapatYuklemeGNC' {{$sistemiKapatYuklemeGNCCheck}}>
            <label class="form-check-label" for="exampleCheck1">Otomatik İptal Yükleme GNC</label>
            </div>

            <div style="margin-left:1050px;" class="form-check">
            <input type="checkbox" class="form-check-input"  onClick='UpdateSureliIptalYukleme()' id='sws_sureliIptalYukleme' {{$sureliIptalYuklemeCheck}}>
            <label class="form-check-label" for="exampleCheck1">Süreli İptal Yükleme</label>
            </div>

        </div>




    <div ref="divKaydet" id="divKaydet" name="divKaydet" style=" clear:both; display:none; margin-left:30px; margin-bottom:-40px; margin-top: 15px;">

        <div class="checkbox" style=" display:inline ;">

        <label style="font-weight:600;"><input id="cb_tum" type="checkbox" onClick="SelectAll" value="">Tümünü Seç</label>

        </div>

        <label for="durum_" style="margin-left: 15px;">Durumu </label>

        <select class="form-control ddDurum" name="durumKaydet" id="durumKaydet"  ref="durumKaydet"   >

            <option value="0">Pasif</option>

            <option value="1">Aktif</option>





        </select>



        <button type="button" class="btn btn-primary btnSorgula" id="kaydet" name="kaydet" onClick="RobotIslem()" style="margin-left: 15px;">Kaydet</button>

    </div>

    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableRobotHesap" >

        <tbody>

            <tr style="color:white; background-color:#0b3779">

                <td style="width:30px;" align="center">

                    Sec

                </td>

                <td style="width:135px;" align="center">Robot Adı</td>

                <td style="width:90px;" align="center">Son Görülme</td>

                <td style="width:120px;" align="center">Bayi 1</td>

                <td style="width:120px;" align="center">Fiyat Grubu</td>

                <td style="width:60px" align="center">Süre <br>Sınırı</td>

                <td style="width:80px" align="center">Sistem <br> Bakiyesi</td>

                <td style="width:80px;" align="center">Pos<br> Bakiyesi</td>

                <td style="width:90px;" align="center">Fark</td>

                <td style="width:60px;" align="center">Para<br> Ekle</td>

                <td style="width:60px;" align="center">Para<br>Çıkar</td>

                <td style="width:65px;" align="center">Hesap<br>Hareketleri</td>

                <td style="width:80px;" align="center">Robot<br>Sorgu</td>

                <td style="width:80px;" align="center">Robot<br>Yükleme</td>

                <td style="width:80px;" align="center">Robot<br>Durum</td>

                </tr>

        </tbody>

    <?php

        foreach($robotlar as $robot)

        {

            if($robot->sorgu==0)

                $robot->sorgu="";

            else

                $robot->sorgu="checked";



            if($robot->yukle==0)

                $robot->yukle="";

            else

                $robot->yukle="checked";



            if($robot->aktif==0)

                $robot->aktif="";

            else

                $robot->aktif="checked";



            if($robot->fatura==0)

                $robot->fatura="";

            else

                $robot->fatura="checked";





            if($robot->yukleyici==0)

                $robot->yukleyici="";

            else

                $robot->yukleyici="checked";



            $renk="";

            if($robot->aktif!="checked")

                $renk="hataBg";







            $sistemBakiye=number_format( $robot->sistemBakiye, 3, '.', '');

            $posBakiye=number_format( $robot->posBakiye, 3, '.', '');

            $fark=$sistemBakiye-$posBakiye;

            $fark=number_format( $fark, 3, '.', '');

            echo "

            <tr  style='border-left:hidden; border-right:hidden;'>

                <td style='padding:3px;' colspan='15'> </td>

            </tr>";



            if ($robot->yukleyici == "checked" && $robot->aktif == "checked")

                echo "<tr style='background-color:#d7ffcc;' id='tr_$robot->id' class='$renk'>";

            else

                echo "<tr id='tr_$robot->id' class='$renk'>";



            echo "

                <td style='width:40px;' align='center'>

                    <input type='checkbox' onClick='CbClicked($robot->id)' class='seciniz tableCb' id='cb_$robot->id'  name='cbx' value='$robot->id' >

                </td>

                <td  align='center' onClick='ModalClick(\"guncelle\",$robot->id)' >$robot->robotAdi</td>
                <td style='color:$robot->tabloRenk;' align='center'><b>$robot->sonGorulme</b></td>

                <td align='center'>$robot->kulAdi</td>

                <td align='center'>$robot->fiyatgrubuId</td>

                <td  align='center'>

                <input type='number'  id='suresinir_$robot->id'  class='form-control tableInput'  onChange='UpdateSureSinir($robot->id)'  value='$robot->sure_siniri' name='$robot->id'>

                </td>

                <td  align='center'>$sistemBakiye</td>

                <td  align='center'>$posBakiye</td>

                <td  align='center'>$fark</td>

                <td  align='center'>

                    <button

                    class='btn modalButton'

                    type='button'

                    data-toggle='modal'

                    data-target='#modaler'

                    onClick='ModalClick(\"ekle\",$robot->id)'

                    name='$robot->id' id='$robot->id' >

                        <img style='width:18px; margin-top:0px; margin-left:0px;' src='public/img/hesapharaketleri.png'>

                    </button>

                </td>

                <td  align='center'>

                    <button

                    class='btn modalButton'

                    type='button'

                    data-toggle='modal'

                    data-target='#modaler'

                    onClick='ModalClick(\"cikar\",$robot->id)'

                    name='$robot->id' id='$robot->id' >

                        <img style='width:18px; margin-top:0px; margin-left:0px;' src='public/img/hesapharaketleri.png'>

                    </button>

                </td>

                <td  align='center'>

                    <button

                    class='btn modalButton'

                    type='button'

                    data-toggle='modal'

                    data-target='#modaler'

                    onClick='ModalClick(\"hesap\",$robot->id)'

                    name='$robot->id' id='$robot->id' >

                        <img style='width:18px; margin-top:0px; margin-left:0px;' src='public/img/hesapharaketleri.png'>

                    </button>

                </td>

                <td  align='center'>

                    <div class='onoffswitch' id='dvs_$robot->id'>

                        <input type='checkbox' value='$robot->id' class='onoffswitch-checkbox'

                        onClick='UpdateRobotSw(\"sws_$robot->id\",\"sorgu\",$robot->id)' id='sws_$robot->id' $robot->sorgu>

                        <label class='onoffswitch-label' for='sws_$robot->id'>

                            <span class='onoffswitch-inner'></span>

                            <span class='onoffswitch-switch'></span>

                        </label>

                    </div>

                </td>

                <td  align='center'>

                    <div class='onoffswitch' id='dvy_$robot->id'>

                        <input type='checkbox' value='$robot->id' class='onoffswitch-checkbox'

                            onClick='UpdateRobotSw(\"swy_$robot->id\",\"yukle\",$robot->id)' id='swy_$robot->id' $robot->yukle >

                        <label class='onoffswitch-label' for='swy_$robot->id'>

                            <span class='onoffswitch-inner'></span>

                            <span class='onoffswitch-switch'></span>

                        </label>

                    </div>

                </td>





      

                <td  align='center'>

                    <div class='onoffswitch' id='dva_$robot->id'>

                        <input type='checkbox' value='$robot->id' class='onoffswitch-checkbox'

                        onClick='UpdateRobotSw(\"swa_$robot->id\",\"aktif\",$robot->id)' id='swa_$robot->id' $robot->aktif >

                        <label class='onoffswitch-label' for='swa_$robot->id'>

                            <span class='onoffswitch-inner'></span>

                            <span class='onoffswitch-switch'></span>

                        </label>

                    </div>

                </td>





            </tr>";

        }



    ?>

    </table>





</div>



@endsection

