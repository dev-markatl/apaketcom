function SelectAll()
{
    var checkboxes=$("[name='cbx']");
    if(document.getElementById("cb_tum").checked)
    {
        for(var i=0, n=checkboxes.length; i<n; i++) 
        {
            checkboxes[i].checked=true;
        }
    }
    else
    {
        for(var i=0, n=checkboxes.length; i<n; i++) 
        {
            checkboxes[i].checked=false;
        }
    }
    
}
            
function  CbClicked(id)
{
    var checked=document.getElementById("cb_"+id).checked;
    var checkboxes=$("[name='cbx']");
    var kutu='';
    console.log(checked);
    for(var i=0, n=checkboxes.length; i<n; i++) 
    {
        if (checkboxes[i].checked)
        {
            kutu += "var";
            break;
        }
    }

    if(kutu=="var")
    {
        window.$("#divKaydet").show(600);
    }
    else
    {
        window.$("#divKaydet").hide(600);
    }
}
function  PaketDurumKaydet()
{
    var SelectedCb=[];
    console.log("aa");
    var checkboxes=window.$("[name='cbx']");
   
    for(var i=0, n=checkboxes.length; i<n; i++) 
    {
        
        if(checkboxes[i].checked)
        {
            SelectedCb.push(checkboxes[i].value);
        }
    }
    var DurumKaydet=window.$("#durumKaydet");
    var postBody={
        Cb:SelectedCb,
        Durum:DurumKaydet[0].value
    }

    $.ajax({
        type:'post',
        url:"ajax/YeniPaketler/TopluDurum",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
            {
                toastr.success(res.message);
                
                this.SelectedCb=[];
            }
            else
            {
                toastr.error(res.message);
            }
        }
     });
    
}