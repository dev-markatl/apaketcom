<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bot extends Model
{
	public $timestamps = false;
    protected $table= "bot";
    protected $fillable = ['id','np','sifre','kul_id','ip','ip2','bakiye','kul_id','operator','aktif'];
}
