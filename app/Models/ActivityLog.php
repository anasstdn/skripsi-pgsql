<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityLog
 * 
 * @property int $id
 * @property character varying|null $log_name
 * @property string $description
 * @property int|null $subject_id
 * @property character varying|null $subject_type
 * @property int|null $causer_id
 * @property character varying|null $causer_type
 * @property string|null $properties
 * @property timestamp without time zone|null $created_at
 * @property timestamp without time zone|null $updated_at
 *
 * @package App\Models
 */
class ActivityLog extends Model
{
	protected $table = 'activity_log';

	protected $casts = [
		'log_name' => 'character varying',
		'subject_id' => 'int',
		'subject_type' => 'character varying',
		'causer_id' => 'int',
		'causer_type' => 'character varying',
		'created_at' => 'timestamp without time zone',
		'updated_at' => 'timestamp without time zone'
	];

	protected $fillable = [
		'log_name',
		'description',
		'subject_id',
		'subject_type',
		'causer_id',
		'causer_type',
		'properties'
	];
}
