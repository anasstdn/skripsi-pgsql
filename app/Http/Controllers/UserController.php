<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RoleUser;
use App\Models\Profile;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-user');
	}

	public function index()
    {
        $role=\App\Role::select(\DB::raw("*"))->get();

        $this->menuAccess(\Auth::user(),'Manajemen Pengguna');
        return view('acl.user.index',compact('role'));
    }

    public function getData()
    {
    	$config = getConfigValues('ROLE_ADMIN');
    	$GLOBALS['nomor']=\Request::input('start',1)+1;
    	$dataList = User::select('*');
    	if (request()->get('status') == 'trash') {
    		$dataList->onlyTrashed();
    	}
    	return DataTables::of($dataList)
    	->addColumn('nomor',function($kategori){
    		return $GLOBALS['nomor']++;
    	})
    	->addColumn('role',function($data){
    		if(isset($data->roleUser->role->display_name)){
                if(in_array($data->roleUser->role_id,getConfigValues('ROLE_ADMIN')))
                {
                    return array('id'=>1,'role'=>$data->roleUser->role->display_name);
                }
                else
                {
                    return array('role'=>$data->roleUser->role->display_name);
                }
    		}else{
    			return null;
    		}
    	})
    	->addColumn('status', function ($data) {
    		if(isset($data->status_aktif))
    		{
                if($data->status_aktif==1)
                {
                    return array('url1'=>url("user/".$data->id)."/nonaktifkan",'status_aktif'=>$data->status_aktif);
                }
                else
                {
                    return array('url1'=>url("user/".$data->id)."/aktifkan",'status_aktif'=>$data->status_aktif);
                }
    			
    		}else
    		{
    			return null;
    		}
    	})
    	->addColumn('action', function ($data) use ($config) {
    		$edit=$data->id;
    		$delete=url("user/hapus/".$data->id);
    		$reset=url("user/".$data->id)."/reset";
    		$content = '';
    		if (!in_array($data->id, $config)) {
                $content.="<a href='#' onclick='ubah_data(\"$edit\")' class='btn btn-primary btn-sm' data-original-title='Edit' title='Edit'><i class='fa fa-edit' aria-hidden='true'></i></a>";
                $content.="<a href='#' onclick='reset(\"$reset\")' class='btn btn-info btn-sm reset-password' data-original-title='Reset Password' title='Reset Password'><i class='fa fa-key' aria-hidden='true'></i></a>";
                $content.="<a href='#' onclick='hapus(\"$delete\")' class='btn btn-danger btn-sm' data-original-title='Hapus' title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i></a>";
    		}

    		return $content;
    	})
    	->make(true);
    }

    public function edit(Request $request, $id)
    {
        $data=User::select(\DB::raw('users.*,role_user.role_id'))
        ->join('role_user','role_user.user_id','=','users.id')
        ->where('users.id',$id)->first();

        $this->menuAccess(\Auth::user(),'Manajemen Pengguna (Edit)');
        return Response::json(array('data' => $data));  
    }

    public function update(Request $request)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'nama'    => 'required',
            'username'      => 'required',
            'email'         => 'required',
            'roles'         => 'required',
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
                return redirect('/user');
            }
        }

        DB::beginTransaction();
        try {
            $user=User::find($all_data['id']);
            
            if(!empty($all_data['password']))
            {
                $dataUser  = array(
                 'name'         =>ucwords(strtolower($all_data['nama'])),
                 'username'     =>$all_data['username'] ,
                 'email'        =>$all_data['email'] ,
                 'password'      =>bcrypt($all_data['password']) ,
               );
            }
            else
            {
                $dataUser  = array(
                 'name'         =>ucwords(strtolower($all_data['nama'])),
                 'username'     =>$all_data['username'] ,
                 'email'        =>$all_data['email'] ,
               );
            }
            $this->logUpdatedActivity(Auth::user(),$user->getAttributes(),$dataUser,'Manajemen Pengguna','users');

            $act=$user->update($dataUser);

            if(!empty(RoleUser::where('user_id',$all_data['id'])->first())){
            $this->logDeletedActivity(RoleUser::where('user_id',$all_data['id'])->first(),'Hapus Role User user_id='.$all_data['id'].'','Manajemen Pengguna','role_user');
            }

            $delRoleUser=RoleUser::where('user_id',$all_data['id'])->forceDelete();

            $this->logCreatedActivity(Auth::user(),[
               'role_id'=>intval($all_data['roles']),
               'user_id'=>$user->id,
               'user_type'=>'App\User'
             ],'Manajemen Pengguna','role_user');

            $role=array(
             'role_id'  =>intval($all_data['roles']),
             'user_id'  =>$user->id,
             'user_type'=>'App\User'
            );

            $roleUser = DB::table('role_user')->insert($role);

            if($user==true && $roleUser==true)
            {
                $act=true;
            }
            else
            {
                $act=false;
            }

            message($act,'Data berhasil diupdate!','Data gagal diupdate!');

            } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/user');
    }

    public function store(Request $request)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'nama'    => 'required',
            'username'      => 'required',
            'email'         => 'required',
            'roles'         => 'required',
            'password'      => 'required',
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
                return redirect('/user');
            }
        }

        DB::beginTransaction();
        try {

             $data  = array(
             'name'         =>ucwords(strtolower($all_data['nama'])),
             'username'     =>$all_data['username'] ,
             'email'        =>$all_data['email'] ,
             'password'     =>bcrypt($all_data['password']) ,
           );

             $this->logCreatedActivity(Auth::user(),$data,'Manajemen Pengguna','users');
             $user=User::create($data);

              $role=array(
             'role_id'  =>intval($all_data['roles']),
             'user_id'  =>$user->id,
             'user_type'=>'App\User'
            );

              $this->logCreatedActivity(Auth::user(),[
               'role_id'=>intval($all_data['roles']),
               'user_id'=>$user->id,
               'user_type'=>'App\User'
             ],'Manajemen Pengguna','role_user');

            $roleUser = DB::table('role_user')->insert($role);

            if($user==true && $roleUser==true)
            {
                $act=true;
            }
            else
            {
                $act=false;
            }

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');


        } catch (Exception $e) {
          echo 'Message' .$e->getMessage();
          DB::rollback();
      }
      DB::commit();

      return redirect('/user');
    }

    public function reset(Request $request, $kode)
    {
      $user=User::find($kode);
      $act=false;
      try {
         $dat=array(
            'password'=>bcrypt('12345678'),
        );
         $reset=$user->update($dat);
         message($reset,'Data berhasil disimpan!','Data gagal disimpan!');
     } catch (\Exception $e) {
         $dat=array(
            'password'=>bcrypt('12345678'),
        );
         $reset=$user->update($dat);
         message($reset,'Data berhasil disimpan!','Data gagal disimpan!');
     }
 }

 public function hapus($id)
 {
    $data = User::find($id);
    $this->logDeletedActivity($data,'Delete data id='.$id.' di menu Manajemen User','Mamajemen User','users');
    $data->delete();

    message($data,'Data berhasil dihapus!','Data gagal dihapus!');
}

    public function destroy(Request $request,$kode)
    {
      $user=User::find($kode);
           $act=false;
           try {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Manajemen Pengguna','Manajemen Pengguna','users');
               $act=$user->forceDelete();
               $delRoleUser=RoleUser::where('user_id',$kode)->forceDelete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           } catch (\Exception $e) {
               $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Manajemen Pengguna','Manajemen Pengguna','users');
               $user=User::find($user->pk());
               $act=$user->delete();
               $delRoleUser=RoleUser::where('user_id',$kode)->delete();
               message($act,'Data berhasil dihapus!','Data gagal dihapus!');
           }
    }

    public function checkUsername(Request $request)
    {
        $all_data = $request->all();

        switch($all_data['mode'])
        {
          case 'add':
          if (env('DB_CONNECTION') == 'pgsql') {
              $query="SELECT * FROM users WHERE username ILIKE '%".trim($all_data['username'])."%' LIMIT 1"; 
          }
          else
          {
            $query="SELECT * FROM users WHERE username LIKE '%".trim($all_data['username'])."%' LIMIT 1"; 
        }
        break;
        case 'edit':
        if (env('DB_CONNECTION') == 'pgsql') {
          $query="SELECT * FROM users WHERE username ILIKE '%".trim($all_data['username'])."%' AND `id` <> '".$all_data['id']."' LIMIT 1"; 
      }
      else{
       $query="SELECT * FROM users WHERE username LIKE '%".trim($all_data['username'])."%' AND `id` <> '".$all_data['id']."' LIMIT 1"; 
   }

   break;
}
$cek=DB::select($query);
if($cek==true) {
    return Response::json(array('msg' => 'true'));
}
return Response::json(array('msg' => 'false'));  
}

