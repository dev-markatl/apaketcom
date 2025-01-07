<?php
  use Illuminate\Support\Facades\Auth;
  $title=$_SERVER['HTTP_HOST'];
  $title=str_replace("www.","",$title);
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
  <div  style='width: 1250px; margin-left:auto;  margin-right:auto;'>
    <div class="" style="width: 1250px; margin-left:1px;  margin-right:;">
	    <div class="" style="align:center;">
<?php
 
  $id =1;
  $vodafone = 1;
  $avea = 1;
  $bakiye=1;
  $ust_menu_liste=array("Anasayfa","Kontor","ÖdemeHareketleri","Ayarlar","Cikis");
  $anasayfa=array("anasayfa-Duyurular");
  $kontor=array("kontor-Yukleme_Takip","kontor-Paket_Listesi");
  $fatura=array("fatura-Fatura_Takip");
  $odemeHareketleri=array("odemehareketleri-Hesap_Hareketleri");
  $ayarlar=array("ayarlar-Bayi_Ayarlari");
  $bakiye=Auth::user()->bakiye;
  $bakiye=number_format( $bakiye, 3, '.', '');
  $menu=$_SESSION["menu"];
  $stil="    outline:none;
  background-color:#0b3779;";
  $renk1=NULL;$renk2=NULL;$renk3=NULL;$renk4=NULL;$renk5=NULL;$renk6=NULL;$renk7=NULL;$renk8=NULL;$renk9=NULL;$renk10=NULL;
  switch ($menu) 
  {
    case '1':
      $renk1=$stil;
      $menu_adi=$anasayfa;
    break;
    case '2':
      $renk2=$stil;
      $menu_adi=$kontor;
    break;
    case '3':
      $renk3=$stil;
      $menu_adi=$odemeHareketleri;
    break;
    case '4':
      $renk4=$stil;
      $menu_adi=$ayarlar;
    break;
    case '5':
      $renk5=$stil;
      $menu_adi=$fatura;
    break;

  }
  $disable="";
  if($avea==0 && $vodafone==0)
  {
    $disable="display:none";
  }
  echo' 
  <a class="btn menubb deselectMain" href="bayi-anasayfa-duyurular" style="'.$renk1.' margin-left:-4.5px; width:90px;" >
  <span class="menubsa"  style=" "><img style="width:18px; margin-top:-10px; margin-left:-8px;" src="public/img/anasayfa.png" /></span>
  <p class="menubya" >&nbsp;Anasayfa</p>
  </a>

  <a class="btn menubb deselectMain" href="bayi-kontor-yuklemetakip" style="'.$renk2.'width:75px;" >
  <span class="menubsa"  style=" "><img style="width:18px;margin-top:-10px; margin-left:-6px;" src="public/img/kontor.png" /></span>
  <p class="menubya" > &nbsp;Kontor</p>
  </a>';
  echo'
  <a class="btn menubb deselectMain" href="bayi-fatura-faturatakip" style="'.$renk5.'width:80px;" >
  <span class="menubsa"  style=" "><img style="width:18px;margin-top:-10px; margin-left:-6px;" src="public/img/ayarlar.png" /></span>
  <p class="menubya" > &nbsp;Fatura</p>
  </a>';
  echo'
  <a class="btn menubb deselectMain" href="bayi-odemehareketleri-hesaphareketleri" style="'.$renk3.'width:140px;" >
  <span class="menubsa"  style=" "><img style="width:18px;margin-top:-10px; margin-left:-6px;" src="public/img/raporlar.png" /></span>
  <p class="menubya" > &nbsp;Ödeme Hareketleri</p>
  </a>';

  echo'
  <a class="btn menubb deselectMain" href="bayi-ayarlar-bayiayarlari" style="'.$renk6.'width:80px;" >
  <span class="menubsa"  style=" "><img style="width:18px;margin-top:-10px; margin-left:-6px;" src="public/img/ayarlar.png" /></span>
  <p class="menubya" > &nbsp;Ayarlar</p>
  </a>';

  echo'
  <a class="btn menubb deselectMain" href="cikis" style="'.$renk7.'width:72px;" >
  <span class="menubsa"  style=" "><img style="width:18px;margin-top:-10px; margin-left:-6px;" src="public/img/cikis.png" /></span>
  <p class="menubya" > &nbsp;Cikis</p>
  </a>
  <div style="float:right; margin-top:10px; text-align:right; font-height:12px; color:#0b3779;">
    <b>Bakiye:<b> <strong> '.$bakiye.'<strong>
  </div>

  ';

