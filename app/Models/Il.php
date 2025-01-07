<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Il
 * 
 * @property int $id
 * @property string $adi
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $ilces
 *
 * @package App\Models
 */
class Il extends Eloquent
{
	protected $table = 'il';
	public $timestamps =true;
	protected $fillable = [
		'adi'
	];

	public function ilces()
	{
		return $this->hasMany(\App\Models\Ilce::class, 'ilId');
	}
}
