<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 04 Mar 2019 15:55:12 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Genelayarlar
 * 
 * @property int $id
 * @property \Carbon\Carbon $exIptalZamani
 * @property bool $olumsuzaTavsiyeDon
 * @property bool $olumsuzSorguTekrar
 * @property string $sonDegisiklikYapan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Genelayarlar extends Eloquent
{
	protected $table = 'genelayarlar';

	protected $casts = [
		'olumsuzaTavsiyeDon' => 'bool',
		'olumsuzSorguTekrar' => 'bool',
		'sistemiKapat' => 'bool'
	];

	protected $dates = [
		'exIptalZamani'
	];

	protected $fillable = [
		'exIptalZamani',
		'olumsuzaTavsiyeDon',
		'olumsuzSorguTekrar',
		'sonDegisiklikYapan',
		'sistemiKapat'
	];
}
