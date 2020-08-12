<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * 
 * @property int $id
 * @property character varying $name
 * @property character varying|null $display_name
 * @property character varying|null $description
 * @property timestamp without time zone|null $created_at
 * @property timestamp without time zone|null $updated_at
 * 
 * @property Collection|RoleUser[] $role_users
 * @property Collection|Permission[] $permissions
 *
 * @package App\Models
 */
class Role extends Model
{
	protected $table = 'roles';

	protected $casts = [
		'name' => 'character varying',
		'display_name' => 'character varying',
		'description' => 'character varying',
		'created_at' => 'timestamp without time zone',
		'updated_at' => 'timestamp without time zone'
	];

	protected $fillable = [
		'name',
		'display_name',
		'description'
	];

	public function role_users()
	{
		return $this->hasMany(RoleUser::class);
	}

	public function permissions()
	{
		return $this->belongsToMany(Permission::class);
	}
}
