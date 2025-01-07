<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 29 Sep 2018 19:03:16 +0300.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Kurum
 * 
 * @property int $id
 * @property string $adi
 * @property int $kod
 * 
 * @property \Illuminate\Database\Eloquent\Collection $istekfaturas
 *
 * @package App\Models
 */
class Kurum extends Eloquent
{
	protected $table = 'kurum';
	public $timestamps = false;

	protected $casts = [
		'kod' => 'int'
	];

	protected $fillable = [
		'adi',
		'kod'
	];

	public function istekfaturas()
	{
		return $this->hasMany(\App\Models\Istekfatura::class, 'kurumId');
	}
}
