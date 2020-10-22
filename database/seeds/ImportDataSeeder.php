<?php

use Symfony\Component\Console\Helper\ProgressBar;
use Illuminate\Database\Seeder;
use App\Imports\RawDataImport;
use Carbon\Carbon;
date_default_timezone_set("Asia/Jakarta");
use Maatwebsite\Excel\Facades\Excel;

class ImportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $this->importData();
    }

    private function importData()
    {
    	$this->command->info('Delete Data Penjualan');
    	DB::table('raw_data')->delete();
        if (env('DB_CONNECTION') == 'pgsql') {
           DB::statement("ALTER SEQUENCE raw_data_id_seq RESTART WITH 1");
       }
       else
       {
        DB::statement("ALTER TABLE raw_data AUTO_INCREMENT = 1");
        }
    	$fileName = 'data/data_import.xlsx';
    	$data = Excel::import(new RawDataImport, $fileName);
 
    	$this->command->info("Seeding Data Penjualan");

    	echo "\n\n";
    }

    public function createOrUpdate($formatted_array,$model) {
		$row = $model::find($formatted_array['id']);
		if ($row === null) {
			$this->logCreatedActivity(Auth::user(),$formatted_array,'Import Excel',with(new $model)->getTable());
			$model::firstOrCreate($formatted_array);
		} else {
			$this->logUpdatedActivity(Auth::user(),$row->getAttributes(),$formatted_array,'Import Excel',with(new $model)->getTable());
			$row->update($formatted_array);
		}
		$affected_row = $model::find($formatted_array['id']);
		return $affected_row;
	}
}
