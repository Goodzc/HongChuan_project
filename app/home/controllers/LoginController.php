<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2018/5/22
 * Time: 下午3:26
 */

namespace app\home\controllers;


use App\Home\Models\Login;
use App\Home\Validation\ValidationLogin;

class LoginController extends BaseController
{
    private static $loginModel;

    public function __construct()
    {
        parent::__construct();
        self::$loginModel = new Login();
    }

    /**
     * 登录
     */
    public function loginIn(){
        // 检测参数
        $rule = [
            'account'    => getHttpParam('account'),
            'password'   => getHttpParam('password'),
        ];
        $check      = new ValidationLogin($rule);
        $isSuccess  = $check->validate('loginIn');
        if(!$isSuccess) sendJson(false,10001,$check->getError());

        $account    = $rule['account'];
        $password   = md5(md5($rule['password']));

        // 检测当前账户是否存在
        $info = self::$loginModel->getManagerInfo('m.*,cpk.NAME as cpk_name',['m.manager_account' => $account]);
        if(!$info) sendJson(false,40101);

        // 检测密码是否正确
        if($info['manager_pass'] != $password) sendJson(false,40102);

        unset($info['manager_pass']);
        sendJson(true,40103,'',$info);
    }
}