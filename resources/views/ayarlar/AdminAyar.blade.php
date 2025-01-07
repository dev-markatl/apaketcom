<?php 
use App\Classes\DropDown;

$_SESSION["menu"]=5;
$_SESSION["altmenu"]=1;
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
        <div class="form-group" v-bind:class="{  'has-error has-feedback': takmaAdErr }">
            <label class="col-sm-2 control-label" for="takmaAd"> Kullanıcı Adı: </label>
            <div class="col-sm-5">
                <input value="{{$takmaAd}}" name="takmaAd" id="takmaAd" type="text" class="form-control">
            </div>
        </div>
            <div class="form-group"  v-bind:class="{  'has-error has-feedback': sifreErr }">
            <label class="col-sm-2 control-label" for="msifre"> Mevcut Sifre: </label>
            <div class="col-sm-5">
                <input   name="msifre" id="msifre" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group"  v-bind:class="{  'has-error has-feedback': sifreErr }">
            <label class="col-sm-2 control-label" for="sifre1"> Yeni Sifre: </label>
            <div class="col-sm-5">
                <input   name="sifre1" id="sifre1" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group"   v-bind:class="{  'has-error has-feedback': sifreTErr }">
            <label class="col-sm-2 control-label" for="sifreT">Sifre Tekrar: </label>
            <div class="col-sm-5">
                <input name="sifreT" id="sifreT" type="password" class="form-control">
            </div>
        </div>
        <div class="form-group"   v-bind:class="{  'has-error has-feedback': sifreTErr }">
            <div class="col-sm-2" align="right"><button type="button" class=" btn btn-success" onClick="isyeriEkle('{{$id}}')">Ip Ekle </button></div>
            <div class="col-sm-5">
                <input name="IsyeriIpEkle" id="IsyeriIpEkle" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group "  v-bind:class="{  'has-error has-feedback': isimErr }">
        <div class="col-sm-2"></div>
            <div class="col-sm-5">
                <div class='col-sm-12 panel panel-danger'  >
                    <b>İs yeri ipleri</b>
                    <br>
                    <?php 
                        foreach($isyeriIpler as $ip)
                        {
                            echo"
                            <div class='row'>
                                <div class='col-sm-3' align='left'>
                                    <h5>$ip->ipAdres</h5>
                                </div>
                                <div class='col-sm-7' align='center'></div>
                                <div class='col-sm-2' align='right'>
                                    <button type='button' class='btn' onClick='Sil(\"$ip->id\")'  style='background-color:red'>sil</button>
                                </div>
                            </div>
                        ";
                        }
                    ?>
                    <!-- <div class='row'>
                        <div class='col-sm-3' align='left'>
                            <h5>1222.2.222.2</h5>
                        </div>
                        <div class='col-sm-7' align='center'></div>
                        <div class='col-sm-2' align='right'>
                            <button class='btn' onClick='Sil(\"$ip->id\")'  style='background-color:red'>sil</button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- <div class="form-group "  v-bind:class="{  'has-error has-feedback': isimErr }">
            <label class="col-sm-2 control-label" for="isim"> İsim: </label>
            <div class="col-sm-5">
                <input value="{{$ad}}" disabled name="isim" id="isim" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': soyadErr }">
            <label class="col-sm-2 control-label" for="soyad"> Soyad: </label>
            <div class="col-sm-5">
                <input value="{{$soyAd}}" disabled name="soyad" id="soyad" type="text" class="form-control">
            </div>
        </div>
            
        <div class="form-group" v-bind:class="{  'has-error has-feedback': firmaAdiErr }">
            <label class="col-sm-2 control-label" for="firmaAdi"> Firma Adı: </label>
            <div class="col-sm-5">
                <input value="{{$firmaAdi}}" disabled name="firmaAdi" id="firmaAdi" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': mailErr }">
            <label class="col-sm-2 control-label" for="mail"> Mail: </label>
            <div class="col-sm-5">
                <input value="{{$mail}}" disabled name="mail" id="mail" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="sabitTel"> Sabit Tel: </label>
            <div class="col-sm-5">
                <input value="{{$sabitTel}}" disabled name="sabitTel" id="sabitTel" type="number" class="form-control">
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': cepTelErr }">
            <label class="col-sm-2 control-label" for="cepTel"> Cep No: </label>
            <div class="col-sm-5">
                <input value="{{$cepTel}}" disabled name="cepTel" id="cepTel" type="number" maxlength="10"  placeholder="5__ ___ __ __" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="vergiDairesi"> Vergi Dairesi: </label>
            <div class="col-sm-5">
                <input value="{{$vergiDairesi}}" disabled name="vergiDairesi" id="vergiDairesi" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="vergiNo"> Vergi Numarası: </label>
            <div class="col-sm-5">
                <input value="{{$vergiNo}}"   name="vergiNo" disabled id="vergiNo" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="adres"> Adres: </label>
            <div class="col-sm-10">
                
                <textarea  disabled name="adres" id="adres"  rows="4" cols="50">
                    "{{$adres}}"
                </textarea>
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': ilErr }">
            <label class="col-sm-2 control-label" for="il"> İl: </label>
            <div class="col-sm-5">
                
                <input class="form-control" value="{{$il}}" type="text" disabled value="">
            </div>
        </div>
        <div class="form-group" v-bind:class="{  'has-error has-feedback': ilceErr }">
            <label class="col-sm-2 control-label" for="ilce"> İlçe: </label>
            <div class="col-sm-5">

                <input class="form-control" value="{{$ilce}}" type="text" disabled value="">
            </div>
        </div> -->
    </form>
 
    <div class="col-md-12">
        <div class="col-md-5"></div>
        <div class="col-md-2">
            <button  type="button"  class="btn btn-success" onClick="sendForm('update')" style="background-color: green;">Güncelle</button>
            
        </div>
        
    </div>
        
    
</div>
@endsection

<script>
function Sil(ipId)
{
    postBody={id:ipId}
    $.ajax({
        type:'post',
        url:"ajax/IpSil",
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
function isyeriEkle(kulId)
{
    var ip=document.getElementById("IsyeriIpEkle").value;
    postBody={id:kulId,ipAdres:ip}
    $.ajax({
        type:'post',
        url:"ajax/IsyeriIpEkle",
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
function sendForm(type)
{
    

    
    var url="ajax/AdminAyar/Update";
    

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
    
    var takmaAd=document.getElementById("takmaAd").value;
    var msifre=document.getElementById("msifre").value;


    
    if(takmaAd.length<4 || takmaAd.length>21 )
    {
        toastr.error("Kullanıcı Adı alanı 5 ila 20 karakter aralığında olmalıdır !");
        this.takmaAdErr=true;
        validate=false;
    }
    

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
    var postBody=
    {
        
        takmaAd:takmaAd,
        sifre1:sifre1,
        mSifre:msifre,

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