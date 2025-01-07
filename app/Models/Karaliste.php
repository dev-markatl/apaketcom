<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 14 Aug 2018 13:44:45 +0000.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;


class Karaliste extends Eloquent
{
	protected $table = 'karaliste';
	public $timestamps =true;
	protected $casts = [
        'telefon' => 'string',
        'sorgu_blok'=>'int',
        'yukleme_blok'=>'int',
    
	];



	protected $fillable = [
		'telefon',
		'sorgu_blok',
                'yukleme_blok',
	];
}
