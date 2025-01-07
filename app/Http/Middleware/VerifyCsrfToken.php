<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "servis/paket_listesi.php",
        "servis/tl_servis.php",
        "servis/bakiye_kontrol.php",
        "servis/tl_kontrol.php",
        "api/kontor_yukle.php",
        "api/durum_sorgula.php",
        "api/bakiye.php",
        "api/tl_listesi.php",
        "services/api.php",
        "services/talimat_bakiye_takip.php",
        "services/talimat_takip.php",
        "services/talimat_ver.php",
        "services/paket_listesi.php",
        "servis/operator_listesi.php",
        "servis/fatura_ekle.php",
        "servis/fatura_kontrol.php",
        "servis/fatura_top_kontrol.php",
        "servis/kurum_listesi.php"
        
    ];
}
