<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2018/5/25
 * Time: 下午3:02
 */

namespace app\home\models;


use DB\Mysql;

class uploadServer extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取出库记录
     * @param array $where
     * @param string $order
     * @return mixed
     */
    public function getCpkOutSheet($where = [],$order = 'OUT_TIME asc')
    {
        $data = $this->field('*')
            ->where($where)
            ->order($order)
            ->select('cpk_out_sheet');
        return $data;
    }

    /**
     * 获取出库商品种类
     * @param array $where
     * @return mixed
     */
    public function getCpkOutData($where = [])
    {
        $data = $this->field('*')
            ->where($where)
            ->select('cpk_out_data');
        return $data;
    }

    /**
     * 获取出库商品种类
     * @param array $where
     * @return mixed
     */
    public function getCpkOutCode($where = [])
    {
        $data = $this->field('*')
            ->where($where)
            ->select('cpk_wlm');
        return $data;
    }
}