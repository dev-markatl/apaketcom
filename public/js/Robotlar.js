function ModalClick(type,id)
{
    var width=0;
    var height=0;
    var url="";
    switch (type) {
        case 'guncelle':
            url="ajax/robotOzellikleri?";

            width=800;
            height=580;
            break;
        case 'yeni':
            url="ajax/robotOzellikleri?";

            width=800;
            height=580;
            break;
        case 'ekle':
            url="ajax/RobotPara?cikar=false&";

            width=500;
            height=380;
            break;
        case 'cikar':
            url="ajax/RobotPara?cikar=true&";

            width=500;
            height=380;
            break;
        case 'hesap':
            url="ajax/RobotHesapHareketleri?sayfa=1&";

            width=1080;
            height=530;
            break;

        default:
            break;
    }
    window.open(url+"id="+id, "aa", "height="+height+",width="+width);
}
function UpdateRobotSw(fullid,type,id)
{

    var checked=document.getElementById(fullid).checked;


    if(checked)
    {
        checked="1";

        if(fullid=="swa_"+id)
            window.$("#tr_"+id)[0].className="";

    }
    else
    {
        checked="0";
        if(fullid=="swa_"+id)
            window.$("#tr_"+id)[0].className="hataBg";
    }





    var postBody={
    id:id,
    status:checked,
    type:type
    }
    $.ajax({
        type:'post',
        url:"ajax/RobotHesap/UpdateSw",
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

function UpdateSureSinir(id)
{

      var sureSinir = document.getElementById("suresinir_"+id).value;


      var postBody={
      id:id,
      value:sureSinir,
      }
      $.ajax({
          type:'post',
          url:"ajax/RobotHesap/UpdateSureSinir",
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
function  Filtrele()
{

}
function Temizle()
{
    window.location.href = "robotlistesi-robotlar/temizle";
}
function RobotIslem()
{
    var SelectedCb=[];
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
    console.log(postBody);
    $.ajax({
        type:'get',
        url:"ajax/Robot/TopluDurum",
        data:postBody,
        success:function(res)
        {
            if(res.status=="true")
            {
                toastr.success(res.message);
                for(var i = 0; i<SelectedCb.length;i++)
                {

                    if(DurumKaydet[0].value != 1)
                    {
                        window.$("#swa_"+SelectedCb[i])[0].checked=false;
                        window.$("#tr_"+SelectedCb[i])[0].className="hataBg";
                    }
                    else
                    {

                        window.$("#swa_"+SelectedCb[i])[0].checked=true;
                        window.$("#tr_"+SelectedCb[i])[0].className="";
                    }

                }
            }
            else
            {
                toastr.error(res.message);
            }
            SelectedCb=[];
        }
     });

}

function UpdateOlumsuzSorguTekrar()
{

  var checked=document.getElementById("sws_olumsuzTekrar").checked;


  if(checked)
  {
      checked="1";
  }
  else
  {
      checked="0";
  }

  var postBody={
  //id:id,
  status:checked,
  type:1
  }
  $.ajax({
      type:'post',
      url:"ajax/RobotHesap/UpdateOlumsuzSorguTekrar",
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


function UpdateSistemiKapat(gncKod)
{


  if(gncKod == "1")
  {
    var checked=document.getElementById("sws_sistemiKapatGNC").checked;

      type = "gnc";
  }
  else
  {
    var checked=document.getElementById("sws_sistemiKapat").checked;

      type = "normal";
  }

  if(checked)
  {
      checked="1";
  }
  else
  {
      checked="0";
  }

  var postBody=
  {
  status:checked,
  type:type
  }

  $.ajax({
      type:'post',
      url:"ajax/RobotHesap/SistemiKapat",
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


function UpdateSistemiKapatYukleme(gncKod)
{


  if(gncKod == "1")
  {
    var checked=document.getElementById("sws_sistemiKapatYuklemeGNC").checked;
      type = "gnc";
  }
  else
  {
        var checked=document.getElementById("sws_sistemiKapatYukleme").checked;
      type = "normal";
  }

  if(checked)
  {
      checked="1";
  }
  else
  {
      checked="0";
  }

  var postBody=
  {
  status:checked,
  type:type
  }

  $.ajax({
      type:'post',
      url:"ajax/RobotHesap/SistemiKapatYukleme",
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


function UpdateSureliIptalYukleme()
{

  var checked=document.getElementById("sws_sureliIptalYukleme").checked;


  if(checked)
  {
      checked="1";
  }
  else
  {
      checked="0";
  }

  var postBody={
  //id:id,
  status:checked,
  type:1
  }
  $.ajax({
      type:'post',
      url:"ajax/RobotHesap/SureliIptalYukleme",
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
