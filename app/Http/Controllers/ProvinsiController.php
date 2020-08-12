<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Provinsi;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class ProvinsiController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-provinsi');
	}

	public function index()
  {
    $this->menuAccess(\Auth::user(),'Provinsi');
    return view('master.provinsi.index');
  }

    public function getData()
    {
    	$GLOBALS['nomor']=\Request::input('start',1)+1;
    	$dataList = Provinsi::select('*')->get();
    	if (request()->get('status') == 'trash') {
    		$dataList->onlyTrashed();
    	}
    	return DataTables::of($dataList)
    	->addColumn('nomor',function($kategori){
    		return $GLOBALS['nomor']++;
    	})
    	->addColumn('kode_provinsi',function($data){
    		if(isset($data->kode_provinsi)){
                return $data->kode_provinsi;
    		}else{
    			return null;
    		}
    	})
    	->addColumn('nama_provinsi',function($data){
    		if(isset($data->nama_provinsi)){
                return $data->nama_provinsi;
    		}else{
    			return null;
    		}
    	})
    	->addColumn('action', function ($data) {
    		$edit=$data->id;
    		$delete=url("provinsi/".$data->id)."/delete";
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
            'kode_provinsi'    => 'required',
            'nama_provinsi'    => 'required',
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
                return redirect('/provinsi');
            }
        }

        DB::beginTransaction();
        try {

             $data  = array(
             'kode_provinsi'    =>$all_data['kode_provinsi'] ,
             'nama_provinsi'    =>$all_data['nama_provinsi'] ,
           );

             $this->logCreatedActivity(Auth::user(),$data,'Provinsi','provinsi');
             $act=Provinsi::create($data);

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');


        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/provinsi');
    }

    public function edit(Request $request, $id)
    {
        $data=Provinsi::find($id);

        $this->menuAccess(\Auth::user(),'Provinsi (Edit)');
        return Response::json(array('data' => $data));  
    }

    public function update(Request $request)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'kode_provinsi'    => 'required',
            'nama_provinsi'    => 'required',
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
                return redirect('/provinsi');
            }
        }

        DB::beginTransaction();
        try {
            $get=Provinsi::find($all_data['id']);

            $data  = array(
               'kode_provinsi'    =>$all_data['kode_provinsi'] ,
               'nama_provinsi'    =>$all_data['nama_provinsi'] ,
           );

            $this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Provinsi','provinsi');

            $act=$get->update($data);

            message($act,'Data berhasil diupdate!','Data gagal diupdate!');

        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/provinsi');
  }

    public function destroy(Request $request,$kode)
    {
      $user=Provinsi::find($kode);
           $act=false;
           try {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Provinsi','Provinsi','provinsi');
               $act=$user->forceDelete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           } catch (\Exception $e) {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Provinsi','Provinsi','provinsi');
               $user=Provinsi::find($user->pk());
               $act=$user->delete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           }
    }

}
