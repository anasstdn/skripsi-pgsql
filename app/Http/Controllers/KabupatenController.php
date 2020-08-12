<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class KabupatenController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-kabupaten');
	}

	public function index()
	{
		$provinsi=Provinsi::get();
		$this->menuAccess(\Auth::user(),'Kabupaten');
		return view('master.kabupaten.index',compact('provinsi'));
	}

	public function getData()
    {
    	$GLOBALS['nomor']=\Request::input('start',1)+1;
    	$dataList = Kabupaten::select(\DB::raw('kabupaten.id,kabupaten.kode_kabupaten,kabupaten.nama_kabupaten,provinsi.nama_provinsi'))->join('provinsi','provinsi.id','=','kabupaten.id_provinsi')->get();
    	if (request()->get('status') == 'trash') {
    		$dataList->onlyTrashed();
    	}
    	return DataTables::of($dataList)
    	->addColumn('nomor',function($kategori){
    		return $GLOBALS['nomor']++;
    	})
    	->addColumn('kode_kabupaten',function($data){
    		if(isset($data->kode_kabupaten)){
                return $data->kode_kabupaten;
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
    		$delete=url("kabupaten/".$data->id)."/delete";
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
            'kode_kabupaten'    => 'required',
            'nama_kabupaten'    => 'required',
            'id_provinsi'    	=> 'required',
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
                return redirect('/kabupaten');
            }
        }

        DB::beginTransaction();
        try {

             $data  = array(
             'kode_kabupaten'    =>$all_data['kode_kabupaten'] ,
             'nama_kabupaten'    =>$all_data['nama_kabupaten'] ,
             'id_provinsi'    	 =>$all_data['id_provinsi'] ,
           );

             $this->logCreatedActivity(Auth::user(),$data,'Kabupaten','kabupaten');
             $act=Kabupaten::create($data);

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');


        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/kabupaten');
    }

    public function edit(Request $request, $id)
    {
        $data=Kabupaten::find($id);

        $this->menuAccess(\Auth::user(),'Kabupaten (Edit)');
        return Response::json(array('data' => $data));  
    }

    public function update(Request $request)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'kode_kabupaten'    => 'required',
            'nama_kabupaten'    => 'required',
            'id_provinsi'    	=> 'required',
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
                return redirect('/kabupaten');
            }
        }

        DB::beginTransaction();
        try {
            $get=Kabupaten::find($all_data['id']);

            $data  = array(
             'kode_kabupaten'    =>$all_data['kode_kabupaten'] ,
             'nama_kabupaten'    =>$all_data['nama_kabupaten'] ,
             'id_provinsi'    	 =>$all_data['id_provinsi'] ,
           );

            $this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Kabupaten','kabupaten');

            $act=$get->update($data);

            message($act,'Data berhasil diupdate!','Data gagal diupdate!');

        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/kabupaten');
  }

    public function destroy(Request $request,$kode)
    {
      $user=Kabupaten::find($kode);
           $act=false;
           try {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Kabupaten','Kabupaten','kabupaten');
               $act=$user->forceDelete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           } catch (\Exception $e) {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Kabupaten','Kabupaten','Kabupaten');
               $user=Kabupaten::find($user->pk());
               $act=$user->delete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           }
    }
}
