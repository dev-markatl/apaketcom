@extends('YukleyiciMasterPage')

@section('content')



<div class="container col-md-12" align="center">
<p style="text-align:center;  font-size:20px;"><b>Bakiye:{{$bakiye}} TL</b></p>

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
<a href="rapor-cikis" class="btn"  ><button    style="width:200px" id="cik" class="btn btn-info btnCikisBig">Çıkış</button></a>
</div>
<br>





<script>

function RaporlarClick(id)
{
    var width=1080;
    var height=530;
    var url="ajax/RaporHesapHareketleri?sayfa=1&";

    
    window.open(url+"id="+id, "aa", "height="+height+",width="+width);
}

</script>
<?php

?>

@endsection