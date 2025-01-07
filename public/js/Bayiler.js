function Temizle()
{
    window.location.href = "bayilistesi-bayiler/temizle";
}
function ModalClick(type,id)
{
    var width=0;
    var height=0;
    var url="";
    switch (type) {
        case 'guncelle':
            url="ajax/KullaniciOzellikleri?";

            width=800;
            height=580;
            break;
        case 'yeni':
            url="ajax/KullaniciOzellikleri?";

            width=800;
            height=580;
            break;
        case 'ekle':
            url="ajax/BayiPara?cikar=false&";

            width=500;
            height=380;
            break;
        case 'cikar':
            url="ajax/BayiPara?cikar=true&";

            width=500;
            height=380;
            break;
        case 'hesap':
            url="ajax/BayiHesapHareketleri?sayfa=1&";

            width=1080;
            height=530;
            break;

        default:
            break;
    }
    window.open(url+"id="+id, "aa", "height="+height+",width="+width);
}
function UpdateSw(fullid,type,id)
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
        url:"ajax/BayiListesi/UpdateSw",
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
function UpdateFiyat(id)
{
    var cur = document.getElementById("fiyat_"+id);
    var curVal=cur.value;


        var postBody={
        id:id,
        sorguUcret:curVal
        }

        $.ajax({
            type:'post',
            url:"ajax/BayiListesi/UpdateFiyat",
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

function UpdateSiteBlokaj(fullid,id,takmaad,sitead)
{
    var checked=document.getElementById("blk_"+id).checked;

    if(checked)
    {
        checked="1";
    }
    else
    {
        checked="0";
    }

    var postBody={
        takmaad:takmaad,
        status:checked,
        sitead:sitead
        }
        $.ajax({
            type:'post',
            url:"ajax/Siteler/Blokaj",
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

function UpdateSiteBlokajYukleme(fullid,id,takmaad,sitead)
{
    var checked=document.getElementById("blkb_"+id).checked;

    if(checked)
    {
        checked="1";
    }
    else
    {
        checked="0";
    }

    var postBody={
        takmaad:takmaad,
        status:checked,
        sitead:sitead
        }
        $.ajax({
            type:'post',
            url:"ajax/Siteler/BlokajYukleme",
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

function UpdateBayiBlokaj(fullid,id)
{

    var checked=document.getElementById("blk_"+id).checked;


    if(checked)
    {
        checked="1";
    }
    else
    {
        checked="0";
    }

    var postBody={
    id:id,
    status:checked,
    type:1
    }
    $.ajax({
        type:'post',
        url:"ajax/BayiNoHareket/Blokaj",
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

function UpdateBayiBlokajYukleme(fullid,id)
{

    var checked=document.getElementById("blkb_"+id).checked;


    if(checked)
    {
        checked="1";
    }
    else
    {
        checked="0";
    }

    var postBody={
    id:id,
    status:checked,
    type:1
    }
    $.ajax({
        type:'post',
        url:"ajax/BayiNoHareket/BlokajYukleme",
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



function UpdateBayiBilgi(id)
{
    var cur = document.getElementById("bayibilgi_"+id);
    var curVal=cur.value;


        var postBody={
        id:id,
        bayiBilgi:curVal
        }

        $.ajax({
            type:'post',
            url:"ajax/BayiNoHareket/UpdateBayiBilgi",
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


function UpdateBayiKategori(id)
{
    var cur = document.getElementById("bayikategori_"+id);
    var curVal=cur.value;


        var postBody={
        id:id,
        bayiKategori:curVal
        }

        $.ajax({
            type:'post',
            url:"ajax/BayiNoHareket/UpdateBayiKategori",
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
