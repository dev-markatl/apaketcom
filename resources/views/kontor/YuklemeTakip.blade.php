<?php 
use App\Classes\DropDown;
use App\Classes\CommonFunctions;

$_SESSION["menu"]=2;
$_SESSION["altmenu"]=1;
$dd=new DropDown ;
$cf=new CommonFunctions;

?>
@extends('MasterPage')
@section('content')
<div >

    <form class="form-horizontal" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="kontor-yuklemetakip" >
        <div class="form-group col-md-12 filter"  align="left" >
            <div class="col-md-8" style="margin-left:-14px;">
                <input type="hidden" value="false" name="session">
                <label class="lbl-sm " >Tarih:</label>
                <?php $dd->MakeInput("tarih1","tarih1","YuklemeTakip");?>
                - 
                <?php $dd->MakeInput("tarih2","tarih2","YuklemeTakip");?>

                <label class="lbl-lg " >Operator:</label>
                <?php $dd->Make($dd->DdOperator(),"operator","operator","YuklemeTakip");?>
                <label class="lbl-xsm ">Tip:</label>
                <?php $dd->Make($dd->DdTip(),"tip","tip","YuklemeTakip");?>

                
                <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
                
                <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
            </div>
            
            <div class="col-md-4 btnDurumFiltreDiv" align="right" >
                
                <?php  $dd->BtnDurum("durum",0,"YuklemeTakip","B",null,"background-color:#EAFFA0");?>
                <?php  $dd->BtnDurum("durum",2,"YuklemeTakip","O",null,"background-color:#ACF2AF");?>
                <?php  $dd->BtnDurum("durum",3,"YuklemeTakip","I",null,"background-color:#F5BCA9");?>
                <?php  $dd->BtnDurum("durum",4,"YuklemeTakip","S",null,"background-color:#b69fff");?>
                <?php  $dd->BtnDurum("durum",-1,"YuklemeTakip","T",null,"background-color:lightblue");?>
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

        <div class="form-group col-md-4 " align="right" >
                <label class="lbl-lg " >Toplam:</label>
                <?php echo "<b>".$satisToplam[0]->toplamTutar."</b>"  ?>
        </div>
    </form>
    <div ref="divKaydet" id="divKaydet" align="left" name="divKaydet" style="clear:both; display:none; margin-left:30px; margin-bottom:-40px; margin-top: 15px;">
        <div class="checkbox" style=" display:inline ;">
        <label style="font-weight:600;"><input type="checkbox" id="cb_tum" onClick="SelectAll()" value="">Tümünü Seç</label>
        </div>
        <label for="durumKaydet" style="margin-left: 15px;">Durumu </label>
        <select class="form-control ddDurum" name="durumKaydet" id="durumKaydet"  ref="durumKaydet" onClick="durumDegistir();"  >
            <option value="0">İşleme Al</option>
            <option value="3">İptal Et</option>
            <option value="9">İptal Et GNC Kod</option>
            <option value="2">Onayla</option>
            <!-- <option value="4">Robot Seç Onayla</option>
            <option value="5">Robot Seç İptal Et</option> -->
        </select>  
        <tag id="robotSecTag" style="display:none;">
        <label class="lbl-sm "  >Robot:</label>
        <?php $dd->Make($ddAktifRobotlar,"robotSec","robotSec","sayfasiz",null,null,null,"Manuel");?>            
        </tag>
        <label for="aciklama" style="margin-left: 15px;">Aciklama: </label>
        <input type="text" name="aciklama" id="aciklama" class="form-control tel ">
        <button type="button" class="btn btn-primary btnSorgula" id="kaydet" name="kaydet" onClick="RobotIslem()" style="margin-left: 15px;">Kaydet</button>
    </div>
    
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableRobotHesap" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:30px;" align="center">
                    Sec
                </td>
                <td style="width:70px;" align="center">İşlem <br> Numarası</td>
                <td style="width:120px;" align="center">Bayi Adı</td>
                <td style="width:70px" align="center">Operatör <br> Tip</td>
                <td style="width:80px" align="center">Telefon <br> No</td>
                <td style="width:260px" align="center">Gelen Paket</td>
                <td style="width:60px" align="center">Satış Tutarı</td>
                <td style="width:180px" align="center">Geliş Tarihi<br>Sonuç Tarihi</td>
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
                        if ($takip->aciklama == "BimZnet") {
                            $renk="#ffdc82";
                        } 
                        else if ($takip->aciklama == "JetBim") {
                            $renk="#97d2fc";
                        }
                        else if ($takip->aciklama == "Jetislem"){
                            $renk="#ff829d";
                        }
                        else if ($takip->aciklama == "Znet"){
                            $renk="#b5caff";
                        } 
                        else if ($takip->aciklama == "Bimcell"){
                            $renk="#a6fff0";
                        }else {
                            $renk="#F5BCA9";
                        }
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
                $tutar=number_format( $takip->tutar, 3, '.', '');
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='12'> </td>
                </tr>
                
                <tr id='tr_$takip->id' name='aa' style='background-color:$renk'  >
                    <td style='width:40px;' align='center'>
                        <input type='checkbox' onClick='CbClicked($takip->id)' class='seciniz tableCb' id='cb_$takip->id' name='cbx' value='$takip->id' >
                    </td>
                    <td  align='center'>$takip->id</td>
                    <td align='center'>$takip->firmaAdi</td>
                    <td  align='center'  onClick='satirAc($takip->id)' >$takip->operatorAdi<br>$takip->tipAdi</td>
                    <td  align='center'>$takip->tel</td>
                    <td  align='center'>($takip->paketKodu) $takip->paketAdi</td>
                    <td  align='center'>$tutar</td>
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
                href='kontor-yuklemetakip?sayfa=$i&$filtreler'
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
function durumDegistir()
{
    if($("#durumKaydet")[0].value==2 )
        window.$("#robotSecTag").show(600);
    else
        window.$("#robotSecTag").hide(600);
    
}

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
function SelectAll()
{
    var checkboxes=$("[name='cbx']");
    if(document.getElementById("cb_tum").checked)
    {
        for(var i=0, n=checkboxes.length; i<n; i++) 
        {
            checkboxes[i].checked=true;
        }
    }
    else
    {
        for(var i=0, n=checkboxes.length; i<n; i++) 
        {
            checkboxes[i].checked=false;
        }
    }
    
}
            
