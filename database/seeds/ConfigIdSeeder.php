<?php

use Illuminate\Database\Seeder;
use App\Models\ConfigId;

class ConfigIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->configId();
    }

    private function configId()
    {
    	$this->command->info("Hapus Config IDS");
    	DB::table('config_ids')->delete();

    	$this->command->info("Simpan Config IDS");
    	$data=array(

    		[
    			'table_source'=>'roles',
    			'config_name'=>'ROLE_ADMIN',
    			'config_value'=>'1,2',
    			'description'=>'Role yang tercatat sebagai Admin',
    		],
    		[
    			'table_source'=>'roles',
    			'config_name'=>'ROLE_DEVELOPER',
    			'config_value'=>1,
    			'description'=>'Role untuk Developer',
    		],
    		[
    			'table_source'=>'roles',
    			'config_name'=>'ROLE_SUPERADMINISTRATOR',
    			'config_value'=>2,
    			'description'=>'Role untuk Superadministrator',
    		],
    		[
    			'table_source'=>'roles',
    			'config_name'=>'ROLE_MANAGER',
    			'config_value'=>3,
    			'description'=>'Role untuk Manager',
    		],
            [
                'table_source'=>'roles',
                'config_name'=>'ROLE_FRONT_OFFICE',
                'config_value'=>4,
                'description'=>'Role untuk FO',
            ],
    		[
    			'table_source'=>'',
    			'config_name'=>'Y-m-d',
    			'config_value'=>'Y-m-d',
    			'description'=>'Date Y-m-d format',
    		],
    		[
    			'table_source'=>'',
    			'config_name'=>'d-m-Y',
    			'config_value'=>'d-m-Y',
    			'description'=>'Date d-m-Y format',
    		],
    		[
    			'table_source'=>'',
    			'config_name'=>'Ymd',
    			'config_value'=>'Ymd',
    			'description'=>'Date ymd format',
    		],
    		[
    			'table_source'=>'',
    			'config_name'=>'dmY',
    			'config_value'=>'dmY',
    			'description'=>'Date dmy format',
    		],
    		[
    			'table_source'=>'',
    			'config_name'=>'d/m/Y',
    			'config_value'=>'d/m/Y',
    			'description'=>'Date d/m/y format',
    		],
    		[
    			'table_source'=>'',
    			'config_name'=>'Y/m/d',
    			'config_value'=>'Y/m/d',
    			'description'=>'Date y/m/d format',
    		],
    	);

if(DB::table('config_ids')->get()->count() == 0){
	$bar=$this->command->getOutput()->createProgressBar(count($data));
	foreach($data as $a){
		ConfigId::create($a);
		$bar->advance();
	}
	$bar->finish();
}
}
}
