<?php 

function getConfigValues($configName){
    return \App\Models\ConfigId::getValues($configName);
}

function message($isSuccess,$successMessage="Data has been saved",$failedMessage="Failed to save data")
{
    if($isSuccess){
        Session::flash('message',$successMessage);
    } else {
        Session::flash('message',$failedMessage);
    }

    Session::flash('messageType',$isSuccess ? 'sukses' : 'gagal');
}

function date_indo($tgl)
{
    $ubah = gmdate($tgl, time()+60*60*8);
    $pecah = explode("-",$ubah);
    $tanggal = $pecah[2];
    $bulan = bulan($pecah[1]);
    $tahun = $pecah[0];
    return $tanggal.' '.$bulan.' '.$tahun;
}

function nama_hari($tgl)
{
    $hari=date('D',strtotime($tgl));
    switch($hari){
        case 'Sun':
        $hari_ini = "Minggu";
        break;
        
        case 'Mon':         
        $hari_ini = "Senin";
        break;
        
        case 'Tue':
        $hari_ini = "Selasa";
        break;
        
        case 'Wed':
        $hari_ini = "Rabu";
        break;
        
        case 'Thu':
        $hari_ini = "Kamis";
        break;
        
        case 'Fri':
        $hari_ini = "Jumat";
        break;
        
        case 'Sat':
        $hari_ini = "Sabtu";
        break;
        
        default:
        $hari_ini = "Tidak di ketahui";     
        break;
    }
    return $hari_ini;
}

function bulan($bln)
{
    switch ($bln)
    {
        case 1:
        return "Januari";
        break;
        case 2:
        return "Februari";
        break;
        case 3:
        return "Maret";
        break;
        case 4:
        return "April";
        break;
        case 5:
        return "Mei";
        break;
        case 6:
        return "Juni";
        break;
        case 7:
        return "Juli";
        break;
        case 8:
        return "Agustus";
        break;
        case 9:
        return "September";
        break;
        case 10:
        return "Oktober";
        break;
        case 11:
        return "November";
        break;
        case 12:
        return "Desember";
        break;
    }
}

function cek_kelengkapan_data()
{
    $sel_all_column_name= DB::getSchemaBuilder()->getColumnListing('profile');

    $cek_kelengkapan_data=App\Models\User::select(\DB::raw('profile.*'))->leftjoin('profile','profile.id','=','users.id_profile')->where('users.id',Auth::user()->id)->first()->toArray();

    $var_check=array(
    	'nama_depan',    		
    	'nama_belakang', 		
    	'nik',      				
    	'id_jenis_kelamin', 		
    	'id_agama',         		
    	'tempat_lahir',      	
    	'tgl_lahir',    			
    	'id_status_perkawinan', 	
    	'alamat_ktp',         	
    	'id_kelurahan_ktp',      
    	'alamat_domisili',      
    	'id_kelurahan_domisili',
    	'no_telp',         		
    );

    
    for($i=0;$i<count($sel_all_column_name);$i++)
    {
      if(in_array($sel_all_column_name[$i], $var_check))
      {
        $cek[$sel_all_column_name[$i]]=isset($cek_kelengkapan_data[$sel_all_column_name[$i]]) && !empty($cek_kelengkapan_data[$sel_all_column_name[$i]])?$cek_kelengkapan_data[$sel_all_column_name[$i]]:null;
      }
      
    }

    if(in_array(null,$cek,true))
    {
     $pesan='';
     $pesan.='Pengguna '.strtoupper(strtolower (Auth::user()->name)).'';
    //  foreach ($cek as $key => $value) {
    //   if (is_null($value) === true) {
    //     $pesan.='<br>'.$key;
    //   }
    // }
    $pesan.='<br>Silahkan lengkapi data diri anda terlebih dahulu. Terima Kasih.';
    $data['status']=true;
    $data['pesan']=$pesan;
  }
  else
  {
    $data['status']=false;
  }
  return $data;
}

function week_number($n)
{
    $w = date('w', $n);
    return 1 + date('z', $n + (6 - $w) * 24 * 3600) / 7;
}

function column_name($name)
{
    switch($name)
    {
        case 'pasir':
        return 'Pasir';
        break;
        case 'gendol':
        return 'Gendol';
        break;
        case 'abu':
        return 'Abu';
        break;
        case 'split2_3':
        return 'Split 2/3';
        break;
        case 'split1_2':
        return 'Split 1/2';
        break;
        case 'lpa':
        return 'LPA';
        break;
    }
}


?>