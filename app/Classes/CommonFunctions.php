<?php
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Models\Istekcevap;
use App\Models\Kullanicihesaphareketleri;
use App\Models\Istek;
use Carbon\Carbon;

//version 1.00 -3.04.2017
class CommonFunctions
{
    public $SayfaSayisi;
    public function TarihSiniriAsildiMi($tarihHata)
    {
        if(!$tarihHata)
        {
            echo "<script> toastr.error('Tarih aralıgı 3 ayı gecemez!'); </script>";
        }

    }
    public function TarihSiniri($tarih1,$tarih2)
    {
        if($tarih1 !=null )
        {

            if($tarih2 == null)
                $tarih2=date('Y-m-d', time());

            $sonucSuresi=strtotime($tarih2) - strtotime($tarih1);
            if(60*60*24*30*3<$sonucSuresi)
                return false;//hata var
            else
                return true;

        }
        return true;
    }
    public function zamanHesapla($almaZamani,$donmeZamani)
    {
        if($donmeZamani!=null && $almaZamani!=null)
        {
            $sonucSuresi=strtotime($donmeZamani) - strtotime($almaZamani);
            if($sonucSuresi>60)
            {
                $dk=intdiv($sonucSuresi,60);
                $dakikaCikar=$dk*60;
                $saniye=$sonucSuresi-$dakikaCikar;
                $sonucSuresi=$dk." Dk ".$saniye." Sn";
            }
            else
            {
                if($sonucSuresi<0)
                    $sonucSuresi="Belirsiz";

                $sonucSuresi=$sonucSuresi." Sn";
            }

        }
        else
            $sonucSuresi="0 Sn";



        return $sonucSuresi;
    }
    public function GetAvaiblePackets($operator)
    {
        $sorgu="SELECT
                    o.adi as operatorAdi ,
                    t.adi as tipAdi  ,
                    p.adi ,
                    p.kod,
                    p.id ,
                    p.maliyetFiyati,
                    p.resmiSatisFiyati,
                    p.kategoriNo,
                    p.kategoriAdi,
                    p.siraNo,
                    p.alternatifKodlar,
                    p.herYoneKonusma as dakika,
                    p.internet as internet,
                    p.resmiSatisFiyati as fiyat,
                    p.maliyetFiyati as paketMaliyet,
                    p.gun
                FROM
                    paket p,
                    tip t ,
                    operator o
                WHERE
                    p.tipId=t.id AND
                    p.operatorId=o.id AND
                    p.aktif=1 AND
                    p.yeni=0 AND
                    p.silindi=0";
        $sorguArr=array();
        if($operator!="*")
        {
            $sorgu=$sorgu." AND o.adi=? ";
            array_push($sorguArr,$operator);
        }


        $packets=DB::select($sorgu,$sorguArr);
        return $packets;
    }
    public function FirsatPaketleri($operator)
    {
        $sorgu="SELECT
                    o.adi as operatorAdi ,
                    t.adi as tipAdi  ,
                    p.adi ,
                    p.kod,
                    p.id ,
                    p.maliyetFiyati,
                    p.resmiSatisFiyati
                FROM
                    paket p,
                    tip t ,
                    operator o
                WHERE
                    p.tipId=t.id AND
                    p.operatorId=o.id AND
                    p.aktif=1 AND
                    p.yeni=0 AND
                    p.silindi=0 AND
                    t.id=2";
        $sorguArr=array();
        if($operator!="*")
        {
            $sorgu=$sorgu." AND o.adi=? ";
            array_push($sorguArr,$operator);
        }


        $packets=DB::select($sorgu,$sorguArr);
        return $packets;
    }
    public function BekleyenVarMiSorgu($operatorId)
    {
        $bekleyenVarMi=DB::select("SELECT
                                        i.id  ,
                                        i.ozelfiyatId,
                                        p.kod ,
                                        p.adi as paketAdi,
                                        i.tel ,
                                        t.adi as tipAdi ,
                                        p.kategoriNo ,
                                        p.kategoriAdi ,
                                        p.siraNo
                                    FROM
                                        istek i ,
                                        paket p ,
                                        tip t
                                    WHERE
                                        p.tipId=t.id AND
                                        p.operatorId=? AND
                                        i.paketId=p.id AND
                                        (p.kod BETWEEN 4999 AND 6001) AND
                                        i.robotAldi=0 AND
                                        i.robotDondu=0 AND
                                        i.robotId=1  ORDER BY
                                        i.id ASC
                                    LIMIT 1 ",array($operatorId));
        return $bekleyenVarMi;
    }
    public function BekleyenSorguSayisi($operatorId)
    {
        $bekleyenVarMi=DB::select("SELECT
                                      i.id
                                    FROM
                                        istek i ,
                                        paket p ,
                                        tip t
                                    WHERE
                                        p.tipId=t.id AND
                                        p.operatorId=? AND
                                        i.paketId=p.id AND
                                        (p.kod BETWEEN 4999 AND 6001) AND
                                        i.robotAldi=0 AND
                                        i.robotDondu=0 AND
                                        i.robotId=1  ORDER BY
                                        i.id ASC
                                     ",array($operatorId));
        return count($bekleyenVarMi);
    }

    public function BekleyenVarmiFatura($kullaniciId)
    {
        $bekleyenVarMi=DB::select("SELECT
                                    i.id ,
                                    i.tel ,
                                    i.tutar ,
                                    i.sonOdemeTarihi ,
                                    i.aboneAdi ,
                                    i.kurumKodu ,
                                    i.faturaNo ,
                                    i.tesisatNo ,
                                    k.adi
                                FROM
                                    istekfatura i ,
                                    kurum k
                                WHERE
                                    i.kurumId=k.id AND
                                    i.robotAldi=0 AND
                                    i.robotDondu=0 AND
                                    i.kullaniciId=? ORDER BY
                                    i.sonOdemeTarihi ASC
                                LIMIT 1 ",array($kullaniciId));
        return $bekleyenVarMi;

    }
    public function BekleyenVarmiYukle($operatorId,$kullaniciId,$kullanici2,$robotFiyatGrup)
    {
        if($robotFiyatGrup != 0)
        {
                $bekleyenVarMi=DB::select("SELECT
                i.id  ,
                i.kullaniciId,
                i.paketId,
                p.kod ,
                p.adi as paketAdi,
                i.tel ,
                t.adi as tipAdi ,
                p.kategoriNo ,
                p.kategoriAdi ,
                p.siraNo,
                i.created_at as sistemTarihi,
                o.id as ozelfiyatId,
                o.maliyet_fiyat as maliyetFiyati,
                o.resmi_fiyat as resmiSatisFiyati
            FROM
                istek i ,
                paket p ,
                tip t,
                ozelfiyat o
            WHERE
                p.tipId=t.id AND
                p.operatorId=? AND
                i.paketId=p.id AND
                o.paket_id = i.paketId AND
                o.aktif = 1 AND
                o.silindi = 0 AND
                o.fiyatgrup_id =? AND
                !(p.kod BETWEEN 4999 AND 6001) AND
                i.robotAldi=0 AND
                i.robotDondu=0 AND
                i.robotId=1 AND
                (i.kullaniciId=? OR i.kullaniciId=?)
                ORDER BY
                i.id ASC
                LIMIT 1 ",array($operatorId,$robotFiyatGrup,$kullaniciId,$kullanici2));

        }
        else
        {
                $bekleyenVarMi=DB::select("SELECT
                i.id  ,
                i.kullaniciId,
                i.paketId,
                i.ozelfiyatId,
                p.kod ,
                p.adi as paketAdi,
                i.tel ,
                t.adi as tipAdi ,
                p.maliyetFiyati ,
                p.resmiSatisFiyati ,
                p.kategoriNo ,
                p.kategoriAdi ,
                p.siraNo,
                i.created_at as sistemTarihi         
            FROM
                istek i ,
                paket p ,
                tip t
            WHERE
                p.tipId=t.id AND
                p.operatorId=? AND
                i.paketId=p.id AND
                !(p.kod BETWEEN 4999 AND 6001) AND
                i.robotAldi=0 AND
                i.robotDondu=0 AND
                i.robotId=1 AND
                (i.kullaniciId=? OR i.kullaniciId=?)
                ORDER BY
                i.id ASC
                LIMIT 1 ",array($operatorId,$kullaniciId,$kullanici2));

        }

        return $bekleyenVarMi;
    }
    
    public function KontorBekleyenSayisi($operatorId,$kullaniciId,$kullanici2,$robotSureSinir,$robotFiyatGrup)
    {



        if($robotFiyatGrup != 0)
        {
                $bekleyenVarMi=DB::select("SELECT
                i.id  ,
                i.paketId,
                p.kod ,
                p.adi as paketAdi,
                i.tel ,
                t.adi as tipAdi ,
                p.kategoriNo ,
                p.kategoriAdi ,
                p.siraNo,
                i.created_at as sistemTarihi,
                o.id as ozelfiyatId,
                o.maliyet_fiyat as maliyetFiyati,
                o.resmi_fiyat as resmiSatisFiyati
            FROM
                istek i ,
                paket p ,
                tip t,
                ozelfiyat o
            WHERE
                p.tipId=t.id AND
                p.operatorId=? AND
                i.paketId=p.id AND
                o.paket_id = i.paketId AND
                o.aktif = 1 AND
                o.silindi = 0 AND
                o.fiyatgrup_id =? AND
                !(p.kod BETWEEN 4999 AND 6001) AND
                i.robotAldi=0 AND
                i.robotDondu=0 AND
                i.robotId=1 AND
                (i.kullaniciId=? OR i.kullaniciId=?)",array($operatorId,$robotFiyatGrup,$kullaniciId,$kullanici2));

        }
        else
        {
                $bekleyenVarMi=DB::select("SELECT
                i.id  ,
                i.paketId,
                i.ozelfiyatId,
                p.kod ,
                p.adi as paketAdi,
                i.tel ,
                t.adi as tipAdi ,
                p.maliyetFiyati ,
                p.resmiSatisFiyati ,
                p.kategoriNo ,
                p.kategoriAdi ,
                p.siraNo,
                i.created_at as sistemTarihi         
            FROM
                istek i ,
                paket p ,
                tip t
            WHERE
                p.tipId=t.id AND
                p.operatorId=? AND
                i.paketId=p.id AND
                !(p.kod BETWEEN 4999 AND 6001) AND
                i.robotAldi=0 AND
                i.robotDondu=0 AND
                i.robotId=1 AND
                (i.kullaniciId=? OR i.kullaniciId=?)",array($operatorId,$kullaniciId,$kullanici2));

        }

        $netCount = 0;

        if(count($bekleyenVarMi)<1)
        {
            return $netCount;
        }
        else
        {
            foreach($bekleyenVarMi as $bekle)
            {

                $simdikiZaman = Carbon::now();
                $sistemZaman = $bekle->sistemTarihi;
                $sureFarki = $simdikiZaman->diffInSeconds($sistemZaman);
    
                if ($robotSureSinir > $sureFarki && $bekle->tipAdi != "tam")
                {
                   //
                }
                else
                {
                    $netCount = $netCount + 1;
                }

            }
         
        }

        return $netCount;


    }

    public function GelenPaketler($packets,$operatorId)
    {
        try
        {
            if($packets==null)
                return array();

            $sorgu="SELECT id,alternatifKodlar FROM paket WHERE kod IN(";
            $incomingData = explode(',', $packets);
            foreach($incomingData as $data)
            {
                $sorgu=$sorgu.$data.",";
            }
            $sorgu=substr($sorgu,0,-1);
            $packet=DB::select($sorgu.") AND silindi=0 AND aktif=1 AND tipId=2 AND operatorId=?  ",array($operatorId));
            return $packet;

        }
        catch(\Excepiton $e)
        {
            return array();
        }


    }
    public function RequestKayitBosalt($id)
    {

        $req=Istek::where("id",$id)->first();
        $req->denemeSayisi=0;
        $req->durum=0;
        $req->robotId=1;
        $req->robotDondu=0;
        $req->robotAldi=0;
        $req->sonDegisiklikYapan="KayitBosaltFonksiyonu";
        $req->save();
    }
    public function Paginate($sorgu,$sorguArr,$suankiSayfa,$sayfadaGosterilecekKayitSayisi,$count)
    {

        //$limit=$suankiSayfa*$sayfadaGosterilecekKayitSayisi;
        $altLimit=($suankiSayfa-1)*$sayfadaGosterilecekKayitSayisi;
        array_push($sorguArr,$altLimit);
        array_push($sorguArr,$sayfadaGosterilecekKayitSayisi);
        $hareketler=DB::select($sorgu." Limit ?,?",$sorguArr);
        $this->SayfaSayisi=ceil($count / $sayfadaGosterilecekKayitSayisi);
        return $hareketler;
    }
    public function GetCount($sorgu,$sorguArr)
    {
        $selectPos=strpos($sorgu, "SELECT");
        $fromPos=strpos($sorgu, "FROM");
        $kalan=substr($sorgu,$fromPos,strlen($sorgu));
        $yeniSorgu="SELECT count(*) as toplam ".$kalan;
        $count=DB::select($yeniSorgu,$sorguArr);
        return $count[0]->toplam;
    }
    public function cevapPaketleri($istekId)
    {
        $cevap=DB::select("SELECT ic.id ,p.adi,p.kod FROM istekcevap ic,paket p WHERE ic.istekId=? AND ic.paketId=p.id",array($istekId));
        return $cevap;
    }

}
?>
