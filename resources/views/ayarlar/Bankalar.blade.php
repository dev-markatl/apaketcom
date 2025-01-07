<?php 
use App\Classes\DropDown;

$_SESSION["menu"]=5;
$_SESSION["altmenu"]=2;
$dd=new DropDown ;


?>
@extends('MasterPage')
@section('content')
<div >
    
    
        <div class="form-group col-md-12"  >
            <input type="button"  onClick="ModalClick(0)"
            style="margin-left:910px;"
            class="btn btn-info btnYeni"  
            id="sorgula" name="sorgula" 
                
            value="Yeni Banka Ekle">      
        </div>
        

    
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableBankalar" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                
                <td style="width:190px;" align="center">Banka Adı / Şube Adı</td>
                <td style="width:100px;" align="center">Şube Kodu<br>Hesap No</td>
                <td style="width:230px" align="center">İban Numarası</td>
                <td style="width:230px" align="center">Hesap Sahibi</td>
                <td style="width:75px;" align="center">Banka<br> Durum</td>
                
                
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
                    <td style='padding:3px;' colspan='5'> </td>
                </tr>
                
                <tr id='tr_$banka->id' v-bind:class='{'hataBG':!$banka->aktif}'>
                    
                    <td  align='center' onClick='ModalClick($banka->id)'>$banka->bankaAdi<br> $banka->subeAdi </td>
                    <td align='center'>$banka->subeKodu <br> $banka->hesapNo </td>
                    <td  align='center'>$banka->ibanNo</td>
                    <td  align='center'>$banka->hesapSahibi</td>
                    <td  align='center'>
                        <div class='onoffswitch' id='dva_$banka->id'>
                            <input type='checkbox' value='$banka->id' class='onoffswitch-checkbox' 
                            onClick='UpdateSw($banka->id)' id='swa_$banka->id' $aktif >
                            <label class='onoffswitch-label' for='swa_$banka->id'>
                                <span class='onoffswitch-inner'></span>
                                <span class='onoffswitch-switch'></span>
                            </label>
                        </div>
                    </td>
                </tr>";
        }
            
        ?>
           
        
    </table>


</div>

@endsection
<script>
function ModalClick( id)
{
    var width=800;
    var height=580;
    var url="ajax/BankaOzellikleri?";
    window.open(url+"id="+id, "aa", "height="+height+",width="+width);
}
function UpdateSw(id)
{
   
    var checked=document.getElementById("swa_"+id).checked;
    

    if(checked)
    {
        checked="1";
        window.$("#tr_"+id)[0].className="";
        
    }
    else
    {
        checked="0";
        window.$("#tr_"+id)[0].className="hataBg";
    }
        
        


   
    var postBody =
    {
        id:id,
        status:checked
    }
    $.ajax({
        type:'post',
        url:"ajax/Bankalar/UpdateSw",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
                toastr.success(res.message);
            else
                toastr.error(res.message);
        }
    });
    
}
</script>