<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Menu;
use App\Permission;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $this->command->info('Delete semua tabel menu');
    	Model::unguard();
    	Menu::truncate();
    	$this->menuHome();
    	$this->menuAcl();
        $this->menuActivity();
        $this->menuMaster();
        $this->menuPegawai();
        $this->menuAnggota();
        $this->menuTransaksi();
    }

    private function menuHome()
    {
    	$this->command->info('Menu Home Seeder');
    	$permission = Permission::firstOrNew(array(
    		'name'=>'read-home-menu'
    	));
    	$permission->display_name = 'Read Home Menus';
    	$permission->save();
    	$menu = Menu::firstOrNew(array(
    		'name'=>'Beranda',
    		'permission_id'=>$permission->id,
    		'ordinal'=>1,
    		'parent_status'=>'N',
    		'url'=>'home',
    	));
    	$menu->icon = 'si-home';
    	$menu->save();
    }

    private function menuAcl(){
    	$this->command->info('Menu ACL Seeder');
    	$permission = Permission::firstOrNew(array(
    		'name'=>'read-acl-menu'
    	));
    	$permission->display_name = 'Read ACL Menus';
    	$permission->save();
    	$menu = Menu::firstOrNew(array(
    		'name'=>'Pengaturan ACL',
    		'permission_id'=>$permission->id,
    		'ordinal'=>1,
    		'parent_status'=>'Y'
    	));
    	$menu->icon = 'si-settings';
    	$menu->save();

          //create SUBMENU master
    	$permission = Permission::firstOrNew(array(
    		'name'=>'read-user',
    	));
    	$permission->display_name = 'Read Users';
    	$permission->save();

    	$submenu = Menu::firstOrNew(array(
    		'name'=>'Manajemen Pengguna',
    		'parent_id'=>$menu->id,
    		'permission_id'=>$permission->id,
    		'ordinal'=>2,
    		'parent_status'=>'N',
    		'url'=>'user',
    	)
    );
    	$submenu->save();

    // 	$permission = Permission::firstOrNew(array(
    // 		'name'=>'read-permission',
    // 	));
    // 	$permission->display_name = 'Read Permissions';
    // 	$permission->save();

    // 	$submenu = Menu::firstOrNew(array(
    // 		'name'=>'Manajemen Permissions',
    // 		'parent_id'=>$menu->id,
    // 		'permission_id'=>$permission->id,
    // 		'ordinal'=>2,
    // 		'parent_status'=>'N',
    // 		'url'=>'permission',
    // 	)
    // );
    // 	$submenu->save();


    // 	$permission = Permission::firstOrNew(array(
    // 		'name' => 'read-role',
    // 	));
    // 	$permission->display_name = 'Read Roles';
    // 	$permission->save();

    // 	$submenu = Menu::firstOrNew(array(
    // 		'name' => 'Manajemen Roles',
    // 		'parent_id' => $menu->id,
    // 		'permission_id' => $permission->id,
    // 		'ordinal' => 2,
    // 		'parent_status' => 'N',
    // 		'url' => 'role',
    // 	)
    // );
    // 	$submenu->save();
    }

    private function menuActivity()
    {
        $this->command->info('Menu Activity Seeder');
        $permission = Permission::firstOrNew(array(
            'name'=>'read-activity'
        ));
        $permission->display_name = 'Read Activity Menus';
        $permission->save();
        $menu = Menu::firstOrNew(array(
            'name'=>'Riwayat Pengguna',
            'permission_id'=>$permission->id,
            'ordinal'=>1,
            'parent_status'=>'N',
            'url'=>'activity-log',
        ));
        $menu->icon = 'si-refresh';
        $menu->save();
    }

    private function menuMaster(){
        $this->command->info('Menu Master Seeder');
        $permission = Permission::firstOrNew(array(
            'name'=>'read-master-menu'
        ));
        $permission->display_name = 'Read Master Menus';
        $permission->save();
        $menu = Menu::firstOrNew(array(
            'name'=>'Master',
            'permission_id'=>$permission->id,
            'ordinal'=>1,
            'parent_status'=>'Y'
        ));
        $menu->icon = 'si-wrench';
        $menu->save();

          //create SUBMENU master
        $permission = Permission::firstOrNew(array(
            'name'=>'read-master-umum-menu',
        ));
        $permission->display_name = 'Read Master Umum Menu';
        $permission->save();

        $submenu = Menu::firstOrNew(array(
            'name'=>'Master Data Umum',
            'parent_id'=>$menu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>2,
            'parent_status'=>'Y',
        )
    );
        $submenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-agama',
        ));
        $permission->display_name = 'Read Agama Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Agama',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'agama',
        )
    );
        $subsubmenu->save();

             //create SUBMENU master
        $permission = Permission::firstOrNew(array(
            'name'=>'read-provinsi',
        ));
        $permission->display_name = 'Read Provinsi Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Provinsi',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'provinsi',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-kabupaten',
        ));
        $permission->display_name = 'Read Kabupaten Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Kabupaten',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'kabupaten',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-kecamatan',
        ));
        $permission->display_name = 'Read Kecamatan Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Kecamatan',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'kecamatan',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-kelurahan',
        ));
        $permission->display_name = 'Read Kelurahan Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Kelurahan',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'kelurahan',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-jenis-kelamin',
        ));
        $permission->display_name = 'Read Jenis Kelamin Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Jenis Kelamin',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'jenis-kelamin',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-status-perkawinan',
        ));
        $permission->display_name = 'Read Status Perkawinan Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Status Perkawinan',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'status-perkawinan',
        )
    );
        $subsubmenu->save();


        $permission = Permission::firstOrNew(array(
            'name'=>'read-master-kepegawaian-menu',
        ));
        $permission->display_name = 'Read Master Kepegawaian Menu';
        $permission->save();

        $submenu = Menu::firstOrNew(array(
            'name'=>'Master Data Kepegawaian',
            'parent_id'=>$menu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>2,
            'parent_status'=>'Y',
        )
    );
        $submenu->save();

         $permission = Permission::firstOrNew(array(
            'name'=>'read-departement',
        ));
        $permission->display_name = 'Read Departement Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Departement',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'departement',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-golongan',
        ));
        $permission->display_name = 'Read Golongan Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Golongan',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'golongan',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-jabatan',
        ));
        $permission->display_name = 'Read Jabatan Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Jabatan',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'jabatan',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-master-kas-menu',
        ));
        $permission->display_name = 'Read Master Kas Menu';
        $permission->save();

        $submenu = Menu::firstOrNew(array(
            'name'=>'Master Data Kas',
            'parent_id'=>$menu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>2,
            'parent_status'=>'Y',
        )
    );
        $submenu->save();

         $permission = Permission::firstOrNew(array(
            'name'=>'read-kategori-transaksi',
        ));
        $permission->display_name = 'Read Kategori Transaksi Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Kategori Transaksi',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'kategori-transaksi',
        )
    );
        $subsubmenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-jenis-transaksi',
        ));
        $permission->display_name = 'Read Jenis Transaksi Menu';
        $permission->save();

        $subsubmenu = Menu::firstOrNew(array(
            'name'=>'Jenis Transaksi',
            'parent_id'=>$submenu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>3,
            'parent_status'=>'N',
            'url'=>'jenis-transaksi',
        )
    );
        $subsubmenu->save();

    }

     private function menuPegawai()
    {
        $this->command->info('Menu Pegawai Seeder');
        $permission = Permission::firstOrNew(array(
            'name'=>'read-pegawai'
        ));
        $permission->display_name = 'Read Pegawai';
        $permission->save();
        $menu = Menu::firstOrNew(array(
            'name'=>'Kepegawaian',
            'permission_id'=>$permission->id,
            'ordinal'=>1,
            'parent_status'=>'N',
            'url'=>'pegawai',
        ));
        $menu->icon = 'si-user';
        $menu->save();
    }

    private function menuAnggota()
    {
        $this->command->info('Menu Anggota Seeder');
        $permission = Permission::firstOrNew(array(
            'name'=>'read-anggota'
        ));
        $permission->display_name = 'Read Anggota';
        $permission->save();
        $menu = Menu::firstOrNew(array(
            'name'=>'Anggota Koperasi',
            'permission_id'=>$permission->id,
            'ordinal'=>1,
            'parent_status'=>'N',
            'url'=>'anggota',
        ));
        $menu->icon = 'si-users';
        $menu->save();
    }

    private function menuTransaksi(){
        $this->command->info('Menu Transaksi Seeder');
        $permission = Permission::firstOrNew(array(
            'name'=>'read-transaksi-menu'
        ));
        $permission->display_name = 'Read Transaksi Menus';
        $permission->save();
        $menu = Menu::firstOrNew(array(
            'name'=>'Transaksi Penjualan',
            'permission_id'=>$permission->id,
            'ordinal'=>1,
            'parent_status'=>'Y'
        ));
        $menu->icon = 'si-wallet';
        $menu->save();

          //create SUBMENU master
        $permission = Permission::firstOrNew(array(
            'name'=>'read-transaksi',
        ));
        $permission->display_name = 'Read Transaksi';
        $permission->save();

        $submenu = Menu::firstOrNew(array(
            'name'=>'Rekap Transaksi',
            'parent_id'=>$menu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>2,
            'parent_status'=>'N',
            'url'=>'transaksi',
        )
    );
        $submenu->save();

        $permission = Permission::firstOrNew(array(
            'name'=>'read-laporan',
        ));
        $permission->display_name = 'Read Laporan';
        $permission->save();

        $submenu = Menu::firstOrNew(array(
            'name'=>'Laporan Transaksi',
            'parent_id'=>$menu->id,
            'permission_id'=>$permission->id,
            'ordinal'=>2,
            'parent_status'=>'N',
            'url'=>'laporan',
        )
    );
        $submenu->save();

    }
}
