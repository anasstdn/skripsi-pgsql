<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Perusahaan
 * 
 * @property int $id
 * @property character varying|null $nama_ps
 * @property string|null $alamat_ps
 * @property character varying|null $email_ps
 * @property character varying|null $fax_ps
 * @property character varying|null $telp_ps
 * @property character varying|null $website_ps
 * @property Carbon|null $tgl_berdiri_ps
 * @property character varying|null $flag_ksp
 * @property character varying|null $flag_ksu
 * @property timestamp without time zone|null $created_at
 * @property timestamp without time zone|null $updated_at
 *
 * @package App\Models
 */
class Perusahaan extends Model
{
	protected $table = 'perusahaan';

	protected $casts = [
		'nama_ps' => 'character varying',
		'email_ps' => 'character varying',
		'fax_ps' => 'character varying',
		'telp_ps' => 'character varying',
		'website_ps' => 'character varying',
		'flag_ksp' => 'character varying',
		'flag_ksu' => 'character varying',
		'created_at' => 'timestamp without time zone',
		'updated_at' => 'timestamp without time zone'
	];

	protected $dates = [
		'tgl_berdiri_ps'
	];

	protected $fillable = [
		'nama_ps',
		'alamat_ps',
		'email_ps',
		'fax_ps',
		'telp_ps',
		'website_ps',
		'tgl_berdiri_ps',
		'flag_ksp',
		'flag_ksu'
	];
}
