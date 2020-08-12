<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * 
 * @property int $id
 * @property character varying $name
 * @property character varying|null $display_name
 * @property character varying|null $description
 * @property timestamp without time zone|null $created_at
 * @property timestamp without time zone|null $updated_at
 * 
 * @property Collection|PermissionUser[] $permission_users
 * @property Collection|Role[] $roles
 * @property Collection|Menu[] $menus
 *
 * @package App\Models
 */
class Permission extends Model
{
	protected $table = 'permissions';

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

	public function permission_users()
	{
		return $this->hasMany(PermissionUser::class);
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class);
	}

	public function menus()
	{
		return $this->hasMany(Menu::class);
	}
}
