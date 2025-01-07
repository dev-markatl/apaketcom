<?php
use App\Classes\DropDown;
$dd=new DropDown ;
$title=$_SERVER['HTTP_HOST'];
$title=str_replace("www.","",$title);
?>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script> window.Laravel={csrfToken:'{{csrf_token()}}'}</script>
    <link href="{{ URL::asset('public/css/bootstrap.min.css') }}"media="screen" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/font.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/customCss/general.css') }}" rel='stylesheet'>
    <link href="{{ URL::asset('public/css/toastr.min.css') }}" rel='stylesheet'>
    <script src="{{ URL::asset('public/js/jquery.min.js') }}"></script>

    <script src="{{ URL::asset('public/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/toastr.min.js') }}"></script>


    <title><?php echo $title;?></title>

</head>
<body style="align:center;" >
<div class="col-md-12" style="text-align: center; font-size: 45px; margin-top: 3%; margin-bottom: 0.5%; color:rgba(21, 162, 210, 0.9);"> <b>Yazılım Api</b></div>
<div class="col-md-12 col-lg-12 col-xs-12" style="   background: rgba(21, 162, 210, 0.9); margin-top:auto; padding: 20px 0px;    position: relative;">
    <div style="width: 1020px; margin-left:auto;  margin-right:auto;">
        <div class="" style="width: 1020px; margin-left:1px;  margin-right:auto;">
            <div class="" style="align:center;">
                <div style="width:900px; margin-top:-5%; margin-right:8%;" align="center">
                    <div  class="col-md-5 col-lg-5 col-xs-5" style=" margin-top:10%; margin-left:36%;">                  
                        <div >
                            <div class="panel panel-info col-md-12 col-lg-12 col-xs-12" style="min-height:80px; margin-top:0%;">
                                <div class="panel-body" style="">
                                    <h1 style="color:#5087b7;">GİRİŞ EKRANI</h1>
                                    <div class="login-box" style=" margin: auto;">
                                        <div id="bal" class="login-box-body loginVue">
                                            <p class="login-box-msg">Hoş Geldiniz</p>
                                                <form action="login-giris"  method="post">
                                                {{ csrf_field() }}
                                                    <loader v-if="loading"></loader>
                                                    <div class="form-group has-feedback">
                                                        <input type="tel" class="form-control" name="ceptelNo" id="ceptelNo" maxlength="10"  placeholder="5__ ___ __ __">
                                                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    </div>
                                                    <div class="form-group has-feedback">
                                                        <input type="password" class="form-control" id="sifre" name="sifre" placeholder="Şifre">
                                                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <button type="submit" id="buton" class="btn btn-info btn-block btn-flat" onClick="loginIn">Giriş Yap</button>
                                                        <div style="margin-bottom: 10px; margin-top: 10px;">
                                                       
                                                    </div>
                                                    <div class="social-auth-links text-center">
                                                    <hr>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <a href="yukleyici-giris" class="btn btn-warning btn-block btn-flat" >Yükleyici Olarak Devam Et</a>
                                                        <div style="margin-bottom: 10px; margin-top: 10px;">
                                                       
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <a href="rapor-giris" class="btn btn-success btn-block btn-flat" >Robotlar</a>
                                                        <div style="margin-bottom: 10px; margin-top: 10px;">                                                     
                                                    </div>
                                                   
                                                </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="kul_adi_sifre" class="modal fade" role="dialog" >
    <div  class="modal-dialog" style="width: 600px;">
    <div id="app" class="modal-content">
    <div class="modal-content">
    <form class="form-horizontal" id="yeniKullaniciForm"
                        action="login-Signup"
                        method="post"
                        >
                        {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Yeni Kayıt</h4>
                </div>
                <div class="modal-body">
                    
                        <div class="form-group " id="isimErr">
                            <label class="col-sm-2 control-label" for="isim"> İsim: </label>
                            <div class="col-sm-10">
                                <input ref="isim" name="isim" id="isim" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" id="soyadErr">
                            <label class="col-sm-2 control-label" for="soyad"> Soyad: </label>
                            <div class="col-sm-10">
                                <input ref="soyad" name="soyad" id="soyad" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" id="takmaAdErr" >
                            <label class="col-sm-2 control-label" for="takmaAd"> Kullanıcı Adı: </label>
                            <div class="col-sm-10">
                                <input  name="takmaAd" id="takmaAd" type="tel" maxlength="10"  placeholder="5__ ___ __ __" class="form-control">
                            </div>
                        </div>
                        <div class="form-group"  id="sifreErr">
                            <label class="col-sm-2 control-label" for="sifre1">Sifre: </label>
                            <div class="col-sm-10">
                                <input  ref="sifre1" name="sifre1" id="sifre1" type="password" class="form-control">
                            </div>
                        </div>
                        <div class="form-group"  id="sifreTErr">
                            <label class="col-sm-2 control-label" for="sifreT">Sifre Tekrar: </label>
                            <div class="col-sm-10">
                                <input ref="sifreT" name="sifreT" id="sifreT" type="password" class="form-control">
                            </div>
                        </div>
                        <div class="form-group"   id="firmaAdiErr">
                            <label class="col-sm-2 control-label" for="firmaAdi"> Firma Adı: </label>
                            <div class="col-sm-10">
                                <input ref="firmaAdi" name="firmaAdi" id="firmaAdi" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" id="mailErr" >
                            <label class="col-sm-2 control-label" for="mail"> Mail: </label>
                            <div class="col-sm-10">
                                <input ref="mail" name="mail" id="mail" type="email" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="sabitTel"> Sabit Tel: </label>
                            <div class="col-sm-10">
                                <input ref="sabitTel" name="sabitTel"  maxlength='10' id="sabitTel" type="tel" class="form-control">
                            </div>
                        </div>
                        <div class="form-group" id="cepTelErr">
                            <label class="col-sm-2 control-label" for="cepTel"> Cep No: </label>
                            <div class="col-sm-10">
                                <input ref="cepTel" name="cepTel" id="cepTel" type="tel" maxlength="10"  placeholder="5__ ___ __ __" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="vergiDairesi"> Vergi Dairesi: </label>
                            <div class="col-sm-10">
                                <input ref="vergiDairesi" name="vergiDairesi" id="vergiDairesi" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="vergiNo"> Vergi Numarası: </label>
                            <div class="col-sm-10">
                                <input ref="vergiNo" name="vergiNo" id="vergiNo" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="adres"> Adres: </label>
                            <div class="col-sm-10">
                                <input ref="adres" name="adres" id="adres" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group"  id="ilErr" >
                            <label class="col-sm-2 control-label" for="il"> İl: </label>
                            <div class="col-sm-10">
                                <?php $dd->Make($dd->DdIl(),"il","il",null,null,"onChange='ilChange()'");?>
                            </div>
                        </div>
                        <div class="form-group"  id="ilceErr" >
                            <label class="col-sm-2 control-label" for="ilce"> İlçe: </label>
                            <div class="col-sm-10">
                               <loader v-if="loading"></loader>
                               <?php $dd->Make(array(),"ilce","ilce",null,null);?>
                            </div>
                        </div>
                         
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onClick="sendForm()"  style="background-color: green;">Kaydol</button>
                </div>
                </form>
            </div>
    </div>
</div>
</body>
</html>
<?php
if(isset($kaydol))
{
    if($kaydol)
        echo "<script>  toastr.success('İşlem Başarılı!');</script>";
    else
        echo "<script>  toastr.error('İşlem Başarısız!');</script>";
}
if(isset($message))
{
    echo "<script>  toastr.error('$message');</script>";
}

?>

<script>
function ilChange()
{
   
    var ilId=document.getElementById("il").value;
    document.getElementById("ilce").innerHTML="";
    $.ajax({
        type:'get',
        url:"ajax/ilce?id="+ilId,
        data:"",
        dataType:"json",
        success:function(res)
        {
            
            $.each(res.Results, function (){
               
                        $("#ilce").append($("<option     />").val(this.id).text(this.adi));
                    });
        }
     });
    
}
function sendForm()
{
    
    
    var validate=this.validateForm();
    if(validate!=false)
    {   
        $( "#yeniKullaniciForm" ).submit();
    }
    

}
function validateForm()
{
    var validate=true;
    var isim=document.getElementById("isim").value;
    var soyad=document.getElementById("soyad").value;
    var firmaAdi=document.getElementById("firmaAdi").value;
    var cepTel=document.getElementById("cepTel").value;
    var takmaAd=document.getElementById("takmaAd").value;
    var sifre1=document.getElementById("sifre1").value;
    var sifreT=document.getElementById("sifreT").value;
    var mail=document.getElementById("mail").value;
    var il=document.getElementById("il").value;
    var ilce=document.getElementById("ilce").value;
    postBody=
    {
        isim:isim,
        soyad:soyad,
        takmaAd:takmaAd,
        sifre1:sifre1,
        firmaAdi:firmaAdi,
        mail:mail,
        sabitTel:sabitTel,
        vergiDairesi:vergiDairesi,
        vergiNo:vergiNo,
        cepTel:cepTel,
        adres:adres,
        ilce:ilce,

    }
    if(isim.length<2 || isim.length>20)
    {
        toastr.error("İsim alanı 2 karakterden uzun 20 karakterden küçük olmalıdır!");
        document.getElementById("isimErr").classList.add("has-error");
        document.getElementById("isimErr").classList.add("has-feedback");
        validate=false;
    }
    if(soyad.length<2 || soyad.length>20)
    {
        toastr.error("Soyad alanı 2 karakterden uzun 20 karakterden küçük olmalıdır!");
        document.getElementById("soyadErr").classList.add("has-error");
        document.getElementById("soyadErr").classList.add("has-feedback");

        validate=false;
    }
    if(firmaAdi.length<4 || firmaAdi.length>40)
    {
        toastr.error("Firma Adi alanı 4 karakterden uzun 40 karakterden küçük olmalıdır!");
        document.getElementById("firmaAdiErr").classList.add("has-error");
        document.getElementById("firmaAdiErr").classList.add("has-feedback");
        validate=false;
    }
    if(cepTel.length!=10 )
    {
        toastr.error("Cep No alanı başında '0' olmadan '10' haneli telefon numaranızı girmenizi istemektedir!");
        document.getElementById("cepTelErr").classList.add("has-error");
        document.getElementById("cepTelErr").classList.add("has-feedback");

        validate=false;
    }
    if(takmaAd.length!=10 )
    {
        toastr.error("Kullanıcı Adı alanı başında '0' olmadan '10' haneli telefon numaranızı girmenizi istemektedir!");
        document.getElementById("takmaAdErr").classList.add("has-error");
        document.getElementById("takmaAdErr").classList.add("has-feedback");

        validate=false;
    }
  
    if(sifre1.length<5 )
    {
        toastr.error("Şifre alanı en az 6 karakter olabilir ! ");
        document.getElementById("sifreErr").classList.add("has-error");
        document.getElementById("sifreErr").classList.add("has-feedback");

        validate=false;
    }
    if(sifre1 != sifreT )
    {
        toastr.error("Şifreleriniz uyuşmamaktadır ! ");
        document.getElementById("sifreTErr").classList.add("has-error");
        document.getElementById("sifreTErr").classList.add("has-feedback");

        validate=false;
    }

    var re = /\S+@\S+\.\S+/;
    var mailValidate= re.test(mail);
    if(!mailValidate)
    {

        toastr.error("Lütfen Uygun bir Mail Adresi Giriniz ! ");
        document.getElementById("mailErr").classList.add("has-error");
        document.getElementById("mailErr").classList.add("has-feedback");
        validate=false;
    }
        
    if(il==-1)
    {
        toastr.error("Lütfen il seçiniz! ");
        document.getElementById("ilErr").classList.add("has-error");
        document.getElementById("ilErr").classList.add("has-feedback");
        validate=false;
    }
    if(ilce==-1)
    {
        toastr.error("Lütfen ilçe seçiniz! ");
        document.getElementById("ilceErr").classList.add("has-error");
        document.getElementById("ilceErr").classList.add("has-feedback");
        validate=false;
    }
    if(validate)
    {
        return postBody;
    }
    else
        return validate;
    
}

</script>
        






