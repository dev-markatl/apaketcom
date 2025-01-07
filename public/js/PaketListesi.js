$(document).on("wheel", "input[type=number]", function (e) {
    $(this).blur();
});
function UpdateAktif(id)
{
    
    var checked=document.getElementById("swi_"+id).checked;
    console.log(checked);
    if(checked)
        checked="1";
    else
        checked="0";
    
    
    var postBody={
    id:id,
    aktif:checked
    }
    $.ajax({
        type:'post',
        url:"ajax/PaketListesi/UpdateProperty",
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
function UpdateSorgu(id)
{
    
    var checked=document.getElementById("sw_"+id).checked;
    console.log(checked)
    if(checked)
        checked="1";
    else
        checked="0";
    
 
    
    var postBody={
    id:id,
    sorguyaEkle:checked
    }
    
   
    $.ajax({
        type:'post',
        url:"ajax/PaketListesi/UpdateProperty",
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
function UpdateResmiFiyat(id)
{
    var cur = document.getElementById("fiyat0_"+id)
    var curmaliyet = document.getElementById("fiyat1_"+id)
    
    var curVal=cur.value;
    var curId=cur.name;
    
    var postBody={
    id:curId,
    resmiSatisFiyati:curVal,
    maliyetFiyati:curmaliyet.value
    }
    
    console.log("resmi:"+curVal+"  :"+curmaliyet.value);
    
    $.ajax({
        type:'post',
        url:"ajax/PaketListesi/Update",
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
function UpdateMaliyetFiyat(id)
{
    var cur = document.getElementById("fiyat1_"+id)
    var curResmi = document.getElementById("fiyat0_"+id)
    var curVal=cur.value;
    var curId=cur.name;
    
    
    var postBody={
    id:curId,
    maliyetFiyati:curVal,
    resmiSatisFiyati:curResmi.value,
    }
    
    console.log("maliyet:"+curVal+"  :"+curResmi.value);
    
    $.ajax({
        type:'post',
        url:"ajax/PaketListesi/Update",
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
function Temizle()
{
    window.location.href = "kontor-paketlistesi/temizle"; 
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
        url:"ajax/PaketListesi/TopluDurum",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
            {
                toastr.success(res.message);
                for(var i = 0; i<SelectedCb.length;i++)
                {
                   
                    if(DurumKaydet[0].value == 1)
                        window.$("#swi_"+SelectedCb[i])[0].checked=true;
                    else
                        window.$("#swi_"+SelectedCb[i])[0].checked=false;
                }
                this.SelectedCb=[];
            }
            else
            {
                toastr.error(res.message);
            }
        }
     });
    
}
function paketDuzenle(id)
{
    window.open("ajax/PaketOzellikleri?id="+id, "aa", "height=500,width=800");
}
function exiptal()
{
    $.ajax({
        type:'get',
        url:"ajax/PaketListesi/exiptal",
        data:null,
        success:function(res)
        {
            if(res.status=="true")
                toastr.success(res.message);
            else
                toastr.error(res.message);
        }
     });
}
