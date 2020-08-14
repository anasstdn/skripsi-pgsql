<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Cache;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CacheUpdater;
use Illuminate\Database\Eloquent\SoftDeletes; //add this line

/**
 * Class ConfigId
 * 
 * @property int $id
 * @property character varying|null $config_name
 * @property character varying|null $table_source
 * @property character varying|null $config_value
 * @property character varying|null $description
 * @property timestamp without time zone|null $created_at
 * @property timestamp without time zone|null $updated_at
 *
 * @package App\Models
 */
class ConfigId extends Model
{
	use CacheUpdater;
	use SoftDeletes;
	protected $table = 'config_ids';

	protected $casts = [
		'config_name' => 'character varying',
		'table_source' => 'character varying',
		'config_value' => 'character varying',
		'description' => 'character varying',
		'created_at' => 'timestamp without time zone',
		'updated_at' => 'timestamp without time zone'
	];

	protected $fillable = [
		'config_name',
		'table_source',
		'config_value',
		'description'
	];

	protected $dates =['deleted_at'];

	public static function getValues($configName){
		$configs = Cache::remember('config_ids_'.$configName,120, function() use($configName)
		{
			$temp = ConfigId::select('config_value')->where('config_name',$configName)->first();
			if($temp==null)return null;
			return explode(',',$temp->config_value);
		});

		return $configs;
	}

	public static function getDate($configName)
	{
		$configs = Cache::remember('config_ids_'.$configName,120, function() use($configName)
		{
			$temp = ConfigId::select('config_value')->where('config_name',$configName)->first();
			if($temp==null)return null;
			return explode(',',$temp->config_value);
		});

		return $configs;
	}

	public static function getValfromDB($configName)
	{
		$configs = Cache::remember('config_ids_'.$configName,120, function() use($configName)
		{
			$temp = ConfigId::select('*')->where('config_name',$configName)->first();

			if($temp==null)
				{return null;}
			else
			{
				$data['table_source']=explode(',',$temp->table_source);
				$data['config_value']=explode(',',$temp->config_value);
				return $data;
			}

		});

		return $configs;
	}

	private function updateCache(){
		Cache::forget('config_ids_'.$this->config_name);

		return self::getValues($this->config_name);
	}

	private function updateCache1(){
		Cache::forget('config_ids_'.$this->config_name);

		return self::getDate($this->config_name);
	}

	private function updateCache2(){
		Cache::forget('config_ids_'.$this->config_name);

		return self::getValfromDB($this->config_name);
	}
}
