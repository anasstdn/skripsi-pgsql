<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DataLog
 * 
 * @property int $id
 * @property string $message
 * @property string $context
 * @property string $level
 * @property string $level_name
 * @property string $channel
 * @property string $record_datetime
 * @property string $extra
 * @property string $formatted
 * @property string|null $remote_addr
 * @property string|null $device
 * @property string|null $user_agent
 * @property int|null $user_id
 * @property string|null $flag_solved
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class DataLog extends Model
{
	use SoftDeletes;
	protected $table = 'data_log';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'message',
		'context',
		'level',
		'level_name',
		'channel',
		'record_datetime',
		'extra',
		'formatted',
		'remote_addr',
		'device',
		'user_agent',
		'user_id',
		'flag_solved'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
