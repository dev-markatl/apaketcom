<?php

/**
 * Created by Reliese Model.
 * Date: Sat, 29 Sep 2018 17:33:55 +0300.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Istekfatura
 * 
 * @property int $id
 * @property string $tel
 * @property int $robotId
 * @property int $kullaniciId
 * @property bool $robotAldi
 * @property bool $robotDondu
 * @property \Carbon\Carbon $almaZamani
 * @property \Carbon\Carbon $donmeZamani
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property float $tutar
 * @property string $sonOdemeTarihi
 * @property string $aboneAdi
 * @property string $kurumKodu
 * @property string $faturaNo
 * @property string $tesisatNo
 * @property string $tekilNumara
 * @property int $kurumId
 * @property int $denemeSayisi
 * @property string $sonDegisiklikYapan

 * 
 * @property \App\Models\Robot $robot
 * @property \App\Models\Kullanici $kullanici
 * @property \App\Models\Kurum $kurum
 *
 * @package App\Models
 */
class Istekfatura extends Eloquent
{
	protected $table = 'istekfatura';
	
	public $timestamps =true;

	protected $casts = [
		'robotId' => 'int',
		'kullaniciId' => 'int',
		'robotAldi' => 'bool',
		'robotDondu' => 'bool',
		'tutar' => 'float',
		'kurumId' => 'int',
		'denemeSayisi' => 'int'
	];

	protected $dates = [
		'almaZamani',
		'donmeZamani'
	];

	protected $fillable = [
		'tel',
		'robotId',
		'kullaniciId',
		'robotAldi',
		'robotDondu',
		'almaZamani',
		'donmeZamani',
		'tutar',
		'sonOdemeTarihi',
		'sonDegisiklikYapan',
		'aboneAdi',
		'kurumKodu',
		'faturaNo',
		'denemeSayisi',
		'tesisatNo',
		'tekilNumara',
		'kurumId'
	];

	public function robot()
	{
		return $this->belongsTo(\App\Models\Robot::class, 'robotId');
	}

	public function kullanici()
	{
		return $this->belongsTo(\App\Models\Kullanici::class, 'kullaniciId');
	}

	public function kurum()
	{
		return $this->belongsTo(\App\Models\Kurum::class, 'kurumId');
	}
}
