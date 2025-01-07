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

    <title>Fiyat Grubu Ekle</title>
</head>
<?php
use App\Classes\DropDown;
$dd=new DropDown;

?>
<div>
    <div class="col-md-12" style="text-align:center">
        <h4 id="grupIsmi" class="modal-title">Yeni Kayıt</h4>
    </div>


        <form class="form-horizontal" id="modalPaket"
        style="margin-top:25px;"
            ref="yeniKullaniciForm"
            action="vue/yeniKullanici"
            method="post" >
            <div class="form-group " id="operatorDiv0" v-bind:class="{  'has-error has-feedback': operatorErr }">
                <label class="col-sm-2 control-label" for="Operator"> Operator: </label>
                <div class="col-sm-10 " id="operatorDiv1">
                    <?php $dd->Make($dd->DdOperator(),"operator","operator",null,$operator);?>
                </div>
            </div>
            <div class="form-group" v-bind:class="{  'has-error has-feedback': adiErr }">
                <label class="col-sm-2 control-label" for="adi"> Grup Adı: </label>
                <div class="col-sm-5">
                  <input name='adi' id='adi' type='text' class='form-control' value=''>
                </div>
            </div>
                <input type="hidden" name="_token" :value="csrf">
        </form>
        <div class="col-md-12">
            <div class="col-md-5">
            </div>
            <div class="col-md-2">

              <button  type='button' class='btn btn-success' onClick='sendForm()' style='background-color: green;'>Kaydet</button>

            </div>

        </div>
</div>

<script>

function sendForm()
{


    var validate=this.formValidate(0);
    if(validate!=false)
    {
        $.ajax({
        type:'post',
        url:"YeniGrupEkle",
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

    var adi= window.$("#adi")[0].value;
    var operator=window.$("#operatorDiv1").children("#operator")[0].value;
    var validate=true;
    var postBody=
    {
        operator:operator,
        adi:adi
    }

    if(adi.length<2 || adi.length>101 )
    {
        toastr.error("Grup adı alanı 3 - 100 karakter aralığında olmalıdır!");
        this.adiErr=true;
        validate=false;
    }
    if(operator==-1)
    {
        toastr.error("Lütfen Operator seçiniz   !");
        this.operatorErr=true;
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
