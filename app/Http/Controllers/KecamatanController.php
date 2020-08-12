<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class KecamatanController extends Controller
{
    //
  use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-kecamatan');
	}

	public function index()
	{
		$kabupaten=Kabupaten::get();
		$this->menuAccess(\Auth::user(),'Kecamatan');
		return view('master.kecamatan.index',compact('kabupaten'));
	}

	public function getData()
    {
    	$GLOBALS['nomor']=\Request::input('start',1)+1;
    	$dataList = Kecamatan::select(\DB::raw('kecamatan.id,kecamatan.kode_kecamatan,kecamatan.nama_kecamatan,kabupaten.nama_kabupaten,provinsi.nama_provinsi'))
    	->join('kabupaten','kabupaten.id','=','kecamatan.id_kabupaten')
    	->join('provinsi','provinsi.id','=','kabupaten.id_provinsi')
    	->get();

    	if (request()->get('status') == 'trash') {
    		$dataList->onlyTrashed();
    	}
    	return DataTables::of($dataList)
    	->addColumn('nomor',function($kategori){
    		return $GLOBALS['nomor']++;
    	})
    	->addColumn('kode_kecamatan',function($data){
    		if(isset($data->kode_kecamatan)){
                return $data->kode_kecamatan;
    		}else{
    			return null;
    		}
    	})
    	->addColumn('nama_kecamatan',function($data){
    		if(isset($data->nama_kecamatan)){
                return $data->nama_kecamatan;
    		}else{
    			return null;
    		}
    	})
    	->addColumn('nama_kabupaten',function($data){
    		if(isset($data->nama_kabupaten)){
                return $data->nama_kabupaten;
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
    		$delete=url("kecamatan/".$data->id)."/delete";
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
            'kode_kecamatan'    => 'required',
            'nama_kecamatan'    => 'required',
            'id_kabupaten'    	=> 'required',
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
                return redirect('/kecamatan');
            }
        }

        DB::beginTransaction();
        try {

             $data  = array(
             'kode_kecamatan'    =>$all_data['kode_kecamatan'] ,
             'nama_kecamatan'    =>$all_data['nama_kecamatan'] ,
             'id_kabupaten'    	 =>$all_data['id_kabupaten'] ,
           );

             $this->logCreatedActivity(Auth::user(),$data,'Kecamatan','kecamatan');
             $act=Kecamatan::create($data);

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');


        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/kecamatan');
    }

    public function edit(Request $request, $id)
    {
        $data=Kecamatan::find($id);

        $this->menuAccess(\Auth::user(),'Kecamatan (Edit)');
        return Response::json(array('data' => $data));  
    }

    public function update(Request $request)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'kode_kecamatan'    => 'required',
            'nama_kecamatan'    => 'required',
            'id_kabupaten'    	=> 'required',
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
                return redirect('/kecamatan');
            }
        }

        DB::beginTransaction();
        try {
            $get=Kecamatan::find($all_data['id']);

            $data  = array(
             'kode_kecamatan'    =>$all_data['kode_kecamatan'] ,
             'nama_kecamatan'    =>$all_data['nama_kecamatan'] ,
             'id_kabupaten'    	 =>$all_data['id_kabupaten'] ,
           );

            $this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Kecamatan','kecamatan');

            $act=$get->update($data);

            message($act,'Data berhasil diupdate!','Data gagal diupdate!');

        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/kecamatan');
  }

    public function destroy(Request $request,$kode)
    {
      $user=Kecamatan::find($kode);
           $act=false;
           try {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Kecamatan','Kecamatan','kecamatan');
               $act=$user->forceDelete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           } catch (\Exception $e) {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Kecamatan','Kecamatan','kecamatan');
               $user=Kecamatan::find($user->pk());
               $act=$user->delete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           }
    }
}
