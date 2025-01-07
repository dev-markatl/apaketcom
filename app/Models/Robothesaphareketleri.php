<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:46 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Robothesaphareketleri
 * 
 * @property int $id
 * @property int $islemTuruId
 * @property int $robotId
 * @property string $aciklama
 * @property string $paket
 * @property \Carbon\Carbon $tarih
 * @property float $oncekiBakiyeSistem
 * @property float $sonrakiBakiyeSistem
 * @property float $posBakiye
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Islemturu $islemturu
 * @property \App\Models\Robot $robot
 *
 * @package App\Models
 */
class Robothesaphareketleri extends Eloquent
{
	protected $table = 'robothesaphareketleri';
	public $timestamps =true;
	protected $casts = [
		'islemTuruId' => 'int',
		'robotId' => 'int',
		'oncekiBakiyeSistem' => 'float',
		'sonrakiBakiyeSistem' => 'float',
		'posBakiye' => 'float'
	];

	protected $dates = [
		'tarih'
	];

	protected $fillable = [
		'islemTuruId',
		'robotId',
		'aciklama',
		'paket',
		'tarih',
		'oncekiBakiyeSistem',
		'sonrakiBakiyeSistem',
		'posBakiye',
		'sonDegisiklikYapan'
	];

	public function islemturu()
	{
		return $this->belongsTo(\App\Models\Islemturu::class, 'islemTuruId');
	}

	public function robot()
	{
		return $this->belongsTo(\App\Models\Robot::class, 'robotId');
	}
}
