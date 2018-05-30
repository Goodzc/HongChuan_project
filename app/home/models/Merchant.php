<?php

/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/5
 * Time: 下午6:06
 */
namespace App\Home\Models;
use DB\Mysql;
class Merchant extends Mysql
{
    public function __construct(){
        parent::__construct();
    }

    /**
     * 创建商户
     * @param $data
     * @return int
     */
    public function createMerchant($data){
        return $this->insert('code_dealer',$data);
    }

    /**
     * 获取商户信息
     * @param string $field
     * @param array $where
     * @return mixed
     */
    public function getMerchantInfo($field = '*',$where = [])
    {
        $data = $this->field($field)
            ->where($where)
            ->find('code_dealer');
        return $data;
    }

    /**
     * 获取商户列表
     * @param string $field
     * @param array $where
     * @param string $order
     * @return array
     */
    public function getMerchantList($field = '*',$where = [],$order = 'ID ASC')
    {
        $data = $this->field($field)
            ->where($where)
            ->order($order)
            ->select('code_dealer');
        return $data;
    }

    /**
     * 获取商户数量
     * @param array $where
     * @return mixed
     */
    public function getMerchantCount($where = []){
        return $this->where($where)->count('code_dealer');
    }
}