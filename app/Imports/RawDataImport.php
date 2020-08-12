<?php
  
namespace App\Imports;
  
use App\Models\RawDatum;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
date_default_timezone_set("Asia/Jakarta");
  
class RawDataImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new RawDatum([
            'tgl_transaksi' => Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]))->format('Y-m-d'),
            'no_nota' => isset($row[1])?$row[1]:null,
            'pasir'=>isset($row[2])?$row[2]:null,
            'gendol'=>isset($row[3])?$row[3]:null,
            'abu'=>isset($row[4])?$row[4]:null,
            'split2_3'=>isset($row[5])?$row[5]:null,
            'split1_2'=>isset($row[6])?$row[6]:null,
            'lpa'=>isset($row[7])?$row[7]:null,
            'campur'=>isset($row[8])?$row[8]:null,
        ]);
    }
}