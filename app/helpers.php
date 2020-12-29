<?php 

function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
{
    /*
    $interval can be:
    yyyy - Number of full years
    q    - Number of full quarters
    m    - Number of full months
    y    - Difference between day numbers
           (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d    - Number of full days
    w    - Number of full weekdays
    ww   - Number of full weeks
    h    - Number of full hours
    n    - Number of full minutes
    s    - Number of full seconds (default)
    */

    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto   = strtotime($dateto, 0);
    }

    $difference        = $dateto - $datefrom; // Difference in seconds
    $months_difference = 0;

    switch ($interval) {
        case 'yyyy': // Number of full years
            $years_difference = floor($difference / 31536000);
            if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                $years_difference--;
            }

            if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                $years_difference++;
            }

            $datediff = $years_difference;
        break;

        case "q": // Number of full quarters
            $quarters_difference = floor($difference / 8035200);

            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }

            $quarters_difference--;
            $datediff = $quarters_difference;
        break;

        case "m": // Number of full months
            $months_difference = floor($difference / 2678400);

            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }

            $months_difference--;

            $datediff = $months_difference;
        break;

        case 'y': // Difference between day numbers
            $datediff = date("z", $dateto) - date("z", $datefrom);
        break;

        case "d": // Number of full days
            $datediff = floor($difference / 86400);
        break;

        case "w": // Number of full weekdays
            $days_difference  = floor($difference / 86400);
            $weeks_difference = floor($days_difference / 7); // Complete weeks
            $first_day        = date("w", $datefrom);
            $days_remainder   = floor($days_difference % 7);
            $odd_days         = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?

            if ($odd_days > 7) { // Sunday
                $days_remainder--;
            }

            if ($odd_days > 6) { // Saturday
                $days_remainder--;
            }

            $datediff = ($weeks_difference * 5) + $days_remainder;
        break;

        case "ww": // Number of full weeks
            $datediff = floor($difference / 604800);
        break;

        case "h": // Number of full hours
            $datediff = floor($difference / 3600);
        break;

        case "n": // Number of full minutes
            $datediff = floor($difference / 60);
        break;

        default: // Number of full seconds (default)
            $datediff = $difference;
        break;
    }

    return $datediff;
}

function test_api($username,$password)
{
    $data = [
        'username' => $username,
        'password' => $password,
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "lumen.test/api/v1/login",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
        // Set here requred headers
            "accept: */*",
            "accept-language: en-US,en;q=0.8",
            "content-type: application/json",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        return json_decode($response);
    }
}

function get_data_with_param($data, $token, $url)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
        // Set here requred headers
            "accept: */*",
            "accept-language: en-US,en;q=0.8",
            "content-type: application/json",
            'Authorization: Bearer '.$token
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        return json_decode($response);
    }
}

function api_get($url,$token)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
        // Set Here Your Requesred Headers
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        dd(json_decode($response));
    }
}

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

function generate_start_date($year,$week_number)
{
    $week_start = new DateTime();
    $week_start->setISODate($year,$week_number);
    return $week_start->format('d-m-Y');
}

function getIsoWeeksInYear($year) {
    $date = new DateTime;
    $date->setISODate($year, 53);
    return ($date->format("W") === "53" ? 53 : 52);
}

?>