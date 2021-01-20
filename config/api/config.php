<?php
return [
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    'template'  =>  [
        'layout_on'     =>  true,
        'layout_name'   =>  'layout',
    ],
    //输出替换
    'view_replace_str'  =>  [
        '__PUBLIC__'=>'/uploads',
        '__QRCODE__'=>'/qrcode',
        '__PUBLICS__'=>'/uploadsb',
        '__IMG__'=>'/image',

    ],
    'IMG_PATH' => 'http://static.shop.com/',

];