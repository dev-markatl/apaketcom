
function ac(gelen2)
{

  if(document.getElementById('ac1_'+gelen2).value=="ac")
  {
    $("#ac_"+gelen2).slideDown("slow");
    //$("#ac4_"+gelen2).slideDown("slow");
    
    document.getElementById('ac1_'+gelen2).value="kapa";
  }
  else
  {
    $("#ac_"+gelen2).slideUp("slow");
    //$("#ac4_"+gelen2).slideUp("slow");
 
      document.getElementById('ac1_'+gelen2).value="ac";
  }
}

function apiden()
{
var kod_secme = document.getElementById("kod_secme");
  if(document.getElementById('islem_turu').value=="apiden")
  {
    kod_secme.style.display = "inline";
  }
  else
  {
   kod_secme.style.display = "none";
  }
}

function odeme(formismi,hangi)
{
   document.getElementById("hangisi_"+formismi).value=hangi;
    //Verileri gönderme işlemi
    var frmisim="#frm_"+formismi;
    alert("hey");
    $.ajax({
        type:'POST',
        url:'onayveiptal',
        data: $(frmisim).serialize(),
        //data:$('#formismi').serialize(),
        success: function (msg) {
            //Dönen sonucu ekranda gösterme
            $('#yaz').html(msg);
        }
    });

  }
