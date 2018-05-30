<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2018/5/22
 * Time: 下午3:26
 */

namespace app\home\controllers;

use App\Home\Models\Merchant;
use App\Home\Validation\ValidationMerchant;

class MerchantController extends BaseController
{
    private static $merchantModel;

    public function __construct()
    {
        parent::__construct();
        self::$merchantModel = new Merchant();
    }

    /**
     * 新建商户
     */
    public function createMerchant(){
        // 检测参数
        $rule = [
            'manager_id'        => $this->currManagerId,
            'merchant_name'     => getHttpParam('merchant_name'),
        ];
        $check      = new ValidationMerchant($rule);
        $isSuccess  = $check->validate('createMerchant');
        if(!$isSuccess) sendJson(false,10001,$check->getError());

        $merchantName   = $rule['merchant_name'];
        $tel            = getHttpParam('tel');

        // 检测当前商户是否存在
        $info = self::$merchantModel->getMerchantInfo('ID',['NAME' => $merchantName]);
        if($info) sendJson(false,40901);

        // 获取ID最大值
        $maxId = self::$merchantModel->getMerchantInfo('(MAX(ID)+1) maxId');
        if(isset($maxId['maxId']) && $maxId['maxId'] ){
            $merchantId = getNumberCharIncreaseId($maxId['maxId']);
        }else{
            $merchantId = '0001';
        }

        // 插入数据
        $data = [
            'ID'    => $merchantId,
            'NAME'  => $merchantName,
            'AREA'  => '01',
            'LOCAL' => '1',
            'TEL'   => $tel,
        ];
        $isSuccess = self::$merchantModel->createMerchant($data);
        if($isSuccess){
            sendJson(true,40902);
        }else{
            sendJson(false,40903);
        }

    }

    /**
     * 获取商户列表
     */
    public function getMerchantList(){
        $where = ['AREA' => '01'];
        // 关键字
        if($keywords = getHttpParam('keywords')){
            $where[] = ['ID','=',$keywords];
            $where[] = ['NAME','like',"%{$keywords}%",'or'];
        }

        $order  = 'ID ASC';
        $num    = self::$merchantModel->getMerchantCount($where);
        $list   = self::$merchantModel->getMerchantList('*',$where,$order);

        sendJson(true,10000,'',['total_num' => $num,'list' => $list]);
    }
}