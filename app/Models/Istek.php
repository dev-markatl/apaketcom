<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 04 Mar 2019 15:55:12 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Istek
 * 
 * @property int $id
 * @property string $tel
 * @property int $robotId
 * @property int $paketId
 * @property int $kullaniciId
 * @property int $durum
 * @property string $cevap
 * @property bool $robotAldi
 * @property bool $robotDondu
 * @property \Carbon\Carbon $almaZamani
 * @property \Carbon\Carbon $donmeZamani
 * @property int $denemeSayisi
 * @property int $olumsuzSayisi
 * @property int $tekilNumara
 * @property int $exIptal
 * @property string $aciklama
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Robot $robot
 * @property \App\Models\Paket $paket
 * @property \App\Models\Kullanici $kullanici
 * @property \Illuminate\Database\Eloquent\Collection $istekcevaps
 *
 * @package App\Models
 */
class Istek extends Eloquent
{
	protected $table = 'istek';

	protected $casts = [
		'robotId' => 'int',
		'paketId' => 'int',
		'kullaniciId' => 'int',
		'durum' => 'int',
		'robotAldi' => 'bool',
		'robotDondu' => 'bool',
		'denemeSayisi' => 'int',
		'olumsuzSayisi' => 'int',
		'tekilNumara' => 'int',
		'exIptal' => 'int'
	];

	protected $dates = [
		'almaZamani',
		'donmeZamani'
	];

	protected $fillable = [
		'tel',
		'robotId',
		'paketId',
		'kullaniciId',
		'durum',
		'cevap',
		'robotAldi',
		'robotDondu',
		'almaZamani',
		'donmeZamani',
		'denemeSayisi',
		'olumsuzSayisi',
		'tekilNumara',
		'exIptal',
		'aciklama',
		'sonDegisiklikYapan'
	];

	public function robot()
	{
		return $this->belongsTo(\App\Models\Robot::class, 'robotId');
	}

	public function paket()
	{
		return $this->belongsTo(\App\Models\Paket::class, 'paketId');
	}

	public function kullanici()
	{
		return $this->belongsTo(\App\Models\Kullanici::class, 'kullaniciId');
	}

	public function istekcevaps()
	{
		return $this->hasMany(\App\Models\Istekcevap::class, 'istekId');
	}
}
