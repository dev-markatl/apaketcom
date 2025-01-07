$(document).on("wheel", "input[type=number]", function (e) {
    $(this).blur();
});
function UpdateOzelAktif(id)
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
        url:"ajax/OzelPaketListesi/UpdateAktif",
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

function Temizle()
{
    var grupno = document.getElementById("grupno");
    var grupNo = grupno.value;
    window.location.href = "kontor-grupduzenle/temizle?i="+grupNo; 
}

function UpdateOzelSorgu(id)
{

    var checked=document.getElementById("sw_"+id).checked;
    console.log(checked)
    if(checked)
        checked="1";
    else
        checked="0";



    var postBody={
    id:id,
    sorguya_ekle:checked
    }


    $.ajax({
        type:'post',
        url:"ajax/OzelPaketListesi/UpdateSorgu",
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

function UpdateOzelResmiFiyat(id)
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
        url:"ajax/OzelPaketListesi/UpdateFiyat",
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

function UpdateOzelMaliyetFiyat(id)
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
        url:"ajax/OzelPaketListesi/UpdateFiyat",
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
function OzelFiyatDurumKaydet()
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
        url:"ajax/OzelPaketListesi/UpdateTopluDurum",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
            {
                toastr.success(res.message);
                if (DurumKaydet[0].value == 2)
                {
                  location.reload();
                }
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

function OtoFiyatDuzenle()
{

  var carpan = document.getElementById("carpan")
  var grupno = document.getElementById("grupno")
  var fiyatCarpani = carpan.value;
  var grupNo = grupno.value;


  var postBody={
    fiyatCarpani:fiyatCarpani,
    grupNo:grupNo
  }

  console.log("fiyatCarpani:"+fiyatCarpani+"  :"+carpan.value);

  $.ajax({
      type:'post',
      url:"ajax/OzelPaketListesi/OtoFiyatDuzenle",
      data:postBody,
      success:function(res)
      {
          if(res.status=="true")
            {
              toastr.success(res.message);
              location.reload();
            }
          else
              toastr.error(res.message);
      }
   });


}
