<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 23 Feb 2019 13:59:39 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Bayihareket
 * 
 * @property int $id
 * @property int $bayi_id
 * @property \Carbon\Carbon $islem_tarih
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Bayibilgi $bayibilgi
 *
 * @package App\Models
 */
class Bayihareket extends Eloquent
{
	protected $table = 'bayihareket';

	protected $casts = [
		'bayi_id' => 'int'
	];

	protected $dates = [
		'islem_tarih'
	];

	protected $fillable = [
		'bayi_id',
		'islem_tarih',
			'operator'
	];

	public function bayibilgi()
	{
		return $this->belongsTo(\App\Models\Bayibilgi::class, 'bayi_id');
	}
}