function  CbClicked(id)
{
    var checked=document.getElementById("cb_"+id).checked;
    var checkboxes=$("[name='cbx']");
    var kutu='';
    console.log(checked);
    for(var i=0, n=checkboxes.length; i<n; i++) 
    {
        if (checkboxes[i].checked)
        {
            kutu += "var";
            break;
        }
    }

    if(kutu=="var")
    {
        window.$("#divKaydet").show(600);
    }
    else
    {
        window.$("#divKaydet").hide(600);
    }
}
function Temizle()
{
    window.location.href = "kontor-yuklemetakip/temizle"; 
}
function  RobotIslem()
{

    var SelectedCb=[];
    var checkboxes=window.$("[name='cbx']");
   
    for(var i=0, n=checkboxes.length; i<n; i++) 
    {
        
        if(checkboxes[i].checked)
        {
            SelectedCb.push(checkboxes[i].value);
        }
    }
    var DurumKaydet=window.$("#durumKaydet");
    var Robotlar=window.$("#robotSec");
    var aciklama=document.getElementById("aciklama").value;
    var durum=DurumKaydet[0].value;

    var gncKod = 0;

    if(durum == 9)
    {
        durum = 3;
        gncKod = 1;
    }

    if(Robotlar[0].value!=-1 && durum!=0)
    {
        if(durum==2)//onay
            durum=4;
        
        if(durum==3)//iptal
            durum=5;
    }

    var postBody={
        Cb:SelectedCb,
        Durum:durum,
        Robot:Robotlar[0].value,
        Aciklama:aciklama,
        GncKod:gncKod
    }

    $.ajax({
        type:'get',
        url:"ajax/YuklemeTakip/Durum",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
            {
                toastr.success(res.message);
               
                this.SelectedCb=[];
                location.reload();
            }
            else
            {
                toastr.error(res.message);
            }
        }
     });
    
}
</script>
@endsection
