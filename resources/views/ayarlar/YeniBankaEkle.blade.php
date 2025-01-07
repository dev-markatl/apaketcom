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
<div  >        
    <form style="margin-top:25px;"
        class="form-horizontal" id="modalPaket"
        ref="yeniKullaniciForm"
        action="vue/yeniKullanici"
        method="post" >
        <div class="form-group " v-bind:class="{  'has-error has-feedback': bankaAdiErr }">
            <label class="col-sm-2 control-label" for="bankaAdi"> Banka Adı: </label>
            <div class="col-sm-5">
                <input name="bankaAdi" id="bankaAdi" type="text" value="{{$bankaAdi}}" class="form-control">
            </div>
        </div>
        <div class="form-group " v-bind:class="{  'has-error has-feedback': subeAdiErr }">
            <label class="col-sm-2 control-label" for="subeAdi"> Şube Adı: </label>
            <div class="col-sm-5">
                <input name="subeAdi" id="subeAdi" type="text" value="{{$subeAdi}}" class="form-control">
            </div>
        </div>
        <div class="form-group " v-bind:class="{  'has-error has-feedback': subeKoduErr }">
            <label class="col-sm-2 control-label" for="subeKodu"> Şube Kodu: </label>
            <div class="col-sm-5">
                <input name="subeKodu" id="subeKodu" type="text" value="{{$subeKodu}}" class="form-control">
            </div>
        </div>
        <div class="form-group " v-bind:class="{  'has-error has-feedback': hesapNoErr }">
            <label class="col-sm-2 control-label" for="hesapNo"> Hesap No: </label>
            <div class="col-sm-5">
                <input name="hesapNo" id="hesapNo" type="text" value="{{$hesapNo}}" class="form-control">
            </div>
        </div>
        <div class="form-group " v-bind:class="{  'has-error has-feedback': ibanNoErr }">
            <label class="col-sm-2 control-label" for="ibanNo"> İban No: </label>
            <div class="col-sm-5">
                <input name="ibanNo" id="ibanNo" type="text" value="{{$ibanNo}}" class="form-control">
            </div>
        </div>
        <div class="form-group " v-bind:class="{  'has-error has-feedback': hesapSahibiErr }">
            <label class="col-sm-2 control-label" for="hesapSahibi"> Hesap Sahibi: </label>
            <div class="col-sm-5">
                <input name="hesapSahibi" id="hesapSahibi" value="{{$hesapSahibi}}" type="text" class="form-control">
            </div>
        </div>
        
    </form>
    <div class="col-md-12">
        <div class="col-md-5">
        </div>
        <div class="col-md-2">
            <?php
                if($update=="true")
                    echo "<button type='button'  class='btn btn-success' onClick='sendForm(\"update\",$id)' style='background-color: green;'>Güncelle</button>";
                else
                    echo "<button  type='button'  class='btn btn-success' onClick='sendForm(\"new\",0)' style='background-color: green;'>Kaydet</button>";
            ?>
        </div>
        
    </div>
        
    
</div>
<script>
function sendForm(type,id)
{
    var url="";
    if(type=="new")
    {
        url="YeniBanka";
    }
    if(type=="update")
    {
        url="BankaGuncelle";
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
    
    var bankaAdi=window.$("#bankaAdi")[0];
    var subeAdi=window.$("#subeAdi")[0] ;
    var subeKodu=window.$("#subeKodu")[0] ;
    var hesapNo=window.$("#hesapNo")[0] ;
    var ibanNo=window.$("#ibanNo")[0] ;
    var hesapSahibi=window.$("#hesapSahibi")[0] ;
    var aktif=window.$("#aktif") [0];
    var id=id;
    var validate=true;
    var postBody=
    {
        bankaAdi:bankaAdi.value,
        subeAdi:subeAdi.value,
        subeKodu:subeKodu.value,
        hesapNo:hesapNo.value,
        ibanNo:ibanNo.value,
        hesapSahibi:hesapSahibi.value,
        id:id
    }

    if(bankaAdi.value.length<2 || bankaAdi.value.length>41 )
    {
        toastr.error("Banka Adı alanı 3 ila 40 karakter aralığında olmalıdır !");
        validate=false;
    }
    if(subeAdi.value.length<2 || subeAdi.value.length>41 )
    {
        toastr.error("Şube Adı alanı 3 ila 40 karakter aralığında olmalıdır !");
        validate=false;
    }
    if(subeKodu.value.length<1 || subeKodu.value.length>41 )
    {
        toastr.error("Şube Kodu alanı Boş Geçilemez !");
        validate=false;
    }
    if(hesapNo.value.length<2 || hesapNo.value.length>41 )
    {
        toastr.error("Hesap No alanı 3 ila 40 karakter aralığında olmalıdır !");
        validate=false;
    }
    if(ibanNo.value.length<2 || ibanNo.value.length>41 )
    {
        toastr.error("İban No alanı 3 ila 40 karakter aralığında olmalıdır !");
        validate=false;
    }
    if(hesapSahibi.value.length<2 || hesapSahibi.value.length>41 )
    {
        toastr.error("Hesap Sahibi alanı 3 ila 40 karakter aralığında olmalıdır !");
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