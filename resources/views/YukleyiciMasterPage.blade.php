<?php
  use Illuminate\Support\Facades\Auth;
  $title=$_SERVER['HTTP_HOST'];
  $title=str_replace("www.","",$title);
  $adi=Auth::guard("RobotAuth")->user()->adi;
?>

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

    <title><?php echo $title;?></title>
</head>
      
<!-- <div id="overlay" style="z-index:3;background: rgba(0,0,0,0.8);width:100%;height: 100%;"><div style="z-index:4;width:100%;" id="overlay"></div></div> -->

<body style="align:center;">
<div class="container">

<div class="col-xs-12">
<div class="panel panel-primary" style="  border-color:#186ef1; ">
      <div class="panel-heading" style="text-align:left;border-color:#186ef1; background-color:#186ef1;"><asd style="text-align:left;" ><img style="width:18px;margin-top:-2px; margin-right:5px;" src="public/img/bayi.png" />{{$adi}}</asd></div>
      <div class="panel-body" style=" border-color:#186ef1; min-height:200px;">
      @yield('content')
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