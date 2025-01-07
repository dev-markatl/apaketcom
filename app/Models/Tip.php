<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:46 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Tip
 * 
 * @property int $id
 * @property string $adi
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $pakets
 *
 * @package App\Models
 */
class Tip extends Eloquent
{
	protected $table = 'tip';
	public $timestamps =true;
	protected $fillable = [
		'adi',
		'sonDegisiklikYapan'
	];

	public function pakets()
	{
		return $this->hasMany(\App\Models\Paket::class, 'tipId');
	}
}
