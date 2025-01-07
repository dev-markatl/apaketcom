<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Islemturu
 * 
 * @property int $id
 * @property string $adi
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $kullanicihesaphareketleris
 * @property \Illuminate\Database\Eloquent\Collection $robothesaphareketleris
 *
 * @package App\Models
 */

class Islemturu extends Eloquent
{
	protected $table = 'islemturu';
	public $timestamps =true;
	protected $fillable = [
		'adi',
		'sonDegisiklikYapan'
	];

	public function kullanicihesaphareketleris()
	{
		return $this->hasMany(\App\Models\Kullanicihesaphareketleri::class, 'islemTuruId');
	}

	public function robothesaphareketleris()
	{
		return $this->hasMany(\App\Models\Robothesaphareketleri::class, 'islemTuruId');
	}
}
