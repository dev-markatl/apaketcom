<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Ip
 * 
 * @property int $id
 * @property string $ipAdres
 * @property int $kullaniciId
 * @property int $isyeri
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Kullanici $kullanici
 *
 * @package App\Models
 */
class Ip extends Eloquent
{
	protected $table = 'ip';
	public $timestamps =true;
	protected $casts = [
		'kullaniciId' => 'int',
		'isyeri' => 'int'
	];

	protected $fillable = [
		'ipAdres',
		'kullaniciId',
		'isyeri',
		'sonDegisiklikYapan'
	];

	public function kullanici()
	{
		return $this->belongsTo(\App\Models\Kullanici::class, 'kullaniciId');
	}
}
