<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:46 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class RobotTip
 * 
 * @property int $id
 * @property string $adi
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \Illuminate\Database\Eloquent\Collection $kullanicis
 *
 * @package App\Models
 */
class RobotTip extends Eloquent
{
	protected $table = 'robottip';
	public $timestamps =true;
	protected $fillable = [
		'adi',
		'sonDegisiklikYapan'
	];

	public function robots()
	{
		return $this->hasMany(\App\Models\Robot::class, 'robotTipId');
	}
}
