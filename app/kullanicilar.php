<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class kullanicilar extends Model
{
    public $timestamps = false;
    protected $table= "kullanicilar";
    protected $fillable = ['id','ip','kul_adi','sifre','bakiye'];
}
