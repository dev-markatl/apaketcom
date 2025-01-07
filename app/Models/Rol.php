<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:46 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Rol
 * 
 * @property int $id
 * @property string $rolAdi
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $kullanicis
 *
 * @package App\Models
 */
class Rol extends Eloquent
{
	protected $table = 'rol';
	public $timestamps =true;
	protected $fillable = [
		'rolAdi',
		'sonDegisiklikYapan'
	];

	public function kullanicis()
	{
		return $this->hasMany(\App\Models\Kullanici::class, 'rolId');
	}
}
