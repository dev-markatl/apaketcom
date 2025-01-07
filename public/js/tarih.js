
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