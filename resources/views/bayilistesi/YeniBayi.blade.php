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

if($aktif==1)
    $aktif="checked";
if($yetkiYukle==1)
    $yetkiYukle="checked";
if($yetkiSorgu==1)
    $yetkiSorgu="checked";
if($yetkiFatura==1)
    $yetkiFatura="checked";
?>
<div  >    
    <form style="margin-top:25px;"
        class="form-horizontal" id="modalPaket"
        ref="yeniKullaniciForm"
        action="vue/yeniKullanici"
        method="post" >
        <div class="form-group" v-bind:class="{  'has-error has-feedback': takmaAdErr }">
            <label class="col-sm-2 control-label" for="takmaAd"> Kullanıcı Adı: </label>
            <div class="col-sm-5">
                <?php echo "<input name='takmaAd' id='takmaAd' type='tel' maxlength='10'  placeholder='5__ ___ __ __' class='form-control' value='$takmaAd'>"; ?>  
            </div>
        </div>
        <div class="form-group "  v-bind:class="{  'has-error has-feedback': isimErr }">
            <label class="col-sm-2 control-label" for="isim"> İsim: </label>
            <div class="col-sm-5">
                <?php echo "<input name='isim' id='isim' type='text' class='form-control' value='$ad'>"; ?> 
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': soyadErr }">
            <label class="col-sm-2 control-label" for="soyad"> Soyad: </label>
            <div class="col-sm-5">
                <?php echo "<input name='soyad' id='soyad' type='text' class='form-control' value='$soyAd'>"; ?> 
            </div>
        </div>
        <?php
        if($update=="false")
            echo "
            <div class='form-group'  id='sifreErr'>
                <label class='col-sm-2 control-label' for='sifre1'>Sifre: </label>
                <div class='col-sm-5'>
                    <input  ref='sifre1' name='sifre1' id='sifre1' type='password' class='form-control'>
                </div>
            </div>
            <div class='form-group'  id='sifreTErr'>
                <label class='col-sm-2 control-label' for='sifreT'>Sifre Tekrar: </label>
                <div class='col-sm-5'>
                    <input ref='sifreT' name='sifreT' id='sifreT' type='password' class='form-control'>
                </div>
            </div>";
        else
            echo "
            <div class='form-group'  id='sifreErr'>
                <label class='col-sm-2 control-label' for='sifre1'>Sifre: </label>
                <div class='col-sm-5'>
                    <input  ref='sifre1' name='sifre1' id='sifre1' type='password' class='form-control'>
                </div>
            </div>";
        ?>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': firmaAdiErr }">
            <label class="col-sm-2 control-label" for="firmaAdi"> Firma Adı: </label>
            <div class="col-sm-5">
                <?php echo "<input name='firmaAdi' id='firmaAdi' type='text' class='form-control' value='$firmaAdi'>"; ?> 
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': mailErr }">
            <label class="col-sm-2 control-label" for="mail"> Mail: </label>
            <div class="col-sm-5">
                <?php echo "<input name='mail' id='mail' type='text' class='form-control' value='$mail'>"; ?> 
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="sabitTel"> Sabit Tel: </label>
            <div class="col-sm-5">
                <?php echo "<input name='sabitTel' id='sabitTel' type='tel'  maxlength='10' class='form-control' value='$sabitTel'>"; ?> 
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': cepTelErr }">
            <label class="col-sm-2 control-label" for="cepTel"> Cep No: </label>
            <div class="col-sm-5">
                <?php echo "<input name='cepTel' id='cepTel' type='tel' maxlength='10'  placeholder='5__ ___ __ __' class='form-control' value='$cepTel'>"; ?> 
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="vergiDairesi"> Vergi Dairesi: </label>
            <div class="col-sm-5">
                <?php echo "<input name='vergiDairesi' id='vergiDairesi' type='text' class='form-control' value='$vergiDairesi'>"; ?> 
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="vergiNo"> Vergi Numarası: </label>
            <div class="col-sm-5">
                <?php echo "<input name='vergiNo' id='vergiNo' type='text' class='form-control' value='$vergiNo'>"; ?> 
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="adres"> Adres: </label>
            <div class="col-sm-10">
                
                <textarea ref="adres" name="adres" id="adres"  rows="4" cols="50">
                    <?php echo "$adres";  ?>
                </textarea>
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': ilErr }">
            <label class="col-sm-2 control-label" for="il"> İl: </label>
            <div class="col-sm-5">
                <?php $dd->Make($dd->DdIl(),"il","il",null,$ilId,"onChange='ilChange()'");?>
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': ilceErr }">
            <label class="col-sm-2 control-label" for="ilce"> İlçe: </label>
            <div class="col-sm-5">
                <?php 
                if($update=="true")
                    $dd->Make($dd->DdIlce(),"ilce","ilce",null,$ilceId);
                else
                    $dd->Make(array(),"ilce","ilce",null,$ilceId);
                ?>
            </div>
        </div>


        <div class="form-group" >
            <label class="col-sm-2 control-label" for="sorguswdiv"> Paket Sorgu: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch" id="sorguswdiv">
                    <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='sorguSw' value='1' $yetkiSorgu  id='sorguSw'>"; ?> 
                    <label class="onoffswitch-label" for="sorguSw">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group" >
            <label class="col-sm-2 control-label" for="yuklemeswdiv"> Paket Yükleme: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch" id="yuklemeswdiv">
                    <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='yuklemeSw' value='1' $yetkiYukle  id='yuklemeSw'>"; ?> 
                    <label class="onoffswitch-label" for="yuklemeSw">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group" >
            <label class="col-sm-2 control-label" for="faturaswdiv"> Fatura: </label>
            <div class="col-sm-10" style="margin-top:5px;">
                <div class="onoffswitch" id="faturaswdiv">
                    <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='faturaSw' value='1' $yetkiFatura  id='faturaSw'>"; ?> 
                    <label class="onoffswitch-label" for="faturaSw">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group" >
            <label class="col-sm-2 control-label" for="durumswdiv"> Bayi Durum: </label>
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
    </form>
    <?php
    if(isset($isyeriIpler))
    {
        echo "
        <div class='form-group'>
        <div class='col-sm-3'>
            <input name='ipekle' id='IsyeriIpEkle' type='text' class='form-control' >
        </div>
        <div class='col-sm-3'>
            <button  type='button' onClick='isyeriEkle(\"$id\")'  class='btn btn-success'>İsyeri Ip Ekle</button>
        </div>
       
        <div class='col-sm-5 panel panel-danger'  >
        <b>İs yeri ipleri</b>
        <br>";
        foreach($isyeriIpler as $ip)
        {
            echo"
                <div class='row'>
                    <div class='col-sm-3' align='left'>
                        <h5>$ip->ipAdres</h5>
                    </div>
                    <div class='col-sm-7' align='center'></div>
                    <div class='col-sm-2' align='right'>
                        <button class='btn' onClick='Sil(\"$ip->id\")'  style='background-color:red'>sil</button>
                    </div>
                </div>
            ";
        }
        
        
       echo "</div>
        </div>";
    }
        
    ?>
    <?php
    if(isset($sunucuIpler))
    {
        echo "
        <div class='form-group'>
        <div class='col-sm-3'>
            <input name='ipekle' id='SunucuIpEkle' type='text' class='form-control' >
        </div>
        <div class='col-sm-3'>
            <button  type='button' onClick='sunucuEkle(\"$id\")'  class='btn btn-success'>Sunucu Ip Ekle</button>
        </div>
       
        <div class='col-sm-5 panel panel-danger'  >
        <b>Sunucu ipleri</b>
        <br>";
        foreach($sunucuIpler as $ip)
        {
            echo"
                <div class='row'>
                    <div class='col-sm-3' align='left'>
                        <h5>$ip->ipAdres</h5>
                    </div>
                    <div class='col-sm-7' align='center'></div>
                    <div class='col-sm-2' align='right'>
                        <button class='btn' onClick='Sil(\"$ip->id\")'  style='background-color:red'>sil</button>
                    </div>
                </div>
            ";
        }
        
        
       echo "</div>
        </div>";
    }
        
    ?>

    <div class="col-md-12">
        <div class="col-sm-5">
        </div>
        <div class="col-sm-2">
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

