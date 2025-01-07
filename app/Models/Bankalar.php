<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Bankalar
 * 
 * @property int $id
 * @property bool $aktif
 * @property string $hesapSahibi
 * @property string $ibanNo
 * @property string $hesapNo
 * @property string $subeKodu
 * @property string $subeAdi
 * @property string $bankaAdi
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Bankalar extends Eloquent
{
	protected $table = 'bankalar';
	public $timestamps =true;
	protected $casts = [
        'aktif' => 'bool',
        'hesapSahibi'=>'string',
        'ibanNo'=>'string',
        'hesapNo'=>'string',
        'subeKodu'=>'string',
        'subeAdi'=>'string',
        'bankaAdi'=>'string',
        'sonDegisiklikYapan'=>'string'
	];



	protected $fillable = [
		'aktif',
		'hesapSahibi',
                'ibanNo',
                'hesapNo',
                'subeKodu',
                'subeAdi',
                'bankaAdi',
                'sonDegisiklikYapan',
	];
}
