<?php 
use App\Classes\DropDown;
use App\Classes\CommonFunctions;

$_SESSION["menu"]=5;
$_SESSION["altmenu"]=1;
$dd=new DropDown ;
$cf=new CommonFunctions;
?>
@extends('BayiMasterPage')
@section('content')
<div >

    <form class="form-horizontal" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="bayi-fatura-faturatakip" >
        <div class="form-group col-md-12 filter"  align="left" >
            <div class="col-md-8" style="margin-left:-14px;">
                <input type="hidden" value="false" name="session">
                <label class="lbl-sm " >Tarih:</label>
                <?php $dd->MakeInput("tarih1","tarih1","FaturaTakip");?>
                - 
                <?php $dd->MakeInput("tarih2","tarih2","FaturaTakip");?>

                <label class="lbl-lg " >Kurum:</label>
                <?php $dd->Make($dd->DdKurum(),"kurum","kurum","FaturaTakip");?>
              

                
                <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
                
                <input type="button" onClick="Temizle()" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Temizle">
            </div>
            
            <div class="col-md-4 btnDurumFiltreDiv" align="right" >
                
                <?php  $dd->BtnDurum("durum",0,"FaturaTakip","B",null,"background-color:#EAFFA0");?>
                <?php  $dd->BtnDurum("durum",2,"FaturaTakip","O",null,"background-color:#ACF2AF");?>
                <?php  $dd->BtnDurum("durum",3,"FaturaTakip","I",null,"background-color:#F5BCA9");?>
                <?php  $dd->BtnDurum("durum",-1,"FaturaTakip","T",null,"background-color:lightblue");?>
            </div>
        </div>
        <div class="form-group col-md-12 filter" align="left"  >
            <label class="lbl-sm " >Bayi:</label>
            <?php $dd->Make($dd->DdBayiler(),"bayiler","bayiler","FaturaTakip");?>

            <label class="lbl-lg "  >Robot Adi:</label>
            <?php $dd->Make($dd->DdRobotlar(),"robotlar","robotlar","FaturaTakip");?>            
        </div>
        <div class="form-group col-md-8 filter" align="left"  >
            <label class="lbl-xlg " >Telefon No:</label>
            <?php $dd->MakeInput("tel","tel","FaturaTakip");?>
            <input type="submit" class="btn btn-info btnSorgula"  id="sorgula" name="sorgula" value="Sorgula">
            
        </div>
        <div class="form-group col-md-4 " align="right" >
                <label class="lbl-lg " >Toplam:</label>
                <?php echo "<b>".$satisToplam[0]->toplamTutar."</b>"  ?>
        </div>
    </form>
    
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableRobotHesap" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
              
                <td style="width:70px;" align="center">İşlem <br> Numarası</td>
                <td style="width:190px;" align="center">Bayi Adı</td>
                <td style="width:70px" align="center">Kurum</td>
                <td style="width:80px" align="center">Telefon No</td>
                <td style="width:190px" align="center">Abone Adı</td>
                <td style="width:115px" align="center">Son Odeme<br>Tarihi</td>
                <td style="width:70px" align="center">Satış Tutarı</td>
                <td style="width:130px" align="center">Geliş Tarihi<br>Sonuç Tarihi</td>
                <td style="width:115px" align="center">Robot Adı</td>
                <td style="width:64px" align="center">Robot Süresi</td>
                <td style="width:64px" align="center">Sonuç Süresi</td>
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
                        $renk="#F5BCA9";
                        break;  
                        
                    case '4'://olumsuz cevap
                        $renk="#cd27da";
                        break;     
                }
                $robotAdi=$takip->robotAdi;
               
                $robotSonucSuresi=$cf->zamanHesapla($takip->almaZamani,$takip->donmeZamani);
                $sistemSonucSuresi=$cf->zamanHesapla($takip->created_at,$takip->donmeZamani);
                if($takip->donmeZamani==null)
                    $takip->donmeZamani="--";
                $tutar=number_format( $takip->tutar, 3, '.', '');
                echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='11'> </td>
                </tr>
                
                <tr id='tr_$takip->id' name='aa' style='background-color:$renk'  >
                   
                    <td  align='center'>$takip->id</td>
                    <td align='center'>$takip->firmaAdi</td>
                    <td  align='center'  >$takip->kurumAdi</td>
                    <td  align='center'>$takip->tel</td>
                    <td  align='center'>$takip->aboneAdi</td>
                    <td  align='center'>$takip->sonOdemeTarihi</td>
                    <td  align='center'>$tutar</td>
                    <td  align='center'>$takip->created_at<br> $takip->donmeZamani </td>
                    <td  align='center'>$robotAdi</td>
                    <td  align='center'>$robotSonucSuresi </td>
                    <td  align='center'>$sistemSonucSuresi </td>
                    
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
                href='bayi-fatura-faturatakip?sayfa=$i&$filtreler'
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
    var options={
    format: 'YYYY-MM-DD',
    locale:'tr'
    }

    $( ".hasDatePicker" ).datetimepicker(options);
});
 

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
    window.location.href = "fatura-faturatakip/temizle"; 
}
function  RobotIslem()
{
    console.log("123123");
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
    var Robotlar=window.$("#robotlar");
    var aciklama=document.getElementById("aciklama").value;
    var postBody={
        Cb:SelectedCb,
        Durum:DurumKaydet[0].value,
        Robot:Robotlar[0].value,
        Aciklama:aciklama
    }

    $.ajax({
        type:'get',
        url:"ajax/FaturaTakip/Durum",
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
