<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RawDatum
 * 
 * @property int $id
 * @property Carbon|null $tgl_transaksi
 * @property character varying|null $no_nota
 * @property float|null $pasir
 * @property float|null $gendol
 * @property float|null $abu
 * @property float|null $split2_3
 * @property float|null $split1_2
 * @property float|null $lpa
 * @property character varying|null $campur
 * @property timestamp without time zone|null $created_at
 * @property timestamp without time zone|null $updated_at
 *
 * @package App\Models
 */
class RawDatum extends Model
{
	protected $table = 'raw_data';

	protected $casts = [
		'no_nota' => 'character varying',
		'pasir' => 'float',
		'gendol' => 'float',
		'abu' => 'float',
		'split2_3' => 'float',
		'split1_2' => 'float',
		'lpa' => 'float',
		'campur' => 'character varying',
		'created_at' => 'timestamp without time zone',
		'updated_at' => 'timestamp without time zone'
	];

	protected $dates = [
		'tgl_transaksi'
	];

	protected $fillable = [
		'tgl_transaksi',
		'no_nota',
		'pasir',
		'gendol',
		'abu',
		'split2_3',
		'split1_2',
		'lpa',
		'campur'
	];
}