function isyeriEkle(kulId)
{
    var ip=document.getElementById("IsyeriIpEkle").value;
    postBody={id:kulId,ipAdres:ip}
    $.ajax({
        type:'post',
        url:"IsyeriIpEkle",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
            {
                window.location.reload(false);
                toastr.success(res.message);
                
            } 
            else
                toastr.error(res.message);
        }
        });
}
function sunucuEkle(kulId)
{
    var ip=document.getElementById("SunucuIpEkle").value;
    postBody={id:kulId,ipAdres:ip}
    $.ajax({
        type:'post',
        url:"SunucuIpEkle",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
            {
                window.location.reload(false);
                toastr.success(res.message);
                
            } 
            else
                toastr.error(res.message);
        }
        });
}
function Sil(ipId)
{
    postBody={id:ipId}
    $.ajax({
        type:'post',
        url:"IpSil",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
            {
                window.location.reload(false);
                toastr.success(res.message);
                
            } 
            else
                toastr.error(res.message);
        }
        });
}
function sendForm(type,id)
{
    
    var url="";
    if(type=="new")
    {
        url="yeniKullanici/admin";
    }
    if(type=="update")
    {
        url="KullaniciGuncelle";
    }

    var validate=this.validateForm(id);
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
function ilChange()
{
   
    var ilId=document.getElementById("il").value;
    document.getElementById("ilce").innerHTML="";
    $.ajax({
        type:'get',
        url:"ilce?id="+ilId,
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
function validateForm(id)
{
    var validate=true;
    var isim=document.getElementById("isim").value;
    var soyad=document.getElementById("soyad").value;
    var takmaAd=document.getElementById("takmaAd").value;
    var yukle=document.getElementById("yuklemeSw").checked;
    var fatura=document.getElementById("faturaSw").checked;
    var aktif=document.getElementById("durumSw").checked;
    var sorgu=document.getElementById("sorguSw").checked;
    var adres = document.getElementById("adres").value;
    var firmaAdi=document.getElementById("firmaAdi").value;
    var cepTel=document.getElementById("cepTel").value;
    var vergiNo=document.getElementById("vergiNo").value;
    var vergiDairesi=document.getElementById("vergiDairesi").value;
    var sabitTel=document.getElementById("sabitTel").value;
    var mail=document.getElementById("mail").value;
    var il=document.getElementById("il").value;
    var ilce=document.getElementById("ilce").value;
    if(aktif)
        aktif=1;
    else
        aktif=0;

    if(sorgu)
        sorgu=1;
    else
        sorgu=0;
    
    if(yukle)
        yukle=1;
    else
        yukle=0;

    if(fatura)
        fatura=1;
    else
        fatura=0;
    var postBody;
    if(isim.length<2 || isim.length>20)
    {
        toastr.error("İsim alanı 2 karakterden uzun 20 karakterden küçük olmalıdır!");
        this.isimErr=true;
        validate=false;
    }
    if(soyad.length<2 || soyad.length>20)
    {
        toastr.error("Soyad alanı 2 karakterden uzun 20 karakterden küçük olmalıdır!");
        this.soyadErr=true;
        validate=false;
    }
    if(firmaAdi.length<4 || firmaAdi.length>40)
    {
        toastr.error("Firma Adi alanı 4 karakterden uzun 40 karakterden küçük olmalıdır!");
        this.firmaAdiErr=true;
        validate=false;
    }
    if(cepTel.length!=10 )
    {
        toastr.error("Cep No alanı başında '0' olmadan '10' haneli telefon numaranızı girmenizi istemektedir!");
        this.cepTelErr=true;
        validate=false;
    }

    if(takmaAd.length!=10 )
    {
        toastr.error("Kullanıcı Adı alanı başında '0' olmadan '10' haneli telefon numaranızı girmenizi istemektedir!");
        this.takmaAdErr=true;
        validate=false;
    }
    
    
    var re = /\S+@\S+\.\S+/;
    var mailValidate= re.test(mail);
    if(!mailValidate)
    {
        console.log(mail);
        console.log(mailValidate);
        toastr.error("Lütfen Uygun bir Mail Adresi Giriniz ! ");
        this.mailErr=true;
        validate=false;
    }
        
    if(il==0)
    {
        toastr.error("Lütfen il seçiniz! ");
        this.ilErr=true;
        validate=false;
    }
    if(ilce==0)
    {
        toastr.error("Lütfen ilçe seçiniz! ");
        this.ilceErr=true;
        validate=false;
    }
    if(id==0)
    {
        var sifre1=document.getElementById("sifre1").value;
        var sifreT=document.getElementById("sifreT").value;
        if(sifre1.length<5 )
        {
            toastr.error("Şifre alanı en az 6 karakter olabilir ! ");
            this.sifreErr=true;
            validate=false;
        }
        if(sifre1 != sifreT )
        {
            toastr.error("Şifreleriniz uyuşmamaktadır ! ");
            this.sifreErr=true;
            this.sifreTErr=true;
            validate=false;
        }
        if(sifre1 != sifreT  )
        {
            toastr.error("Şifreleriniz uyuşmamaktadır ! ");
            this.sifreErr=true;
            this.sifreTErr=true;
            validate=false;
        }
        postBody=
        {
            isim:isim,
            soyad:soyad,
            takmaAd:takmaAd,
            sifre1:sifre1,
            yukle:yukle,
            fatura:fatura,
            sorgu:sorgu,
            firmaAdi:firmaAdi,
            aktif:aktif,
            mail:mail,
            sabitTel:sabitTel,
            vergiDairesi:vergiDairesi,
            vergiNo:vergiNo,
            cepTel:cepTel,
            adres:adres,
            ilce:ilce,
            id:id
        }
    }
    else
    {
        var sifre1=document.getElementById("sifre1").value;
        postBody=
        {
            isim:isim,
            soyad:soyad,
            sifre1:sifre1,
            takmaAd:takmaAd,
            yukle:yukle,
            sorgu:sorgu,
            fatura:fatura,
            firmaAdi:firmaAdi,
            aktif:aktif,
            mail:mail,
            sabitTel:sabitTel,
            vergiDairesi:vergiDairesi,
            vergiNo:vergiNo,
            cepTel:cepTel,
            adres:adres,
            ilce:ilce,
            id:id
        }
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