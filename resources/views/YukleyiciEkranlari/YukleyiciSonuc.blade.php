@extends('YukleyiciMasterPage')

@section('content')

<div class="container col-md-12" align = "center">
<?php
$part1=substr($tel,0,3);
$part2=substr($tel,3,3);
$part3=substr($tel,6,2);
$part4=substr($tel,8,2);
?>

<audio id="uyariYapSes">
  <source src="public/cevapGecikme.mp3" type="audio/mpeg">
  Your browser does not support the audio element.
</audio>

<b><p style="color:red; font-size:28; text-align:center;" id="uyariYazi2">{{session()->get( 'cevapsizIslem' )}}</p></b>


<b><p style="color:red; font-size:28; text-align:center;" id="uyariYazi"></p></b>

<hr>

<p style="text-align:center;  font-size:15px;"><b> Yüklenecek Numara</b></p>
<p style="text-align:center; color:red; font-size:30px;"><b>{{$part1}}-{{$part2}} {{$part3}} {{$part4}}</b></p>
<p style="text-align:center;  font-size:15px;"><b> Yüklenecek Paket</b></p>
<p style="text-align:center; font-size:30px;"><b>{{$paketAdi}}</b></p>
<p style="text-align:center;  font-size:15px;"><b> Resmi Satış Fiyatı</b></p>
<p style="text-align:center; color:red; font-size:30px;"><b>{{$resmiSatisFiyati}} TL</b></p>
<p style="text-align:center;  font-size:15px;"><b> Alış Fiyatı</b></p>
<p style="text-align:center; color:red; font-size:30px;"><b>{{$tutar}} TL</b></p>
<br>

<div class="col-md-12">
<div class="row">
<button  onClick="sonucBildir(2,{{$kayitId}},'ONAYLANDI')" style="font-size:48px !important; font-weight:bold; width:250px; height:100px;"
            class="btn  btnOnaylaBig"  
            id="sorgula" name="sorgula" >ONAYLA</button>
</div>
<hr>
</div>



<div class="col-md-4">
<br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'GÖNDERİLEN PAKETİN ALIM LİMİTİ DOLU')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >Hattın Alabileceği<br>Paket Limiti Dolu</button>
    </div>
    <br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'HATTIN STATÜSÜ UYGUN DEĞİL.')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >Hattın Statüsü Uygun Değil</button>
    </div>
    <br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'TURKCELL TARAFINDAN ONAYLANMAMAKTADIR. MÜŞTERİ HİZMETLERİNİ ARAYINIZ.')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >Turkcell Tarafından<br>Onaylanmamaktadır<br>Müşteri Hizm. Arayınız</button>
    </div>
    <br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'FATURALI HAT PAKET YÜKLENEMEZ')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >Faturalı Hat</button>
    </div>
    </div>
    <div class="col-md-4">
<br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'ABONEYE UYGUN TAHSİLAT PAKETİ BULUNMAMAKTADIR')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >Aboneye Uygun Tahsilat<br>Paketi Bulunmamaktadır</button>
    </div>
    <br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'YABANCI KİMLİK NUMARASI EKSİK PAKET YÜKLENEMEZ')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >Yabancı Kimlik No.</button>
    </div>
    <br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'İNTERNET PAKETİ VE GNC PAKETLER YÜKLENEMEZ')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >GNC Yüklenemez</button>
    </div>
    <br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'İNTERNET PAKETİ VE GNC PAKETLER YÜKLENEMEZ')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >İnternet Yüklenemez</button>
    </div>
    <br>
    <div class="row">
    <button onClick="sonucBildir(3,{{$kayitId}},'GÖNDERİLEN PAKET YÜKLENEMİYOR')" style="width:250px" class="btn btnIptalBig" id="sorgula" name="sorgula" >4907 Hatası</button>
    </div>
</div>



<div class="col-md-4">
<br>
    <div class="row">
    <button type="button"  onClick="sonucBildir(4,{{$kayitId}},'YÜKLENECEK PAKET POS CİHAZINDA YOK')" style="width:250px" class="btn  btnSorunluBig" id="sorgula" name="sorgula" >Yüklenecek Paket<br>Pos Cihazında Yok</button>
    </div>
    
    <br>
    <div class="row">
    <button type="button"  onClick="sonucBildir(4,{{$kayitId}},'CİHAZDA SORUN VAR')" style="width:250px" class="btn  btnSorunluBig" id="sorgula" name="sorgula" >Cihazda Sorun Var</button>
    </div>
    <br>
    <div class="row">
    <button type="button"  onClick="sonucBildir(4,{{$kayitId}},'LİMİT BİTTİ')" style="width:250px" class="btn  btnSorunluBig" id="sorgula" name="sorgula" >Limit Bitti</button>
    </div>

</div>

<br>

<script>

$(document).ready(function() {
  setInterval(function() {
    uyariYap()
  }, 120000);
});

function uyariYap()
{

    setInterval(function()
    {
        document.getElementById("uyariYazi").innerHTML ="LÜTFEN İŞLEME CEVAP VERİN!";
        var x = document.getElementById("uyariYapSes"); 
        x.play(); 
    }, 60000);

}


function sonucBildir(durum,id,aciklama) 
{
    $.ajax({
        type:'get',
        url:"api/Response?robotName={{$robotAdi}}&password={{$sifre}}&status="+durum+"&id="+id+"&yukleyici=1&aciklama="+aciklama,
        data:null,
        success:function(res)
        {
            if(res.status=="true")
            {
                toastr.success(res.message);
                window.location.replace("yukleyici-bekleyen");
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