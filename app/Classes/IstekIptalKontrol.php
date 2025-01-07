<?php 
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Database\QueryException;
use App\Models\Genelayarlar;
use App\Classes\BayiNoHareket;
use Carbon\Carbon;

class IstekIptalKontrol
{

    public static function IstekIptalKontrol($takip)
    {

        $genelAyar = Genelayarlar::where("id","1")->first();

        if($takip[0]->durum != 0)
        {
            return false;
        }

        if($genelAyar->istekIptalAktif == "0")
        {
            return false;
        }

        $paket = DB::select("SELECT p.kod FROM paket p WHERE p.id=?",array($takip[0]->paketId));

        if($paket[0]->kod >= 5000 && $paket[0]->kod <= 6000)
        {
            return false;
        }

        $zaman = Carbon::now();

        $limitZaman = Carbon::parse($takip[0]->created_at)->addSeconds($genelAyar->istekIptalSuresi);

        if($zaman > $limitZaman)
        {
            return true;
        }

        return false;

    }


}