<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\StatusPerkawinan;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class StatusPerkawinanController extends Controller
{
    //
	use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-status-perkawinan');
	}

	public function index()
	{
		$this->menuAccess(\Auth::user(),'Status Perkawinan');
		return view('master.status_perkawinan.index');
	}

	public function getData()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$dataList = StatusPerkawinan::select('*')->get();
		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('status_perkawinan',function($data){
			if(isset($data->status_perkawinan)){
				return $data->status_perkawinan;
			}else{
				return null;
			}
		})
		->addColumn('action', function ($data) {
			$edit=$data->id;
			$delete=url("status-perkawinan/".$data->id)."/delete";
			$content = '';
			$content.="<a href='#' onclick='ubah_data(\"$edit\")' class='btn btn-primary btn-sm' data-original-title='Edit' title='Edit'><i class='fa fa-edit' aria-hidden='true'></i></a>";
			$content.="<a href='#' onclick='hapus(\"$delete\")' class='btn btn-danger btn-sm' data-original-title='Hapus' title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i></a>";

			return $content;
		})
		->make(true);
	}

	public function store(Request $request)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'status_perkawinan'   => 'required',
        ]);


        if (!$validation->passes()){
            $count_err=count($validation->errors()->all());
            $i=0;
            foreach($validation->errors()->all() as $val)
            {
                message(false,'',$val);
                $i++;
            }
            if($count_err==$i)
            {
                return redirect('/status-perkawinan');
            }
        }

        DB::beginTransaction();
        try {

             $data  = array(
             'status_perkawinan'  => $all_data['status_perkawinan'] ,
           );

             $this->logCreatedActivity(Auth::user(),$data,'Status Perkawinan','status_perkawinan');
             $act=StatusPerkawinan::create($data);

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');


        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/status-perkawinan');
    }

    public function edit(Request $request, $id)
    {
    	$data=StatusPerkawinan::find($id);

    	$this->menuAccess(\Auth::user(),'Status Perkawinan (Edit)');
    	return Response::json(array('data' => $data));  
    }

    public function update(Request $request)
    {
    	$all_data=$request->all();

    	$validation = Validator::make($request->all(), [
    		'status_perkawinan'    => 'required',
    	]);


    	if (!$validation->passes()){
    		$count_err=count($validation->errors()->all());
    		$i=0;
    		foreach($validation->errors()->all() as $val)
    		{
    			message(false,'',$val);
    			$i++;
    		}
    		if($count_err==$i)
    		{
    			return redirect('/status-perkawinan');
    		}
    	}

    	DB::beginTransaction();
    	try {
    		$get=StatusPerkawinan::find($all_data['id']);

    		$data  = array(
    			'status_perkawinan'    =>$all_data['status_perkawinan'] ,
    		);

    		$this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Status Perkawinan','status_perkawinan');

    		$act=$get->update($data);

    		message($act,'Data berhasil diupdate!','Data gagal diupdate!');

    	} catch (Exception $e) {
    		echo 'Message' .$e->getMessage();
    		DB::rollback();
    	}
    	DB::commit();

    	return redirect('/status-perkawinan');
    }

    public function destroy(Request $request,$kode)
    {
      $user=StatusPerkawinan::find($kode);
           $act=false;
           try {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Status Perkawinan','Status Perkawinan','status_perkawinan');
               $act=$user->forceDelete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           } catch (\Exception $e) {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Status Perkawinan','Status Perkawinan','status_perkawinan');
               $user=StatusPerkawinan::find($user->pk());
               $act=$user->delete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           }
    }

}
