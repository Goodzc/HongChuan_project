<?php

/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/5
 * Time: 下午6:06
 */
namespace App\Home\Models;
use DB\Mysql;
class Login extends Mysql
{
    public function __construct(){
        parent::__construct();
    }

    /**
     * 获取管理员信息
     * @param string $field
     * @param array $where
     * @return mixed
     */
    public function getManagerInfo($field = 'm.*',$where = [])
    {
        $data = $this->field($field)
            ->join([
                "left join {$this->_tbPrefix}code_cpk as cpk on cpk.ID = m.cpk_id"
            ])
            ->where($where)
            ->find('manager as m');
        return $data;
    }

    /**
     * 更新用户绑定信息
     * @param $uid 用户ID
     * @param array $data 要更新的字段
     * @return mixed
     */
    public function updateUserBindInfo($uid, $data = [])
    {
        $isSuccess = $this->where([ 'user_id' => $uid])->update('user_bind',$data);
        if ($isSuccess === false) {
            return false;
        } else {
            return true;
        }
    }
}