<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class takip extends Model
{
	public $timestamps = false;
    protected $table= "takip";
    protected $fillable = ['id','islem_tar','tekilno','kul_id','gsmno',"kontor","tip",'operator','tutar','durum','bot_kodu',"bot_durum","bot_tar","referans_no",
    "n_paket","no_sahibi","hata_aciklama","oto_cevap"];    

























}
