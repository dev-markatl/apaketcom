<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class Kullanici
 * 
 * @property int $id
 * @property string $ad
 * @property string $soyAd
 * @property string $takmaAd
 * @property float $bakiye
 * @property string $sifre
 * @property string $sonSifre
 * @property bool $yetkiYukle
 * @property bool $yetkiSorgu
 * @property bool $yetkiFatura
 * @property float $sorguUcret
 * @property int $rolId
 * @property string $firmaAdi
 * @property bool $aktif
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $mail
 * @property string $sabitTel
 * @property string $cepTel
 * @property string $vergiDairesi
 * @property string $adres
 * @property int $ilceId
 * @property string $vergiNo
 * @property string $remember_token
 * 
 * @property \App\Models\Rol $rol
 * @property \App\Models\Ilce $ilce
 * @property \Illuminate\Database\Eloquent\Collection $ips
 * @property \Illuminate\Database\Eloquent\Collection $isteks
 * @property \Illuminate\Database\Eloquent\Collection $istekfaturas
 * @property \Illuminate\Database\Eloquent\Collection $kullanicihesaphareketleris
 * @property \Illuminate\Database\Eloquent\Collection $robots
 *
 * @package App\Models
 */
class Kullanici extends Authenticatable
{
	protected $table = 'kullanici';
	public $timestamps =true;

	protected $casts = [
		'bakiye' => 'float',
		'yetkiYukle' => 'bool',
		'yetkiSorgu' => 'bool',
		'yetkiFatura' => 'bool',
		'sorguUcret' => 'float',
		'rolId' => 'int',
		'aktif' => 'bool',
		'ilceId' => 'int'
	];

	protected $hidden = [
		'remember_token'
	];

	protected $fillable = [
		'ad',
		'soyAd',
		'takmaAd',
		'bakiye',
		'sifre',
		'sonSifre',
		'yetkiYukle',
		'yetkiSorgu',
		'yetkiFatura',
		'sorguUcret',
		'rolId',
		'firmaAdi',
		'aktif',
		'sonDegisiklikYapan',
		'mail',
		'sabitTel',
		'cepTel',
		'vergiDairesi',
		'adres',
		'ilceId',
		'vergiNo',
		'remember_token'
	];

	public function rol()
	{
		return $this->belongsTo(\App\Models\Rol::class, 'rolId');
	}

	public function ilce()
	{
		return $this->belongsTo(\App\Models\Ilce::class, 'ilceId');
	}

	public function ips()
	{
		return $this->hasMany(\App\Models\Ip::class, 'kullaniciId');
	}

	public function isteks()
	{
		return $this->hasMany(\App\Models\Istek::class, 'kullaniciId');
	}

	public function istekfaturas()
	{
		return $this->hasMany(\App\Models\Istekfatura::class, 'kullaniciId');
	}

	public function kullanicihesaphareketleris()
	{
		return $this->hasMany(\App\Models\Kullanicihesaphareketleri::class, 'kullaniciId');
	}

	public function robots()
	{
		return $this->hasMany(\App\Models\Robot::class, 'kullaniciId');
	}
}
