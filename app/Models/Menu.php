<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
 * 
 * @property int $id
 * @property character varying $name
 * @property character varying|null $url
 * @property character varying|null $icon
 * @property int|null $ordinal
 * @property character varying|null $parent_status
 * @property int|null $parent_id
 * @property int|null $permission_id
 * @property timestamp without time zone|null $created_at
 * @property timestamp without time zone|null $updated_at
 * 
 * @property Menu $menu
 * @property Permission $permission
 * @property Collection|Menu[] $menus
 *
 * @package App\Models
 */
class Menu extends Model
{
	protected $table = 'menu';

	protected $casts = [
		'name' => 'character varying',
		'url' => 'character varying',
		'icon' => 'character varying',
		'ordinal' => 'int',
		'parent_status' => 'character varying',
		'parent_id' => 'int',
		'permission_id' => 'int',
		'created_at' => 'timestamp without time zone',
		'updated_at' => 'timestamp without time zone'
	];

	protected $fillable = [
		'name',
		'url',
		'icon',
		'ordinal',
		'parent_status',
		'parent_id',
		'permission_id'
	];

	public function menu()
	{
		return $this->belongsTo(Menu::class, 'parent_id');
	}

	public function permission()
	{
		return $this->belongsTo(Permission::class);
	}

	public function menus()
	{
		return $this->hasMany(Menu::class, 'parent_id');
	}
}
