<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PermissionUser
 * 
 * @property int $permission_id
 * @property int $user_id
 * @property character varying $user_type
 * 
 * @property Permission $permission
 *
 * @package App\Models
 */
class PermissionUser extends Model
{
	protected $table = 'permission_user';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'permission_id' => 'int',
		'user_id' => 'int',
		'user_type' => 'character varying'
	];

	public function permission()
	{
		return $this->belongsTo(Permission::class);
	}
}
