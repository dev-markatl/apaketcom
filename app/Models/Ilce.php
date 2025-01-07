<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Ilce
 * 
 * @property int $id
 * @property string $adi
 * @property int $ilId
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Il $il
 * @property \Illuminate\Database\Eloquent\Collection $kullanicis
 *
 * @package App\Models
 */
class Ilce extends Eloquent
{
	protected $table = 'ilce';
	public $timestamps =true;
	protected $casts = [
		'ilId' => 'int'
	];

	protected $fillable = [
		'adi',
		'ilId'
	];

	public function il()
	{
		return $this->belongsTo(\App\Models\Il::class, 'ilId');
	}

	public function kullanicis()
	{
		return $this->hasMany(\App\Models\Kullanici::class, 'ilceId');
	}
}
