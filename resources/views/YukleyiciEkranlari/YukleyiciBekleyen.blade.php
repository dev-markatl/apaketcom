@extends('YukleyiciMasterPage')

@section('content')

<?php 
$disabled="";
if($bekleyenSayisi==0)
  $disabled="disabled";
?>

<div class="container col-md-12" align="center">

<audio id="bekleyenVarSes">
  <source src="public/bekleyenVar.mp3" type="audio/mpeg">
  Your browser does not support the audio element.
</audio>

<p style="text-align:center; color:red; font-size:20px;" ><b id="bekleyenKayitSayisi"> Bekleyen Kayıt Sayısı: {{$bekleyenSayisi}}</b></p>
<p style="text-align:center;  font-size:20px;"><b>Bakiye:{{$bakiye}} TL</b></p>
<div class="row">
<button onClick="Sorgula()" style="width:200px" id="bekleyencek" class="btn btn-info btnSorgulaBig" {{$disabled}}>Bekleyen Sorgula</button>
<!-- <a href="yukleyici-sonuc" class="btn"  ><button    style="width:200px" id="bekleyencek" class="btn btn-info btnSorgulaBig">Bekleyen Sorgula</button></a> -->

</div>
<br>
<div class="row">

<input type="button"  onClick="RaporlarClick({{$id}})" style="width:200px"
            class="btn btn-info btnSorgulaBig"  
            id="sorgula" name="sorgula" 
            value="Raporlar">
</div>
<br>
<div class="row">
<hr>
<a href="yukleyici-cikis" class="btn"  ><button    style="width:200px" id="cik" class="btn btn-info btnCikisBig">Çıkış</button></a>
</div>
<br>





<script>

function RaporlarClick(id)
{
    var width=1080;
    var height=530;
    var url="ajax/YukleyiciHesapHareketleri?sayfa=1&";

    
    window.open(url+"id="+id, "aa", "height="+height+",width="+width);
}
$(document).ready(function() {
  cache_clear();
  setInterval(function() {
    cache_clear()
  }, 60000);
});

function cache_clear() {
  //window.location.reload(true);
  // window.location.reload(); use this if you do not remove cache
  $.ajax({
        type:'post',
        url:"yukleyici-bekleyen-sayisi",
        data:null,
        success:function(res)
        {
          document.getElementById("bekleyenKayitSayisi").innerHTML="Bekleyen Kayıt Sayısı: "+res.count;
          console.log(res.count);
          if(res.count==0)
            document.getElementById("bekleyencek").disabled=true;
          else
          {
            var x = document.getElementById("bekleyenVarSes"); 
            x.play();
            document.getElementById("bekleyencek").disabled=false;
          }
            
        }
        });
}
function Sorgula() 
{
  document.getElementById("bekleyencek").disabled=true;
  //window.location("yukleyici-sonuc");
  window.location.href = "yukleyici-sonuc";
}

</script>
<?php


if(session()->has('message'))
{
    $message=session()->get('message');
    echo "<script>  toastr.error('$message');</script>";
}
?>

@endsection