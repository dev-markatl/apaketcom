<?php
use App\Classes\DropDown;

$_SESSION["menu"]=2;
$_SESSION["altmenu"]=3;
$dd=new DropDown ;


?>
@extends('MasterPage')
@section('content')
<script src="{{ URL::asset('public/js/FiyatGruplari.js') }}"></script>

<form class="form-horizontal" method="post" align="center" name="faturalistesi" style=" display:inline ;clear:both; " id="faturalistesi" action="kontor-paketlistesi" >
{{csrf_field()}}
    <div class="form-group col-md-12 filter" align="left">

        <input type="button"  onClick="FiyatGrubuEkle(0)"
        class="btn btn-info btnYeni "   style="margin-left:980px;"
        id="sorgula" name="sorgula"
        value="Yeni Grup Ekle">
    </div>

</form>


<table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tablePaket" >
    <tbody>
        <tr style="color:white; background-color:#0b3779">
            <td style="width:30px;" align="center">Operatör</td>
            <td style="width:100px;" align="center">Fiyat Grubu</td>
            <td style="width:30px;" align="center">İşlem</td>
        </tr>
        @foreach($gruplar as $grup)
        <tr>
          <td  align='center'>{{$grup->operator->adi}}</td>
          <td  align='center'>{{$grup->grup_ad}}</td>
          <td align='center'>
            <a href="kontor-grupduzenle?i={{$grup->id}}" type="button" class="btn btn-warning btnDuzenle" id="duzenle" name="duzenle" style="margin-left: 15px;">Düzenle</a>
            @if($grup->kullanimda == 0)
            <button onclick="GrupSil({{$grup->id}})" class="btn btn-danger btnSil" id="sil" name="sil" style="margin-left: 15px;">Sil</button>
            @else
            <button class="btn btn-danger btnSil" id="sil" name="sil" style="margin-left: 15px;"disabled>Sil</button>
            @endif
          </td>
        </tr>
        @endforeach
    </tbody>
</table>


<script>

function GrupSil(id)
{

      var postBody={
      id:id,
      }
      $.ajax({
          type:'post',
          url:"ajax/kontor-grupsil",
          data:postBody,
          success:function(res)
          {
              if(res.status=="true")
              {
                toastr.success(res.message);
                location.reload(true);
              }   
              else
                  toastr.error(res.message);
          }
      });

}

</script>


@endsection
