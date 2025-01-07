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
<div >
    

    
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableBankalar" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                
                <td style="width:190px;" align="center">Banka Adı / Şube Adı</td>
                <td style="width:100px;" align="center">Şube Kodu<br>Hesap No</td>
                <td style="width:230px" align="center">İban Numarası</td>
                <td style="width:230px" align="center">Hesap Sahibi</td>
                
                
                </tr>
        </tbody>
        <?php
        foreach ($bankalar as $banka )
        {
            $aktif="";
            if($banka->aktif)
                $aktif="checked";
            else
                $aktif="";

            echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='4'> </td>
                </tr>
                
                <tr id='tr_$banka->id' v-bind:class='{'hataBG':!$banka->aktif}'>
                    
                    <td  align='center' onClick='ModalClick($banka->id)'>$banka->bankaAdi<br> $banka->subeAdi </td>
                    <td align='center'>$banka->subeKodu <br> $banka->hesapNo </td>
                    <td  align='center'>$banka->ibanNo</td>
                    <td  align='center'>$banka->hesapSahibi</td>

                </tr>";
        }
            
        ?>
           
        
    </table>


</div>
