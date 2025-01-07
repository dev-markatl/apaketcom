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
use App\Classes\DropDown;
$dd=new DropDown ;
if($sorgu==1)
    $sorgu="checked";
if($aktif==1)
    $aktif="checked";
if($yukle==1)
    $yukle="checked";
if($sorgu==1)
    $sorgu="checked";
if($fatura==1)
    $fatura="checked";
?>
<div>
    <div class="col-md-12" style="text-align:center">
        <h4 id="paketIsmi" class="modal-title">Yeni Kayıt</h4>
    </div>


    <form style="margin-top:25px;"
        class="form-horizontal" id="modalPaket"
        ref="yeniKullaniciForm"
        action="vue/yeniKullanici"
        method="post" >
        <div class="form-group " v-bind:class="{  'has-error has-feedback': adiErr }">
            <label class="col-sm-2 control-label" for="adi"> Kullanıcı Adı: </label>
            <div class="col-sm-5">
                <?php echo "<input name='adi' id='adi' type='text' class='form-control' value='$adi'>"; ?>
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': sifreErr }">
            <label class="col-sm-2 control-label" for="sifre">Şifre: </label>
            <div class="col-sm-5">
                <?php echo "<input name='sifre' id='sifre' type='text' class='form-control' value='$sifre'>"; ?>
            </div>
        </div>
        <div class="form-group " id="operatorDiv0" v-bind:class="{  'has-error has-feedback': operatorErr }">
            <label class="col-sm-2 control-label" for="operator"> Operator: </label>
            <div class="col-sm-10 " id="operatorDiv1">
                <?php $dd->Make($dd->DdOperator(),"operator","operator",null,$operator);?>
            </div>
        </div>
        <div class="form-group " id="operatorDiv0" v-bind:class="{  'has-error has-feedback': fiyatgrupErr }">
            <label class="col-sm-2 control-label" for="fiyatgrup"> Fiyat Grubu: </label>
            <div class="col-sm-10 " id="fiyatgrupDiv1">
                <select  style='border-color:;' class='form-control ddFiyatGrup' name='fiyatgrup' id='fiyatgrup'>
                  <option value='-1'>Seçiniz</option>
                  @if($aktifgrup == 0)
                  <option  value='0' selected>ANA FIYAT GRUBU</option>
                  @else
                  <option  value='0'>ANA FIYAT GRUBU</option>
                  @endif
                  @foreach($fiyatgruplar as $fiyatgrup)
                    @if($aktifgrup == $fiyatgrup->id)
                    <option value='{{$fiyatgrup->id}}' selected>{{$fiyatgrup->grup_ad}}</option>
                    @else
                    <option value='{{$fiyatgrup->id}}'>{{$fiyatgrup->grup_ad}}</option>
                    @endif
                  @endforeach
                </select>
            </div>
        </div>
        <div class="form-group " id="turDiv0" v-bind:class="{  'has-error has-feedback': turuErr }">
            <label class="col-sm-2 control-label" for="turu"> Robot Türü: </label>
            <div class="col-sm-10 " id="turDiv1">
            <?php $dd->Make($dd->DdRobotTuru(),"turu","turu",null,$turu);?>
            </div>
        </div>
        <div class="form-group " id="bayilerDiv0" v-bind:class="{  'has-error has-feedback': bayiErr }">
            <label class="col-sm-2 control-label" for="bayiler"> Bayi 1: </label>
            <div class="col-sm-10 " id="bayilerDiv1">
            <?php $dd->Make($dd->DdAktifBayiler(),"bayiler","bayiler",null,$bayi);?>
            </div>
        </div>

        <div class="form-group " id="bayiler2Div0" v-bind:class="{  'has-error has-feedback': bayiErr }">
            <label class="col-sm-2 control-label" for="bayiler"> Bayi 2: </label>
            <div class="col-sm-10 " id="bayiler2Div1">
            <?php $dd->Make($dd->DdAktifBayiler(),"bayiler2","bayiler2",null,$bayi2);?>
            </div>
        </div>

        <div class="form-group" >
            <label class="col-sm-2 control-label" for="sorguswdiv"> Robot Sorgu: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch" id="sorguswdiv">
                <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='sorguSw' value='1' $sorgu  id='sorguSw'>"; ?>
                    <label class="onoffswitch-label" for="sorguSw">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group" >
            <label class="col-sm-2 control-label" for="yuklemeswdiv"> Robot Yükleme: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch" id="yuklemeswdiv">
                <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='yuklemeSw' value='1' $yukle  id='yuklemeSw'>"; ?>
                    <label class="onoffswitch-label" for="yuklemeSw">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group" >
            <label class="col-sm-2 control-label" for="faturaswdiv"> Robot Fatura: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch" id="faturaswdiv">
                <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='faturaSw' value='1' $fatura  id='faturaSw'>"; ?>
                    <label class="onoffswitch-label" for="faturaSw">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group" >
            <label class="col-sm-2 control-label" for="durumswdiv"> Robot Durum: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch"  id="durumswdiv">
                <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='durumSw' value='1' $aktif  id='durumSw'>"; ?>
                    <label class="onoffswitch-label" for="durumSw" >
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>
            <input type="hidden" name="_token" :value="csrf">
    </form>
        <div class="col-md-12">
            <div class="col-md-5">
            </div>
            <div class="col-md-2">
                <?php
                    if($update)
                        echo "<button  type='button' class='btn btn-success' onClick='updateForm($id)' style='background-color: green;'>Güncelle</button>";
                    else
                        echo "<button class='btn btn-success' onClick='sendForm()' style='background-color: green;'>Kaydet</button>";
                ?>


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
        url:"YeniRobot",
        data:validate,
        success:function(res)
        {
            if(res.status=="true")
            {
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
function updateForm(id)
{


    var validate=formValidate(id);
    if(validate!=false)
    {
        $.ajax({
        type:'post',
        url:"RobotGuncelle",
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
    var operator=window.$("#operatorDiv1").children("#operator")[0];
    var turu=window.$("#turDiv1").children("#turu")[0];
    var bayi=window.$("#bayilerDiv1").children("#bayiler")[0];
    var bayi2=window.$("#bayiler2Div1").children("#bayiler2")[0];
    var adi=window.$("#adi")[0];
    var sifre=window.$("#sifre")[0];
    var aktif=window.$("#durumSw")[0];
    var sorgu=window.$("#sorguSw")[0];
    var yukle=window.$("#yuklemeSw")[0];
    var fatura=window.$("#faturaSw")[0];
    var validate=true;
    var a=0;
    var s=0;
    var y=0;
    var f=0;

    if(aktif.checked)
        a=1;
    else
        a=0;

    if(sorgu.checked)
        s=1;
    else
        s=0;

    if(yukle.checked)
        y=1;
    else
        y=0;

    if(fatura.checked)
        f=1;
    else
        f=0;
    var postBody=
    {
        operator:operator.value,
        fiyatgrup:fiyatgrup.value,
        turu:turu.value,
        bayi:bayi.value,
        bayi2:bayi2.value,
        adi:adi.value,
        sifre:sifre.value,
        aktif:a,
        sorgu:s,
        yukle:y,
        fatura:f,
        id:id
    }
    console.log(postBody)
    if(adi.value.length<2 || adi.value.length>41 )
    {
        toastr.error("Robot Adı alanı 3 ila 40 karakter aralığında olmalıdır !");
        validate=false;
    }
    if(sifre.value.length<1 || sifre.value.length>41 )
    {
        toastr.error("Robot sifre alanı boş geçilemez !");
        validate=false;
    }

    if(operator.value==-1)
    {
        toastr.error("Lütfen Operator seçiniz   !");
        validate=false;
    }
    if(turu.value==-1)
    {
        toastr.error("Lütfen Tür seçiniz   !");
        validate=false;
    }
    if(bayi.value==-1)
    {
        toastr.error("Lütfen kullanıcı seçiniz!");
        validate=false;
    }
    if(bayi2.value==-1)
    {
        toastr.error("Lütfen 2. kullanıcı seçiniz!");
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
