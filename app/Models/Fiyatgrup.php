<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Fiyatgrup extends Eloquent
{
	protected $table = 'fiyatgrup';
	//public $timestamps =true;
	protected $casts = [

	];

	protected $fillable = [
    'grup_ad',
    'operator_id'
	];

  public function operator()
  {
    return $this->belongsTo(\App\Models\Operator::class, 'operator_id');
  }


}
