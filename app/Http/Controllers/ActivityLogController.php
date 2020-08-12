<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Http\Requests;
use Illuminate\Support\Facades\Schema;
use DatePeriod;
use DateTime;
use DateInterval;
use Carbon\Carbon;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
date_default_timezone_set("Asia/Jakarta");
use App\Traits\ActivityTraits;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    //
	use ActivityTraits;
	public $viewDir = "activity";

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-activity');
	}

	protected function view($view, $data = [])
	{
		return view($this->viewDir.".".$view, $data);
	}

	public function index()
	{
		return $this->view('index');
	}

	public function getData(Request $request)
	{
		$config = getConfigValues('ROLE_ADMIN');
    	$GLOBALS['nomor']=\Request::input('start',1)+1;
    	$dataList = Activity::select('*')->orderby('id','DESC');
    	if (request()->get('status') == 'trash') {
    		$dataList->onlyTrashed();
    	}
    	return DataTables::of($dataList)
    	->addColumn('nomor',function($kategori){
    		return $GLOBALS['nomor']++;
    	})
    	->addColumn('log_name',function($data){
    		if(isset($data->log_name)){
    			return $data->log_name;
    		}else{
    			return null;
    		}
    	})
    	->addColumn('created_at',function($data){
    		if(isset($data->created_at)){
    			return date('d-m-Y H:i:s',strtotime($data->created_at));
    		}else{
    			return null;
    		}
    	})
    	->addColumn('description',function($data){
    		if(isset($data->description)){
    			return $data->description;
    		}else{
    			return null;
    		}
    	})
    	->addColumn('causer_id',function($data){
    		if(isset($data->causer_id)){
    			return User::find($data->causer_id)->name;
    		}else{
    			return null;
    		}
    	})
    	->addColumn('action', function ($data) {

    		$jsonData = json_decode($data->properties, true);

    		$content = '';

    		$content.="&emsp;<table border='0' style='font-size:9pt'>
    		<tfoot>
    		<tr>
    		<td width='40%''><b>Access Type</b></td><td>".$jsonData['attributes']['type']."</td>
    		</tr>";

    		if(isset($jsonData['attributes']['description'])){
    			$content .="<tr>
    			<td><b>Description</b></td><td>".$jsonData['attributes']['description']."</td>
    			</tr>";
    		}
    		if(isset($jsonData['attributes']['menu'])){
    			$content .="<tr>
    			<td><b>Menu</b></td><td>".$jsonData['attributes']['menu']."</td>
    			</tr>";
    		}
    		if(isset($jsonData['attributes']['table'])){
    			$content .="<tr>
    			<td><b>Table</b></td><td>".$jsonData['attributes']['table']."</td>
    			</tr>";
    		}
    		if(isset($jsonData['attributes']['data'])){

    			$content .="<tr>
    			<td><b>Data</b></td><td><pre>".json_encode($jsonData['attributes']['data'],JSON_PRETTY_PRINT)."</pre></td>
    			</tr>";
    		}
    		if(isset($jsonData['attributes']['device'])){
    			$content .="<tr>
    			<td><b>Device</b></td><td>".$jsonData['attributes']['device']."</td>
    			</tr>";
    		}
    		if(isset($jsonData['attributes']['browser'])){
    			$content .="<tr>
    			<td><b>Browser</b></td><td>".$jsonData['attributes']['browser']."</td>
    			</tr>";
    		}
    		$content .="</tfoot></table>";

    		return $content;
    	})
    	->make(true);
	}

}
