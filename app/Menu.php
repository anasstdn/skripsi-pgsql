<?php

/**
 * Created by Reliese Model.
 */

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
 * 
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $icon
 * @property int $ordinal
 * @property string $parent_status
 * @property int $parent_id
 * @property int $permission_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Menu $menu
 * @property Permission $permission
 * @property Collection|Menu[] $menus
 *
 * @package App\Models
 */
class Menu extends Model
{
	public $guarded = ["id","created_at","updated_at"];
	protected $table = 'menu';

	protected $casts = [
		'ordinal' => 'int',
		'parent_id' => 'int',
		'permission_id' => 'int'
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

    public $timestamps=true;
    protected $primaryKey = "id";
    public $incrementing = true;
    public static function findRequested()
    {
        $query = Menu::query();

        // search results based on user input
        \Request::input('id') and $query->where('id',\Request::input('id'));
        \Request::input('name') and $query->where('name','like','%'.\Request::input('name').'%');
        \Request::input('url') and $query->where('url','like','%'.\Request::input('url').'%');
        \Request::input('icon') and $query->where('icon','like','%'.\Request::input('icon').'%');
        \Request::input('ordinal') and $query->where('ordinal',\Request::input('ordinal'));
        \Request::input('parent_id') and $query->where('parent_id',\Request::input('parent_id'));
        \Request::input('permission_id') and $query->where('permission_id',\Request::input('permission_id'));
        \Request::input('created_at') and $query->where('created_at',\Request::input('created_at'));
        \Request::input('updated_at') and $query->where('updated_at',\Request::input('updated_at'));

        // sort results
        \Request::input("sort") and $query->orderBy(\Request::input("sort"),\Request::input("sortType","asc"));

        // paginate results
        return $query->paginate(15);
    }

	// public function menu()
	// {
	// 	return $this->belongsTo(Menu::class, 'parent_id');
	// }

	// public function permission()
	// {
	// 	return $this->belongsTo(Permission::class);
	// }

	// public function menus()
	// {
	// 	return $this->hasMany(Menu::class, 'parent_id');
	// }

	public function parent()
    {
        return $this->belongsTo('App\Menu','parent_id');
    }

    public function setParentAttribute($parent) {
      unset($this->attributes['parent']);
    }

    public function permission()
    {
        return $this->belongsTo('App\Permission','permission_id');
    }

    public function setPermissionAttribute($permission) {
      unset($this->attributes['permission']);
    }
}
