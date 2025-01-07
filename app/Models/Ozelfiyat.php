<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Ozelfiyat extends Eloquent
{
	protected $table = 'ozelfiyat';
	//public $timestamps =true;
	protected $casts = [

	];

	protected $fillable = [
    'fiyatgrup_id',
    'paket_id',
    'aktif',
    'sorguya_ekle',
    'maliyet_fiyat',
    'resmi_fiyat'
	];

  public function fiyatgrup()
  {
    return $this->belongsTo(\App\Models\Fiyatgrup::class, 'fiyatgrup_id');
  }

  public function paket()
  {
    return $this->belongsTo(\App\Models\Paket::class, 'paket_id');
  }


}
