<?php
/**
 * 数据库配置
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/12
 * Time: 下午2:45
 */

$dbConf = [
    // 主库
    [
        'host'      => '127.0.0.1',
        'port'      => '3306',
        'user'      => 'root',
        'pass'      => 'root',
        'dbName'    => 'zgzl_oa',
        'tbPrefix'  => 'zgzl_',
    ],
    // 从库
//    [
//        'host'      => '127.0.0.1',
//        'port'      => '3306',
//        'user'      => 'root',
//        'pass'      => 'root',
//        'dbName'    => 'zgzl_oa',
//        'tbPrefix'  => 'zgzl_',
//    ],
];
return $dbConf;