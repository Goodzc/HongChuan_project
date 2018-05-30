<?php
/**
 * 上传服务器
 * Created by PhpStorm.
 * User: qzc
 * Date: 2018/5/25
 * Time: 下午2:52
 */

namespace app\home\controllers;


use app\home\models\uploadServer;
use App\Home\Validation\ValidationUploadServer;
use DB\Mysql;

class uploadServerController extends BaseController
{
    protected static $uploadServer;
    public function __construct()
    {
        parent::__construct();
        self::$uploadServer = new uploadServer();
    }

    public function uploadStream(){
        // 检测参数
        $rule = [
            'manager_id'        => $this->currManagerId
        ];
        $check      = new ValidationUploadServer($rule);
        $isSuccess  = $check->validate('uploadStream');
        if(!$isSuccess) sendJson(false,10001,$check->getError());

        // 获取未上传的数据
        $data = $this->getNotUploadData();
        if($data) sendJson(false,40301);

        // 连接线上数据库
        $dbConf = [
            'host'      => '127.0.0.1',
            'port'      => '3306',
            'user'      => 'wlywl',
            'pass'      => 'wlywl',
            'dbName'    => 'wlywl',
            'tbPrefix'  => '',
        ];
        $mysqlModel = new Mysql($dbConf);

    }

    /**
     * 获取未上传的数据
     * @return array
     */
    public function getNotUploadData(){
        // 获取未上传的出库单
        $list1 = self::$uploadServer->getCpkOutSheet(['UPLOAD_TIME' => 0]);
        if(!$list1) return [];

        // 获取未上传的出库商品种类
        $sheetId = array_column($list1,'ID');
        $where = [
            ['SHEET_ID','in',implode("','",$sheetId)],
        ];
        $list2 = self::$uploadServer->getCpkOutData($where);

        // 获取未上传的出库箱码
        $list3 = self::$uploadServer->getCpkOutCode($where);

        return [
            'list1' => $list1,
            'list2' => $list2,
            'list3' => $list3,
        ];
    }
}