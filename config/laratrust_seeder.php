<?php

return [
    'role_structure' => [
        'developer' => [
            'acl'                           => 'c,r,u,d',
            'acl-menu'                      => 'r',
                'user'                      => 'r',
                'permission'                => 'r',
                'role'                      => 'r',
            'home-menu'                     => 'r',
            'activity'                      => 'r',
            'transaksi-menu'                => 'r',
                'transaksi'                 => 'r',
                'laporan'                   => 'r',
                'grafik'                    => 'r',
                'peramalan'                 => 'r',
            'recycle-bin-menu'              => 'r',
                'recycle-bin'               => 'r',
        ],
        'superadministrator' => [
            'acl'                           => 'c,r,u,d',
            'acl-menu'                      => 'r',
                'user'                      => 'r',
                'permission'                => 'r',
                'role'                      => 'r',
            'home-menu'                     => 'r',
            'activity'                      => 'r',
            'transaksi-menu'                => 'r',
                'transaksi'                 => 'r',
                'laporan'                   => 'r',
                'grafik'                    => 'r',
                'peramalan'                 => 'r',
            'recycle-bin-menu'              => 'r',
                'recycle-bin'               => 'r',
        ],
        // 'administrator' => [
        //     'users' => 'c,r,u,d',
        //     'profile' => 'r,u',
        //     'home-menu' => 'r'
        // ],
        'manajer' => [
            'profile' => 'r,u',
            'transaksi-menu'                => 'r',
                'laporan'                   => 'r',
                'grafik'                    => 'r',
            // 'home-menu' => 'r'
        ],
        'front_office' => [
            'profile' => 'r,u',
            'transaksi-menu'                => 'r',
                'transaksi'                 => 'r',
                'peramalan'                 => 'r',
        ],
    ],
    'permission_structure' => [
        // 'cru_user' => [
        //     'profile' => 'c,r,u'
        // ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
