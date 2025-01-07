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
                                                <form action="yukleyici-giris-login"  method="post">
                                                {{ csrf_field() }}
                                                    <div class="form-group has-feedback">
                                                        <input type="text" class="form-control" name="ceptelNo" id="ceptelNo" maxlength="10"  placeholder="Kullanıcı Adı">
                                                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                                    </div>
                                                    <div class="form-group has-feedback">
                                                        <input type="password" class="form-control" id="sifre" name="sifre" placeholder="Şifre">
                                                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <button type="submit" id="buton" class="btn btn-info btn-block btn-flat" onClick="loginIn">Yukleyici Girişi Yap</button>
                                                        <div style="margin-bottom: 10px; margin-top: 10px;">
                                                       
                                                    </div>
                                                    <div class="social-auth-links text-center">
                                                    <br>
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






