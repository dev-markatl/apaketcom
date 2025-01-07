<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 23 Feb 2019 13:59:39 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Bayibilgi
 *
 * @property int $id
 * @property int $bayi_id
 * @property string $bayi_ad
 * @property \Carbon\Carbon $created_at
 *
 * @property \Illuminate\Database\Eloquent\Collection $bayiharekets
 *
 * @package App\Models
 */
class Bayibilgi extends Eloquent
{
	protected $table = 'bayibilgi';
	public $timestamps = false;

	protected $casts = [
		'bayi_id' => 'int',
	];

	protected $fillable = [
		'bayi_id',
		'bayi_ad',
		'sorgu_blokaj',
		'yukleme_blokaj',
		'kategori',
		'takma_ad'
	];

	public function bayiharekets()
	{
		return $this->hasMany(\App\Models\Bayihareket::class, 'bayi_id');
	}
}
