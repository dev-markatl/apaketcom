<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Operator
 * 
 * @property int $id
 * @property string $adi
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $pakets
 * @property \Illuminate\Database\Eloquent\Collection $robots
 *
 * @package App\Models
 */
class Operator extends Eloquent
{
	protected $table = 'operator';
	public $timestamps =true;
	protected $fillable = [
		'adi',
		'sonDegisiklikYapan'
	];

	public function pakets()
	{
		return $this->hasMany(\App\Models\Paket::class, 'operatorId');
	}

	public function robots()
	{
		return $this->hasMany(\App\Models\Robot::class, 'operatorId');
	}
}
