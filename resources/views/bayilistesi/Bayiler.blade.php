<?php 
use App\Classes\DropDown;

$_SESSION["menu"]=4;
$_SESSION["altmenu"]=1;
$dd=new DropDown ;
$toplamBakiye=number_format( $toplamBakiye, 3, '.', '');

?>
@extends('MasterPage')
@section('content')
<script src="{{ URL::asset('public/js/Bayiler.js') }}"></script>
<div >
    <div class="form-group col-md-12"  >
    <form action="bayilistesi-bayiler" method="get">
    <input type="hidden" value="false" name="session">
            <input type="button"  onClick="ModalClick('yeni',0)"
            style="margin-left:1000px;"
            class="btn btn-info btnYeni"  
            id="sorgula" name="sorgula"  
            value="Yeni Bayi Ekle">
            <div class="form-group col-md-8 filter" align="left"  >
                <label class="lbl-sm " >K. Adı:</label>
                <?php $dd->MakeInput("tel","tel","Bayiler");?>
                <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
                <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
            </div>
            <div align="right" class="col-md-4" style='margin-left:-60px; margin-top:30px;'>
                <?php echo "<b  >Toplam Bakiye:  $toplamBakiye</b>"; ?>      
            </div>
        </form>
    </div>
    
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableBayiListesi" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">

                <td style="width:200px;" align="center">Firma Adı</td>
                <td style="width:120px;" align="center">Kullanici Adi</td>
                <td style="width:120px;" align="center">Bakiye</td>
                <td style="width:90px" align="center">Para <br> Ekle</td>
                <td style="width:90px" align="center">Para <br> Çıkar</td>
                <td style="width:70px;" align="center">Hesap<br>Haraketleri</td>
                <td style="width:80px;" align="center">Sorgu<br>Fiyatı</td>
                <td style="width:80px;" align="center">Bayi<br>Sorgu</td>
                <td style="width:80px;" align="center">Bayi<br>Yükleme</td>   
                <td style="width:80px;" align="center">Bayi<br>Fatura</td>  
                <td style="width:80px;" align="center">Bayi<br>Durum</td>   
                
                </tr>
        </tbody>
        <?php
            $class="";
            foreach ($bayiler as $bayi) 
            {
                if($bayi->aktif)
                    $class="";
                else
                    $class="hataBG";

                if($bayi->yetkiSorgu==0)
                    $bayi->yetkiSorgu="";
                else
                    $bayi->yetkiSorgu="checked";

                if($bayi->aktif==0)
                    $bayi->aktif="";
                else
                    $bayi->aktif="checked";
                
                if($bayi->yetkiYukle==0)
                    $bayi->yetkiYukle="";
                else
                    $bayi->yetkiYukle="checked";

                if($bayi->yetkiFatura==0)
                    $bayi->yetkiFatura="";
                else
                    $bayi->yetkiFatura="checked";
                
                $sorguUcret=number_format( $bayi->sorguUcret, 3, '.', '');
                $bakiye=number_format( $bayi->bakiye, 3, '.', '');
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='11'> </td>
                </tr>
                
                <tr id='tr_$bayi->id' class='$class'>
                    <td  align='center' onClick='ModalClick(\"guncelle\",$bayi->id)' >$bayi->firmaAdi</td>
                    <td  align='center'  >$bayi->takmaAd</td>
                    <td align='center'>$bakiye</td>
                
                    <td  align='center'>
                        <button 
                        class='btn modalButton' 
                        type='button' 
                        onClick='ModalClick(\"ekle\",$bayi->id)'
                        name='$bayi->id' id='$bayi->id' >
                            <img style='width:18px; margin-top:0px; margin-left:0px;' src='public/img/hesapharaketleri.png'>
                        </button>
                    </td>
                    <td  align='center'>
                        <button 
                        class='btn modalButton' 
                        type='button' 
                        onClick='ModalClick(\"cikar\",$bayi->id)'
                        name='$bayi->id' id='$bayi->id' >
                            <img style='width:18px; margin-top:0px; margin-left:0px;' src='public/img/hesapharaketleri.png'>
                        </button>
                    </td>
                    <td  align='center'>
                        <button 
                        class='btn modalButton' 
                        type='button' 
                        onClick='ModalClick(\"hesap\",$bayi->id)'
                        name='$bayi->id' id='$bayi->id' >
                            <img style='width:18px; margin-top:0px; margin-left:0px;' src='public/img/hesapharaketleri.png'>
                        </button>
                    </td>
                    <td align='center'>
                        <input class='form-control tableInput' onChange='UpdateFiyat($bayi->id)' type='number' 
                        maxlength='5' id='fiyat_$bayi->id' style='width:50px' name='$bayi->id' value='$sorguUcret'>
                    </td>
                    <td  align='center'>
                        <div class='onoffswitch' id='dvs_$bayi->id'>
                            <input type='checkbox' value='$bayi->id' class='onoffswitch-checkbox' 
                            onClick='UpdateSw(\"sws_$bayi->id\",\"sorgu\",$bayi->id)' id='sws_$bayi->id'  $bayi->yetkiSorgu>
                            <label class='onoffswitch-label' for='sws_$bayi->id'>
                                <span class='onoffswitch-inner'></span>
                                <span class='onoffswitch-switch'></span>
                            </label>
                        </div>
                    </td>
                    <td  align='center'>
                        <div class='onoffswitch' id='dvy_$bayi->id'>
                            <input type='checkbox' value='$bayi->id' class='onoffswitch-checkbox'
                                onClick='UpdateSw(\"swy_$bayi->id\",\"yukle\",$bayi->id)' id='swy_$bayi->id' $bayi->yetkiYukle >
                            <label class='onoffswitch-label' for='swy_$bayi->id'>
                                <span class='onoffswitch-inner'></span>
                                <span class='onoffswitch-switch'></span>
                            </label>
                        </div>
                    </td>
                    <td  align='center'>
                        <div class='onoffswitch' id='dvf_$bayi->id'>
                            <input type='checkbox' value='$bayi->id' class='onoffswitch-checkbox'
                                onClick='UpdateSw(\"swf_$bayi->id\",\"fatura\",$bayi->id)' id='swf_$bayi->id' $bayi->yetkiFatura >
                            <label class='onoffswitch-label' for='swf_$bayi->id'>
                                <span class='onoffswitch-inner'></span>
                                <span class='onoffswitch-switch'></span>
                            </label>
                        </div>
                    </td>
                    <td  align='center'>
                        <div class='onoffswitch' id='dva_$bayi->id'>
                            <input type='checkbox' value='$bayi->id' class='onoffswitch-checkbox' 
                            onClick='UpdateSw(\"swa_$bayi->id\",\"aktif\",$bayi->id)' id='swa_$bayi->id' $bayi->aktif >
                            <label class='onoffswitch-label' for='swa_$bayi->id'>
                                <span class='onoffswitch-inner'></span>
                                <span class='onoffswitch-switch'></span>
                            </label>
                        </div>
                    </td>
                </tr>
            ";
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
                href='bayilistesi-bayiler?sayfa=$i&$filtreler'
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
@endsection


