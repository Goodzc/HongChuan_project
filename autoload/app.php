<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/5
 * Time: 下午5:21
 */

if(!isset($_SESSION)) session_start(); // 开启session
date_default_timezone_set('Asia/Shanghai');// 设置时区

// 自动加载类
require_once __DIR__.'/./autoload.php';

// 公共方法
require_once __DIR__.'/../common/common.php';

// 公共配置
$systemConf       = include_once __DIR__ . '/../common/conf.php';
$GLOBALS['CONF']  = $systemConf;

// 过滤数据
filterData();

// 路由分发
$module     = isset($_GET['m']) && !empty($_GET['m']) ? $_GET['m'] : $systemConf['DEFAULT_MODULE'];         // 模块
$controller = isset($_GET['c']) && !empty($_GET['c']) ? $_GET['c'] : $systemConf['DEFAULT_CONTROLLER'];     // 控制器
$action     = isset($_GET['a']) && !empty($_GET['a']) ? $_GET['a'] : $systemConf['DEFAULT_ACTION'];         // 方法
$namespace  = 'App\\'.ucwords($module).'\\Controllers\\'.ucwords($controller).'Controller';
if(class_exists($namespace)){
    $model      = new $namespace();
    if(!method_exists($model,$action)) die('The Method ' . $action . ' Is Not Exist In Class ' .$namespace);
    $model      ->$action();
}else{
    die('Class ' . $namespace . ' Is Not Exist');
}

