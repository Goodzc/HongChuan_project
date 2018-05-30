<?php

/**
 * 登录表单验证
 * Created by PhpStorm.
 * User: qzc
 * Date: 2017/12/11
 * Time: 下午4:41
 */
namespace App\Home\Validation;
use Validation\Validation;

class ValidationLogin extends Validation
{
    /**
     * 登录
     */
    protected $loginIn = [
        ['account','require','账户名不能为空'],
        ['password','require','密码不能为空'],
        ['password','length_between','密码长度6-16位',1,16],
    ];
}