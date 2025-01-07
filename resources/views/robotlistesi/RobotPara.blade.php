<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script> window.Laravel={csrfToken:' {{csrf_token()}}'}</script>
    <link href="{{ URL::asset('public/css/bootstrap.min.css') }}"media="screen" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/font.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/general.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/new.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/style.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/toastr.min.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/bootstrapdate.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/switch.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/alertify.css') }}" rel='stylesheet'>
    <script src="{{ URL::asset('public/js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/moment.js') }}"></script>
    <script src="{{ URL::asset('public/js/toastr.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
    <script src="{{ URL::asset('public/js/bootstrapdate.js') }}"></script>
    <script src="{{ URL::asset('public/js/tarih.js') }}"></script>
    <script src="{{ URL::asset('public/js/alertify.js') }}"></script>
    <script>
      $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
      });
    </script>

    <title>Robot Ekle</title>
</head>
<?php

?>
<div style="margin-top:25px;" >
<h4 align="center" class="modalTitle" ref="robotAdi">Robot: {{$robot->adi}}</h4>
    <div class="form-horizontal" id="modalPaket"
        ref="yeniKullaniciForm"
 >
        
        <div class="form-group col-md-5" v-bind:class="{  'has-error has-feedback': tutarErr }">
            <label class="col-sm-2 control-label" for="tutar"> Tutar: </label>
            <div class="col-sm-2">
                <input ref="tutar" name="tutar" id="tutar" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group col-md-5" >
            <label class="col-sm-2 control-label" for="desc"> Açıklama: </label>
            <div class="col-sm-10">
                <textarea ref="desc" id="desc"  rows="4" cols="50">
                    
                </textarea>
            </div>
        </div>
        
            <input type="hidden" name="_token" value="csrf">
    </div>

    <div class="col-md-12">
        <div class="col-md-5">
        </div>
        <div class="col-md-2">
        <?php
            if($cikar=="true")
            {
                echo "<butto type='button' id='btnSend' class='btn btn-success' onClick='sendForm(\"cikar\",$id)' style='background-color: red;'>Çıkar</button>";
            }
            else
            {

                echo "<button type='button' id='btnSend' class='btn btn-success' onClick='sendForm(\"ekle\",$id)' style='background-color: green;'>Ekle</button>";

            }
        ?>
                
        
    </div>
        
    </div>
</div>
<script>
var input = document.getElementById("tutar");
input.addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("btnSend").click();
    }
});
</script>
<script>

function sendForm(type,id)
{
    
    var url="";
    if(type=="cikar")
    {
        url="RobotPara/Cikar";
    }
    if(type=="ekle")
    {
        url="RobotPara/Ekle";
    }

    var validate=this.formValidate(id);
    if(validate!=false)
    {
        $.ajax({
        type:'post',
        url:url,
        data:validate,
        success:function(res)
        {
            if(res.status=="true")
            {
                window.opener.location.reload(false);
                toastr.success(res.message);
                setTimeout(function(){ window.close();}, 500);
            } 
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
function formValidate(id)
{
    var desc=document.getElementById("desc");
    var tutar=document.getElementById("tutar");
    var validate=true;
    var postBody=
    {   aciklama:desc.value,
        tutar:tutar.value,
        id:id
    }

    if(tutar.value.length<1 || tutar.value.length>41 )
    {
        toastr.error("Tutar alanı boş bırakılamaz!");
        this.tutarErr=true;
        validate=false;
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