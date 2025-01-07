<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Istekgezdirme extends Eloquent
{
	protected $table = 'istekgezdirme';

	protected $casts = [
		'istek_id' => 'int',
		'robot_id' => 'int',
		'kategori_id' => 'int',
		'durum' => 'int'
	];


	protected $fillable = [
		'istek_id',
		'robot_id',
		'kategori_id',
		'cevap',
		'durum',
		'aciklama',
		'islem_yapan'
	];

	public function robot()
	{
		return $this->belongsTo(\App\Models\Robot::class, 'robot_id');
	}

	public function istek()
	{
		return $this->belongsTo(\App\Models\Istek::class, 'istek_id');
	}
}
