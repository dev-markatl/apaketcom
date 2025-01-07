<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Paket
 * 
 * @property int $id
 * @property bool $aktif
 * @property string $adi
 * @property int $kod
 * @property int $operatorId
 * @property int $tipId
 * @property float $maliyetFiyati
 * @property float $resmiSatisFiyati
 * @property int $sistemPaketKodu
 * @property bool $silindi
 * @property bool $sorguyaEkle
 * @property bool $yeni
 * @property int $gun
 * @property int $herYoneKonusma
 * @property int $sebekeIciKonusma
 * @property int $herYoneSms
 * @property int $sebekeIciSms
 * @property int $internet
 * @property int $kategoriNo
 * @property string $kategoriAdi
 * @property int $siraNo
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Operator $operator
 * @property \App\Models\Tip $tip
 * @property \Illuminate\Database\Eloquent\Collection $isteks
 * @property \Illuminate\Database\Eloquent\Collection $istekcevaps
 *
 * @package App\Models
 */
class Paket extends Eloquent
{
	protected $table = 'paket';
	//public $timestamps =true;
	protected $casts = [
		'aktif' => 'bool',
		'kod' => 'int',
		'operatorId' => 'int',
		'tipId' => 'int',
		'maliyetFiyati' => 'float',
		'resmiSatisFiyati' => 'float',
		'sistemPaketKodu' => 'int',
		'silindi' => 'bool',
		'sorguyaEkle' => 'bool',
		'yeni' => 'bool',
		'gun' => 'int',
		'herYoneKonusma' => 'int',
		'sebekeIciKonusma' => 'int',
		'herYoneSms' => 'int',
		'sebekeIciSms' => 'int',
		'internet' => 'int',
		'kategoriNo' => 'int',
		'siraNo' => 'int'
	];

	protected $fillable = [
		'aktif',
		'adi',
		'kod',
		'operatorId',
		'tipId',
		'maliyetFiyati',
		'resmiSatisFiyati',
		'sistemPaketKodu',
		'silindi',
		'sorguyaEkle',
		'yeni',
		'gun',
		'herYoneKonusma',
		'sebekeIciKonusma',
		'herYoneSms',
		'sebekeIciSms',
		'internet',
		'kategoriNo',
		'kategoriAdi',
		'siraNo',
		'sonDegisiklikYapan'
	];

	public function operator()
	{
		return $this->belongsTo(\App\Models\Operator::class, 'operatorId');
	}

	public function tip()
	{
		return $this->belongsTo(\App\Models\Tip::class, 'tipId');
	}

	public function isteks()
	{
		return $this->hasMany(\App\Models\Istek::class, 'paketId');
	}

	public function istekcevaps()
	{
		return $this->hasMany(\App\Models\Istekcevap::class, 'paketId');
	}
}
