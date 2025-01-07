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
?>
<div>
    <div class="col-md-12" style="text-align:center">
        <h4 id="paketIsmi" class="modal-title">Yeni Kayıt</h4>
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
            <div class="form-group" v-bind:class="{  'has-error has-feedback': tipErr }">
                <label class="col-sm-2 control-label" for="Tip"> Tip: </label>
                <div class="col-sm-10"  id="tipDiv1">
                    <?php $dd->Make($dd->DdTip(),"tip","tip",null,$tip);?>
                </div>
            </div>
            <div class="form-group" v-bind:class="{  'has-error has-feedback': adiErr }">
                <label class="col-sm-2 control-label" for="adi"> Paket Adı: </label>
                <div class="col-sm-5">
                    <?php echo "<input name='adi' id='adi' type='text' class='form-control' value='$adi'>"; ?>  
                </div>
            </div>
            <div class="form-group" v-bind:class="{  'has-error has-feedback': koduErr }">
                <label class="col-sm-2 control-label" for="kodu">Paket Kodu: </label>
                <div class="col-sm-5">
                    <?php echo "<input name='kodu' id='kodu' type='text' class='form-control' value='$kod'>"; ?>  
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="kodu">Kategori No: </label>
                <div class="col-sm-5">
                    <?php echo "<input name='kategoriNo' id='kategoriNo' type='number' class='form-control' value='$kategoriNo'>"; ?>  
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="kodu">Kategori Adı: </label>
                <div class="col-sm-5">
                    <?php echo "<input name='kategoriAdi' id='kategoriAdi' type='text' class='form-control' value='$kategoriAdi'>"; ?>  
                </div>
            </div>
            <div class="form-group" >
                <label class="col-sm-2 control-label" for="kodu">Sıra No: </label>
                <div class="col-sm-5">
                    <?php echo "<input name='siraNo' id='siraNo' type='text' class='form-control' value='$siraNo'>"; ?>  
                </div>
            </div>
            <div class="form-group" >
                <label class="col-sm-2 control-label" for="kodu">Alternatif Kodlar (Virgülle ayırın): </label>
                <div class="col-sm-5">
                    <?php echo "<input name='alternatifKodlar' id='alternatifKodlar' type='text' class='form-control' value='$alternatifKodlar'>"; ?>  
                </div>
            </div>
            <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableModal" >
                <tbody>
                    <tr style="color:white; background-color:#0b3779">
                        <td style="width:40px;" align="center">Gün</td>
                        <td style="width:60px;" align="center">HerYöne <br>Konuşma</td>
                        <td style="width:60px;" align="center">Şebeke içi<br>Konuşma</td>
                        <td style="width:60px;" align="center">HerYöne<br>Sms</td>
                        <td style="width:60px" align="center">Şebeke içi<br>Sms</td>
                        <td style="width:60px" align="center">İnternet</td>
                        <td style="width:60px;" align="center">R.Satış<br>Fiyatı</td>
                        <td style="width:60px;" align="center">Maliyet<br>Fiyatı</td>
                        <td style="width:100px;" align="center">Sorguya<br> Ekle</td>
                        <td style="width:100px;" align="center">Durum</td>
                    </tr>
                    <tr>
                        <td align="center">
                            <?php echo "<input name='gun' id='gun' type='number' maxlength='3' class='form-control' value='$gun'>"; ?>  
                        </td>
                        <td align="center">
                            <?php echo "<input name='hyk' id='hyk' type='number' maxlength='5' class='form-control' value='$hyk'>"; ?>  
                        </td>
                        <td align="center">
                            <?php echo "<input name='sik' id='sik' type='number' maxlength='5' class='form-control' value='$sik'>"; ?>  
                        </td>
                        <td align="center">
                            <?php echo "<input name='hys' id='hys' type='number' maxlength='5' class='form-control' value='$hys'>"; ?>  
                        </td>
                        <td align="center">
                            <?php echo "<input name='sis' id='sis' type='number' maxlength='5' class='form-control' value='$sis'>"; ?>  
                        </td>
                        <td align="center">
                            <?php echo "<input name='int' id='int' type='number' maxlength='5' class='form-control' value='$int'>"; ?>  
                        </td>
                        <td align="center">
                            <?php echo "<input name='rsf' id='rsf' type='number' maxlength='5' class='form-control' value='$rsf'>"; ?>  
                        </td>
                        <td align="center">
                            <?php echo "<input name='mf' id='mf' type='number' maxlength='5' class='form-control' value='$mf'>"; ?>  
                        </td>
                        <td align="center">
                            <div class="onoffswitch" id="a">
                                <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='sorguyaEkleSw' value='1' $sorgu  id='sorguyaEkleSw'>"; ?>    
                                <label class="onoffswitch-label" for="sorguyaEkleSw">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                        <td align="center">
                            <div class="onoffswitch" id="b">
                                <?php echo "<input type='checkbox' class='onoffswitch-checkbox' name='aktifSw' value='2' $aktif  id='aktifSw'>"; ?>    
                                <label class="onoffswitch-label" for="aktifSw">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
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
        url:"YeniPaketEkle",
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
function updateForm(id)
{
    

    var validate=formValidate(id);
    if(validate!=false)
    {
        $.ajax({
        type:'post',
        url:"PaketOzellikleri/Guncelle",
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
    var operator=window.$("#operatorDiv1").children("#operator")[0].value;
    var tip=window.$("#tipDiv1").children("#tip")[0].value;
    var adi= window.$("#adi")[0].value;
    var kodu= window.$("#kodu")[0].value;
    var kategoriNo= window.$("#kategoriNo")[0].value;
    var kategoriAdi= window.$("#kategoriAdi")[0].value;
    var siraNo= window.$("#siraNo")[0].value;
    var alternatifKodlar= window.$("#alternatifKodlar")[0].value;
    
    var gun= window.$("#gun")[0].value;
    var hys= window.$("#hys")[0].value;
    var hyk= window.$("#hyk")[0].value;
    var sik= window.$("#sik")[0].value;
    var sis= window.$("#sis")[0].value;
    var int= window.$("#int")[0].value;
    var rsf= window.$("#rsf")[0].value;
    var mf= window.$("#mf")[0].value;
    var sorgu= window.$("#sorguyaEkleSw")[0].checked;
    var aktif =  window.$("#aktifSw")[0].checked;
    var validate=true;
    var id=id;

    var postBody=
    {
        operator:operator,
        tip:tip,
        adi:adi,
        kodu:kodu,
        gun:gun,
        hys:hys,
        hyk:hyk,
        sik:sik,
        sis:sis,
        int:int,
        rsf:rsf,
        mf:mf,
        sorgu:sorgu,
        aktif:aktif,
        kategoriAdi:kategoriAdi,
        kategoriNo:kategoriNo,
        id:id,
        siraNo:siraNo,
        alternatifKodlar:alternatifKodlar
    }

    if(adi.length<2 || adi.length>101 )
    {
        toastr.error("Paket Adı alanı 3 ila 100 karakter aralığında olmalıdır !");
        this.adiErr=true;
        validate=false;
    }
    if(kodu.length<1 || kodu.length>41 )
    {
        toastr.error("Paket Kodu alanı boş geçilemez !");
        this.koduErr=true;
        validate=false;
    }
    if(gun.length<1 || gun.length>41 )
    {
        toastr.error("Gün alanı boş geçilemez  !");
        this.gunErr=true;
        validate=false;
    }
    if(hys.length<1 || hys.length>41 )
    {
        toastr.error("Her Yöne SES alanı boş geçilemez  !");
        validate=false;
    }
    if(hyk.length<1 || hyk.length>41 )
    {
        toastr.error("Her Yöne Konusma alanı boş geçilemez  !");     
        validate=false;
    }
    if(sik.length<1 || sik.length>41 )
    {
        toastr.error("Şebeke içi Konuşma alanı boş geçilemez  !");     
        validate=false;
    }
    if(sis.length<1 || sis.length>41 )
    {
        toastr.error("Şebeke içi SMS alanı boş geçilemez  !");     
        validate=false;
    }
    if(int.length<1 || int.length>41 )
    {
        toastr.error("İnternet alanı boş geçilemez  !");     
        validate=false;
    }
    if(rsf.length<1 || rsf.length>41 )
    {
        toastr.error("Resmi Satış Fiyatı alanı boş geçilemez ve Virgül ile Girilmelidir !");     
        validate=false;
    }
    if(mf.length<1 || mf.length>41 )
    {
        toastr.error("Maliyet  Fiyatı alanı boş geçilemez ve Virgül ile Girilmelidir  !");     
        validate=false;
    }
    if(operator==-1)
    {
        toastr.error("Lütfen Operator seçiniz   !");  
        this.operatorErr=true;
        validate=false;  
    }
    if(tip==-1)
    {
        toastr.error("Lütfen Tip seçiniz   !");    
        this.tipErr=true;
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