<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/6
 * Time: 下午4:01
 */
/**
 * 自动加载类
 * @param $className
 */
function __autoload($className){
    $dirArr = explode('\\',$className);
    $class  = array_pop($dirArr);
    $file   = __DIR__ . '/../' . strtolower(implode('/',$dirArr)) . '/' . $class . '.php';

    if (is_file($file)) {
        require_once $file;
    }
}