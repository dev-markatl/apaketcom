<?php 
use App\Classes\DropDown;

$_SESSION["menu"]=5;
$_SESSION["altmenu"]=3;
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
                
            value="Yeni Numara Ekle">      
        </div>
        

    
    <table align="center" border="1" bordercolor="lightblue" cellspacing="100" class="tableBankalar" >
        <tbody>
            <tr style="color:white; background-color:#0b3779">
                <td style="width:600px;" align="center">Telefon<br>Numarası</td>
                <td style="width:150px;" align="center">Sorgu<br>Blok</td>
                <td style="width:150px" align="center">Yükleme<br>Blok</td>
                <td style="width:150px" align="center">Sil</td>

                </tr>
        </tbody>
        <?php
        foreach ($numaralar as $numara)
        {
            $aktif="";
            if($numara->sorgu_blok)
                $sorgu="checked";
            else
                $sorgu="";

            if($numara->yukleme_blok)
                $yukleme="checked";
            else
                $yukleme="";

            echo "
                <tr  style='border-left:hidden; border-right:hidden;'>
                    <td style='padding:3px;' colspan='5'> </td>
                </tr>
                
               
                    
                    <td style='font-size:16px;'  align='center'>$numara->telefon</td>

                    <td  align='center'>
                        <div class='onoffswitch' id='dva_$numara->id'>
                            <input type='checkbox' value='$numara->id' class='onoffswitch-checkbox' 
                            onClick='UpdateSw($numara->id)' id='swa_$numara->id' $sorgu >
                            <label class='onoffswitch-label' for='swa_$numara->id'>
                                <span class='onoffswitch-inner'></span>
                                <span class='onoffswitch-switch'></span>
                            </label>
                        </div>
                    </td>


                    <td  align='center'>
                    <div class='onoffswitch' id='dvy_$numara->id'>
                        <input type='checkbox' value='$numara->id' class='onoffswitch-checkbox' 
                        onClick='UpdateYuklemeSw($numara->id)' id='swy_$numara->id' $yukleme >
                        <label class='onoffswitch-label' for='swy_$numara->id'>
                            <span class='onoffswitch-inner'></span>
                            <span class='onoffswitch-switch'></span>
                        </label>
                    </div>
                    </td>

                    <td align='center'>
                    <button onclick='NumaraSil($numara->id)' class='btn btn-danger btnSil' id='sil' name='sil' style='margin-left: 15px;'>Sil</button>
                    </td>

                </tr>";
        }
            
        ?>
           
        
    </table>


</div>

@endsection
<script>

function NumaraSil(id)
{
    var postBody={
      id:id,
      }
      $.ajax({
          type:'post',
          url:"ajax/KaraListe/DeleteSw",
          data:postBody,
          success:function(res)
          {
              if(res.status=="true")
              {
                toastr.success(res.message);
                location.reload(true);
              }   
              else
                  toastr.error(res.message);
          }
      });

}


function ModalClick(id)
{
    var width=800;
    var height=580;
    var url="ajax/KaraListeOzellikleri?";
    window.open(url+"id="+id, "aa", "height="+height+",width="+width);
}
function UpdateSw(id)
{
   
    var checked=document.getElementById("swa_"+id).checked;
    

    if(checked)
    {
        checked="1";
        
    }
    else
    {
        checked="0";
    }
        
        


   
    var postBody =
    {
        id:id,
        status:checked
    }
    $.ajax({
        type:'post',
        url:"ajax/KaraListe/UpdateSw",
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

function UpdateYuklemeSw(id)
{
   
    var checked=document.getElementById("swy_"+id).checked;
    

    if(checked)
    {
        checked="1";
        
    }
    else
    {
        checked="0";
        
    }
        
        


   
    var postBody =
    {
        id:id,
        status:checked
    }
    $.ajax({
        type:'post',
        url:"ajax/KaraListe/UpdateYuklemeSw",
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