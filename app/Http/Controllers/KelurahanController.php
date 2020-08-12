<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class KelurahanController extends Controller
{
    //
     use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-kelurahan');
	}

	public function index()
	{
		$kecamatan=Kecamatan::get();
		$this->menuAccess(\Auth::user(),'Kelurahan');
		return view('master.kelurahan.index',compact('kecamatan'));
	}

	public function getData()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$dataList = Kelurahan::select(\DB::raw('kelurahan.id,kelurahan.kode_kelurahan,kelurahan.nama_kelurahan,kecamatan.nama_kecamatan,kabupaten.nama_kabupaten,provinsi.nama_provinsi,kelurahan.kodepos'))
		->join('kecamatan','kecamatan.id','=','kelurahan.id_kecamatan')
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
		->addColumn('kode_kelurahan',function($data){
			if(isset($data->kode_kelurahan)){
				return $data->kode_kelurahan;
			}else{
				return null;
			}
		})
		->addColumn('nama_kelurahan',function($data){
			if(isset($data->nama_kelurahan)){
				return $data->nama_kelurahan;
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
		->addColumn('kodepos',function($data){
			if(isset($data->kodepos)){
				return $data->kodepos;
			}else{
				return null;
			}
		})
		->addColumn('action', function ($data) {
			$edit=$data->id;
			$delete=url("kelurahan/".$data->id)."/delete";
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
            'kode_kelurahan'    => 'required',
            'nama_kelurahan'    => 'required',
            'id_kecamatan'    	=> 'required',
            'kodepos'    		=> 'required',
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
                return redirect('/kelurahan');
            }
        }

        DB::beginTransaction();
        try {

             $data  = array(
             'kode_kelurahan'    =>$all_data['kode_kelurahan'] ,
             'nama_kelurahan'    =>$all_data['nama_kelurahan'] ,
             'id_kecamatan'    	 =>$all_data['id_kecamatan'] ,
             'kodepos'    	 	 =>$all_data['kodepos'] ,
           );

             $this->logCreatedActivity(Auth::user(),$data,'Kelurahan','kelurahan');
             $act=Kelurahan::create($data);

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');


        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/kelurahan');
    }

    public function edit(Request $request, $id)
    {
        $data=Kelurahan::find($id);

        $this->menuAccess(\Auth::user(),'Kelurahan (Edit)');
        return Response::json(array('data' => $data));  
    }

     public function update(Request $request)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'kode_kelurahan'    => 'required',
            'nama_kelurahan'    => 'required',
            'id_kecamatan'    	=> 'required',
            'kodepos'    		=> 'required',
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
                return redirect('/kelurahan');
            }
        }

        DB::beginTransaction();
        try {
            $get=Kelurahan::find($all_data['id']);

            $data  = array(
             'kode_kelurahan'    =>$all_data['kode_kelurahan'] ,
             'nama_kelurahan'    =>$all_data['nama_kelurahan'] ,
             'id_kecamatan'    	 =>$all_data['id_kecamatan'] ,
             'kodepos'    	 	 =>$all_data['kodepos'] ,
           );

            $this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Kelurahan','kelurahan');

            $act=$get->update($data);

            message($act,'Data berhasil diupdate!','Data gagal diupdate!');

        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/kelurahan');
  }

  public function destroy(Request $request,$kode)
  {
  	$user=Kelurahan::find($kode);
  	$act=false;
  	try {
  		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Kelurahan','Kelurahan','kelurahan');
  		$act=$user->forceDelete();
  		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
  	} catch (\Exception $e) {
  		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Kelurahan','Kelurahan','kelurahan');
  		$user=Kelurahan::find($user->pk());
  		$act=$user->delete();
  		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
  	}
  }

}