public function checkEmail(Request $request)
{
    $all_data = $request->all();
    switch($all_data['mode'])
    {
      case 'add':
      if (env('DB_CONNECTION') == 'pgsql') {
          $query="SELECT * FROM users WHERE email ILIKE '%".trim($all_data['email'])."%' LIMIT 1";
      }
      else{
        $query="SELECT * FROM users WHERE email LIKE '%".trim($all_data['email'])."%' LIMIT 1";
    } 
    break;
    case 'edit':
    if (env('DB_CONNECTION') == 'pgsql') {
      $query="SELECT * FROM users WHERE email ILIKE '%".trim($all_data['email'])."%' AND `id` <> '".$all_data['id']."' LIMIT 1"; 
  }
  else{
    $query="SELECT * FROM users WHERE email LIKE '%".trim($all_data['email'])."%' AND `id` <> '".$all_data['id']."' LIMIT 1"; 
}

break;
}
$cek=DB::select($query);
if($cek==true) {
    return Response::json(array('msg' => 'true'));
}
return Response::json(array('msg' => 'false'));  
}

public function nonaktifkan(Request $request,$kode)
{
     $user=User::find($kode);
      $act=false;
      try {
         $dat=array(
            'status_aktif'=>0,
        );
         $reset=$user->update($dat);
         message($reset,'Data berhasil disimpan!','Data gagal disimpan!');
     } catch (\Exception $e) {
         $dat=array(
            'status_aktif'=>0,
        );
         $reset=$user->update($dat);
         message($reset,'Data berhasil disimpan!','Data gagal disimpan!');
     }
}

public function aktifkan(Request $request,$kode)
{
    $user=User::find($kode);
      $act=false;
      try {
         $dat=array(
            'status_aktif'=>1,
        );
         $reset=$user->update($dat);
         message($reset,'Data berhasil disimpan!','Data gagal disimpan!');
     } catch (\Exception $e) {
         $dat=array(
            'status_aktif'=>1,
        );
         $reset=$user->update($dat);
         message($reset,'Data berhasil disimpan!','Data gagal disimpan!');
     }
}

}
