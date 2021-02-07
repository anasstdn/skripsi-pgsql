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
 * @property string|null $nama_ps
 * @property string|null $alamat_ps
 * @property string|null $email_ps
 * @property string|null $fax_ps
 * @property string|null $telp_ps
 * @property string|null $website_ps
 * @property Carbon|null $tgl_berdiri_ps
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $flag_peramalan_bulanan
 *
 * @package App\Models
 */
class Perusahaan extends Model
{
	protected $table = 'perusahaan';

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
		'flag_peramalan_bulanan'
	];
}
