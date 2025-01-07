<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Istekcevap
 * 
 * @property int $id
 * @property int $istekId
 * @property int $paketId
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Istek $istek
 * @property \App\Models\Paket $paket
 *
 * @package App\Models
 */
class Istekcevap extends Eloquent
{
	protected $table = 'istekcevap';
	public $timestamps =true;
	protected $casts = [
		'istekId' => 'int',
		'paketId' => 'int'
	];

	protected $fillable = [
		'istekId',
		'paketId',
		'sonDegisiklikYapan'
	];

	public function istek()
	{
		return $this->belongsTo(\App\Models\Istek::class, 'istekId');
	}

	public function paket()
	{
		return $this->belongsTo(\App\Models\Paket::class, 'paketId');
	}
}
