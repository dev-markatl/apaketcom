<?php 
use App\Classes\DropDown;

$_SESSION["menu"]=5;
$_SESSION["altmenu"]=4;
$dd=new DropDown ;


?>
@extends('MasterPage')
@section('content')
<div  >
    
    <form style="margin-top:25px;"
        class="form-horizontal" id="modalPaket"
        value="yeniKullaniciForm"
        action="vue/yeniKullanici"
        method="post" >

        <div class="form-group">
            <label class="col-sm-2 control-label" for="takmaAd"> Robot Alt Limit: </label>
            <div class="col-sm-5">
                <input value="{{$robotAltLimit}}" name="robotAltLimit" id="robotAltLimit" type="text" class="form-control">
            </div>
        </div>

     

        <div class="form-group">
            <label class="col-sm-2 control-label" for="takmaAd"> İstek Süreli İptal (Saniye): </label>
            <div class="col-sm-5">
                <input value="{{$istekIptalSuresi}}" name="istekIptalSuresi" id="istekIptalSuresi" type="text" class="form-control">
            </div>
        </div>
        
     
        </div>
     
    </form>
 
    <div class="col-md-12">
        <div class="col-md-6"> </div>
        <div class="col-md-2">
            <button  type="button"  class="btn btn-success" onClick="sendForm('update')" style="background-color: green;">Güncelle</button>      
        </div>

    </div>
        
    
</div>
@endsection

<script>

function sendForm(type)
{
    

    var url="ajax/OzelAyarlar/Update";
    

    var validate=this.validateForm();
    if(validate!=false)
    {
        console.log(validate);
        $.ajax({
        type:'post',
        url:url,
        data:validate,
        success:function(res)
        {
            if(res.status=="true")
                toastr.success(res.message);
            else
                toastr.error(res.message);
        }
        });
      

    }
    else
    {
        console.log(validate);
    }
    
}


function validateForm()
{
    var validate=true;
    
    var robotAltLimit=document.getElementById("robotAltLimit").value;
    
    var istekIptalSuresi=document.getElementById("istekIptalSuresi").value;

    var postBody=
    {
        
        robotAltLimit:robotAltLimit,
        istekIptalSuresi:istekIptalSuresi
    }
    
    
    if(validate)
    {
        return postBody;
    }
    else
    {
        return validate;
    }
    
}
</script>