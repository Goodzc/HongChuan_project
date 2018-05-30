<?php
/**
 * 控制器基类
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/14
 * Time: 下午3:33
 */

namespace App;


class Controller
{
    // 当前时间戳
    public $currTime;

    // 日期格式
    public static $dateFormat  = 'Y-m-d';
    public static $dateTimeFormat  = 'Y-m-d H:i';

    // 字符串分隔符
    public static $strSeparateMark = '|@|';

    // html根目录
    public $htmlBaseDir = __DIR__ . '/../public/';

    public function __construct()
    {
        date_default_timezone_set('Asia/Shanghai');// 设置时区
        $this->currTime = time();
    }

    /**
     * 显示页面
     * @param string $htmlUrl
     * @param array $data
     */
    public function display($htmlUrl = '',$data = []){
        foreach($data as $k => $v){
            $$k = $v;
        }
        include_once $this->htmlBaseDir . $htmlUrl;exit;
    }
}