<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Kullanicihesaphareketleri
 * 
 * @property int $id
 * @property float $oncekiBakiye
 * @property float $sonrakiBakiye
 * @property string $aciklama
 * @property string $paket
 * @property int $kullaniciId
 * @property \Carbon\Carbon $tarih
 * @property int $islemTuruId
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Kullanici $kullanici
 * @property \App\Models\Islemturu $islemturu
 *
 * @package App\Models
 */
class Kullanicihesaphareketleri extends Eloquent
{
	protected $table = 'kullanicihesaphareketleri';
	public $timestamps =true;
	protected $casts = [
		'oncekiBakiye' => 'float',
		'sonrakiBakiye' => 'float',
		'kullaniciId' => 'int',
		'islemTuruId' => 'int'
	];

	protected $dates = [
		'tarih'
	];

	protected $fillable = [
		'oncekiBakiye',
		'sonrakiBakiye',
		'aciklama',
		'paket',
		'kullaniciId',
		'tarih',
		'islemTuruId',
		'sonDegisiklikYapan'
	];

	public function kullanici()
	{
		return $this->belongsTo(\App\Models\Kullanici::class, 'kullaniciId');
	}

	public function islemturu()
	{
		return $this->belongsTo(\App\Models\Islemturu::class, 'islemTuruId');
	}
}
