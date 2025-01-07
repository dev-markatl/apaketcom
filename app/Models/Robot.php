<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Notifications\Notifiable;


/**
 * Class Robot
 * 
 * @property int $id
 * @property string $adi
 * @property string $sifre
 * @property bool $aktif
 * @property int $operatorId
 * @property bool $yukleyici
 * @property bool $yetkiYukle
 * @property bool $yetkiSorgu
 * @property bool $yetkiFatura
 * @property bool $mesgul
 * @property float $sistemBakiye
 * @property float $posBakiye
 * @property bool $silindi
 * @property int $kullaniciId
 * @property int $robotTipId
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Operator $operator
 * @property \App\Models\Kullanici $kullanici
 * @property \Illuminate\Database\Eloquent\Collection $isteks
 * @property \Illuminate\Database\Eloquent\Collection $robothesaphareketleris
 *
 * @package App\Models
 */
class Robot extends Authenticatable
{
	protected $table = 'robot';
	public $timestamps =true;
	protected $casts = [
		'aktif' => 'bool',
		'operatorId' => 'int',
		'yukleyici' => 'bool',
		'yetkiYukle' => 'bool',
		'yetkiSorgu' => 'bool',
		'yetkiFatura' => 'bool',
		'mesgul' => 'bool',
		'sistemBakiye' => 'float',
		'posBakiye' => 'float',
		'silindi' => 'bool',
		'kullaniciId' => 'int',
		'robotTipId'=>'int'
	];

	protected $fillable = [
		'adi',
		'sifre',
		'aktif',
		'operatorId',
		'yukleyici',
		'yetkiYukle',
		'yetkiSorgu',
		'yetkiFatura',
		'mesgul',
		'sistemBakiye',
		'posBakiye',
		'silindi',
		'kullaniciId',
		'robotTipId',
		'sonDegisiklikYapan'
	];

	public function robottip()
	{
		return $this->belongsTo(\App\Models\RobotTip::class, 'robotTipId');
	}
	public function operator()
	{
		return $this->belongsTo(\App\Models\Operator::class, 'operatorId');
	}

	public function kullanici()
	{
		return $this->belongsTo(\App\Models\Kullanici::class, 'kullaniciId');
	}

	public function isteks()
	{
		return $this->hasMany(\App\Models\Istek::class, 'robotId');
	}

	public function robothesaphareketleris()
	{
		return $this->hasMany(\App\Models\Robothesaphareketleri::class, 'robotId');
	}
}
