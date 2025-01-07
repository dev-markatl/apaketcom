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

    <title>Yeni Numara Ekle</title>
</head>
<div  >        
    <form style="margin-top:25px;"
        class="form-horizontal" id="modalPaket"
        ref="yeniKullaniciForm"
        action="vue/yeniKullanici"
        method="post" >

        <div class="form-group " v-bind:class="{  'has-error has-feedback': telefonNoErr }">
            <label class="col-sm-2 control-label" for="telefonNo"> Telefon Numarası: </label>
            <div class="col-sm-5">
                <input name="telefonNo" id="telefonNo" type="text" class="form-control">
            </div>
        </div>

        <div class="form-group" >
            <label class="col-sm-2 control-label" for="sorguswdiv"> Sorgu Blok: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch" id="sorguswdiv">
                <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='sorguSw' value='1'  id='sorguSw'>"; ?>
                    <label class="onoffswitch-label" for="sorguSw">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group" >
            <label class="col-sm-2 control-label" for="yuklemeswdiv"> Yükleme Blok: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch" id="yuklemeswdiv">
                <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='yuklemeSw' value='1'  id='yuklemeSw'>"; ?>
                    <label class="onoffswitch-label" for="yuklemeSw">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>




       
        
    </form>
    <div class="col-md-12">
        <div class="col-md-5">
        </div>
        <div class="col-md-2">
            <?php
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
        url="YeniKaraListe";
    }

    var validate=this.formValidate();
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
function formValidate()
{
    
    var telefonNo = window.$("#telefonNo")[0];
    var sorguBlok = window.$("#sorguSw")[0];
    var yuklemeBlok = window.$("#yuklemeSw")[0];
    var validate=true;

    if(sorguBlok.checked)
        s=1;
    else
        s=0;

    if(yuklemeBlok.checked)
        y=1;
    else
        y=0;

    var postBody=
    {
        telefonNo:telefonNo.value,
        sorguBlok:s,
        yuklemeBlok:y,
    }

    if(telefonNo.value.length != 10)
    {
        toastr.error("Telefon numarası 10 karakter olmalıdır!");
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