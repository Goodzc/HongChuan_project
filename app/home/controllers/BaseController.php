<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/11
 * Time: 下午5:47
 */

namespace app\home\controllers;

use App\Controller;
use App\Home\Models\Login;
use Vendor\Redis\RedisExtension;

class BaseController extends Controller
{
    // 分页大小
    public $pageSize = 10;

    // 当前管理员ID
    public $currManagerId;

    // 当前管理员信息
    public $currManagerInfo;

    // redis缓存
    public static $redis;

    // 管理员redis缓存健值
    public static $managerRedisKey;

    // 字符串分隔符
    public static $strSeparateMark = '|@|';

    public function __construct()
    {
        parent::__construct();

        self::$redis = new RedisExtension();

        // 获取管理员ID
        if($this->currManagerId = getHttpParam('manager_id')){
            // 获取管理员信息
            self::$managerRedisKey = 'MANAGER_INFO'.$this->currManagerId;
            $managerInfo = json_decode(self::$redis->get(self::$managerRedisKey),true);
            if(!$managerInfo){
                $login = new Login();
                $managerInfo = $login->getManagerInfo("m.*,cpk.NAME as cpk_name",['m.id' => $this->currManagerId]);
                self::$redis->set(self::$managerRedisKey,json_encode($managerInfo));
            }
            $this->currManagerInfo  = $managerInfo;
        }
    }
}