?>
    <br>
  	<div style="float:left; margin-left:7px;" >
  	<?php
  	//altmenu bastir

    $altmenu=$_SESSION["altmenu"];
    $style="";
    $sayac=0;
  	foreach ($menu_adi as $key ) 
  	{
      $sayac++;
      $href="bayi-".str_replace("İ","i",str_replace("_","",strtolower($key)));
  		$dosya_yolu=str_replace("İ","i",str_replace("_","",strtolower($key)));
  		$resim_yolu="img/".str_replace("-","/",$dosya_yolu).".png";
  		$buton_adi=str_replace("_"," ",substr($key,strpos($key,"-")+1,strlen($key)+1));
  		if($sayac== $altmenu)
      {
        $style="outline:none;background-color: #0c6265;";

      }
      else
      {
        $style="";

      }


      
  		echo'
			  	<a href="'.$href.'" class="btn altmenubb deselectSub" style="'.$style.'" >
			    <span class="menubs"  style=" "><img style="width:18px; margin-top:-10px; margin-left:-8px;" src="public/'.$resim_yolu.'" /></span>
			    <p class="menuby" style="width:auto;" >&nbsp;'.$buton_adi.'&nbsp;&nbsp;</p>
			    </a>
			';
  	}
  	?>
  	</div>

  	
	<div  style=" margin-left:2%; margin-top:40px; min-height:440px; ">
            <div class="panel-body"> 
<?php
  $sayac=0;
  foreach ($menu_adi as $key ) 
  {
    $sayac++;
    $href="bayi-".str_replace("İ","i",str_replace(array("_"," ","  "),"",strtolower($key)));
    $dosya_yolu=str_replace("İ","i",str_replace(array("_"," ","  "),"",strtolower($key)));
    $resim_yolu="img/".str_replace("-","/",$dosya_yolu).".png";
    $buton_adi=str_replace("_"," ",substr($key,strpos($key,"-")+1,strlen($key)+1));
    if($sayac== $altmenu)
        echo'<div class="panel panel-primary" style="  border-color:#186ef1; width:1250px; margin-left:-38px;">
      <div class="panel-heading" style="text-align:left;border-color:#186ef1; background-color:#186ef1;"><asd style="text-align:left;" ><img style="width:18px;margin-top:-2px; margin-right:5px;" src="public/'.$resim_yolu.'" />'.$buton_adi.'</asd></div>
      <div class="panel-body" style=" border-color:#186ef1; min-height:200px;">
    ';
    else
        $style="";
  }

//altmenusu yoksa
  if($menu_adi==null)
  {
    $adi=$ust_menu_liste[$_SESSION["menu"]-1];
    $resim_yolu="img/".str_replace(" ", "", $adi).".png";
    echo'<div class="panel panel-primary" style="  border-color:#186ef1; width:1025px; margin-left:-38px;">
        <div class="panel-heading" style="text-align:left;border-color:#186ef1; background-color:#186ef1;"><asd style="text-align:left;" ><img style="width:18px;margin-top:-2px; margin-right:5px;" src="public/'.$resim_yolu.'" />'.$adi.'</asd></div>
        <div class="panel-body" style=" border-color:#186ef1; min-height:200px;">
      ';
  }
?>



	@yield('content')
  </div>
</div>
 </div>
        </div>
    </div>
    </div>
    </body>
</html>